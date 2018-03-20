<?php

namespace Iliich246\YicmsCommon\Controllers;

use Yii;
use yii\base\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\Base\DevFilter;
use Iliich246\YicmsCommon\Base\CommonHashForm;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Conditions\ConditionValues;
use Iliich246\YicmsCommon\Conditions\ConditionTemplate;
use Iliich246\YicmsCommon\Conditions\DevConditionsGroup;
use Iliich246\YicmsCommon\Conditions\ConditionValueNamesForm;
use Iliich246\YicmsCommon\Conditions\ConditionsDevModalWidget;

/**
 * Class DeveloperConditionsController
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DeveloperConditionsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            'root' => [
//                'class' => DevFilter::className(),
//                'except' => ['change-field-editable'],
//            ],
        ];
    }

    /**
     * Action for refresh dev conditions modal window
     * @param $conditionTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws \Exception
     */
    public function actionLoadModal($conditionTemplateId)
    {
        if (Yii::$app->request->isPjax &&
            Yii::$app->request->post('_pjax') == '#'.ConditionsDevModalWidget::getPjaxContainerId())
        {
            $devConditionsGroup = new DevConditionsGroup();
            $devConditionsGroup->initialize($conditionTemplateId);

            return ConditionsDevModalWidget::widget([
                'devConditionsGroup' => $devConditionsGroup,
            ]);
        }

        throw new BadRequestHttpException();
    }

    /**
     * Action for send empty condition modal window
     * @param $conditionTemplateReference
     * @return string
     * @throws BadRequestHttpException
     * @throws \Exception
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function actionEmptyModal($conditionTemplateReference)
    {
        if (Yii::$app->request->isPjax &&
            Yii::$app->request->post('_pjax') == '#'.ConditionsDevModalWidget::getPjaxContainerId())
        {
            $devConditionsGroup = new DevConditionsGroup();
            $devConditionsGroup->setConditionsTemplateReference($conditionTemplateReference);
            $devConditionsGroup->initialize();

            return ConditionsDevModalWidget::widget([
                'devConditionsGroup' => $devConditionsGroup,
            ]);
        }

        throw new BadRequestHttpException();
    }

    /**
     * Action for update conditions templates list container
     * @param $conditionTemplateReference
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionUpdateConditionsListContainer($conditionTemplateReference)
    {
        if (Yii::$app->request->isPjax &&
            Yii::$app->request->post('_pjax') == '#update-conditions-list-container') {

            $conditionTemplates = ConditionTemplate::getListQuery($conditionTemplateReference)
                ->orderBy([ConditionTemplate::getOrderFieldName() => SORT_ASC])
                ->all();

            return $this->render('/pjax/update-conditions-list-container', [
                'conditionTemplateReference' => $conditionTemplateReference,
                'conditionsTemplates'        => $conditionTemplates,
            ]);
        }

        throw new BadRequestHttpException();
    }

    /**
     * Action for delete conditions template
     * @param $conditionTemplateId
     * @param bool|false $deletePass
     * @return string
     * @throws BadRequestHttpException
     * @throws CommonException
     * @throws NotFoundHttpException
     */
    public function actionDeleteConditionsBlockTemplate($conditionTemplateId, $deletePass = false)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var ConditionTemplate $conditionTemplate */
        $conditionTemplate = ConditionTemplate::findOne($conditionTemplateId);

        if (!$conditionTemplate) throw new NotFoundHttpException('Wrong conditionTemplateId');

        if ($conditionTemplate->isConstraints())
            if (!Yii::$app->security->validatePassword($deletePass, CommonHashForm::DEV_HASH))
                throw new CommonException('Wrong dev password');

        $conditionTemplateReference = $conditionTemplate->condition_template_reference;

        $conditionTemplate->delete();

        $conditionTemplates = ConditionTemplate::getListQuery($conditionTemplateReference)
            ->orderBy([ConditionTemplate::getOrderFieldName() => SORT_ASC])
            ->all();

        return $this->render('/pjax/update-conditions-list-container', [
            'conditionTemplateReference' => $conditionTemplateReference,
            'conditionsTemplates'        => $conditionTemplates,
        ]);
    }

    /**
     * Action for up condition template order
     * @param $conditionTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionConditionTemplateUpOrder($conditionTemplateId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var ConditionTemplate $conditionTemplate */
        $conditionTemplate = ConditionTemplate::findOne($conditionTemplateId);

        if (!$conditionTemplate) throw new NotFoundHttpException('Wrong conditionTemplateId');

        $conditionTemplateReference = $conditionTemplate->condition_template_reference;

        $conditionTemplate->upOrder();

        $conditionTemplates = ConditionTemplate::getListQuery($conditionTemplateReference)
            ->orderBy([ConditionTemplate::getOrderFieldName() => SORT_ASC])
            ->all();

        return $this->render('/pjax/update-conditions-list-container', [
            'conditionTemplateReference' => $conditionTemplateReference,
            'conditionsTemplates'        => $conditionTemplates,
        ]);
    }

    /**
     * Action for down condition template order
     * @param $conditionTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionConditionTemplateDownOrder($conditionTemplateId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var ConditionTemplate $conditionTemplate */
        $conditionTemplate = ConditionTemplate::findOne($conditionTemplateId);

        if (!$conditionTemplate) throw new NotFoundHttpException('Wrong conditionTemplateId');

        $conditionTemplateReference = $conditionTemplate->condition_template_reference;

        $conditionTemplate->downOrder();

        $conditionTemplates = ConditionTemplate::getListQuery($conditionTemplateReference)
            ->orderBy([ConditionTemplate::getOrderFieldName() => SORT_ASC])
            ->all();

        return $this->render('/pjax/update-conditions-list-container', [
            'conditionTemplateReference' => $conditionTemplateReference,
            'conditionsTemplates'        => $conditionTemplates,
        ]);
    }

    /**
     * Returns list of condition values
     * @param $conditionTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionConditionValuesList($conditionTemplateId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var ConditionTemplate $conditionTemplate */
        $conditionTemplate = ConditionTemplate::findOne($conditionTemplateId);

        if (!$conditionTemplate) throw new NotFoundHttpException('Wrong conditionTemplateId');

        $conditionValues = ConditionValues::find()->where([
            'common_condition_template_id' => $conditionTemplate->id,
        ])->orderBy([
            'condition_value_order' => SORT_ASC
        ])->all();

        return $this->renderAjax('/pjax/conditions-value-list-container', [
            'conditionTemplate' => $conditionTemplate,
            'conditionValues'   => $conditionValues
        ]);
    }

    /**
     * @param $conditionTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionCreateConditionValue($conditionTemplateId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var ConditionTemplate $conditionTemplate */
        $conditionTemplate = ConditionTemplate::findOne($conditionTemplateId);

        if (!$conditionTemplate) throw new NotFoundHttpException('Wrong conditionTemplateId');

        $conditionValue = new ConditionValues();
        $conditionValue->scenario = ConditionValues::SCENARIO_CREATE;
        $conditionValue->setConditionTemplate($conditionTemplate);

        $languages = Language::getInstance()->usedLanguages();

        $conditionValuesTranslates = [];

        foreach($languages as $key => $language) {

            $conditionValuesTranslate = new ConditionValueNamesForm();
            $conditionValuesTranslate->scenario = ConditionValueNamesForm::SCENARIO_CREATE;
            $conditionValuesTranslate->setLanguage($language);
            $conditionValuesTranslate->setConditionValues($conditionValue);

            $conditionValuesTranslates[$key] = $conditionValuesTranslate;
        }

        if ($conditionValue->load(Yii::$app->request->post()) &&
            Model::loadMultiple($conditionValuesTranslates, Yii::$app->request->post())) {

            if ($conditionValue->validate() && Model::validateMultiple($conditionValuesTranslates)) {

                $conditionValue->save();

                /** @var ConditionValueNamesForm $conditionValuesTranslate */
                foreach ($conditionValuesTranslates as $conditionValuesTranslate) {
                    $conditionValuesTranslate->save();
                }

                if (Yii::$app->request->post('_saveAndBack'))
                    $returnBack = true;
                else
                    $returnBack = false;

                return $this->renderAjax('/pjax/create-update-condition-value', [
                    'conditionTemplate'         => $conditionTemplate,
                    'conditionValue'            => $conditionValue,
                    'conditionValuesTranslates' => $conditionValuesTranslates,
                    'redirectUpdate'            => true,
                    'returnBack'                => $returnBack
                ]);
            }
        }

        return $this->renderAjax('/pjax/create-update-condition-value', [
            'conditionTemplate'         => $conditionTemplate,
            'conditionValue'            => $conditionValue,
            'conditionValuesTranslates' => $conditionValuesTranslates
        ]);
    }

    /**
     * @param $conditionValueId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdateConditionValue($conditionValueId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var ConditionValues $conditionValue */
        $conditionValue = ConditionValues::findOne($conditionValueId);

        if (!$conditionValue) throw new NotFoundHttpException('Wrong conditionValueId');

        $conditionTemplate = $conditionValue->getConditionTemplate();

        $conditionValue->scenario = ConditionValues::SCENARIO_UPDATE;
        $conditionValue->setConditionTemplate($conditionTemplate);

        $languages = Language::getInstance()->usedLanguages();

        $conditionValuesTranslates = [];

        foreach($languages as $key => $language) {

            $conditionValuesTranslate = new ConditionValueNamesForm();
            $conditionValuesTranslate->scenario = ConditionValueNamesForm::SCENARIO_UPDATE;
            $conditionValuesTranslate->setLanguage($language);
            $conditionValuesTranslate->setConditionValues($conditionValue);
            $conditionValuesTranslate->loadFromDb();

            $conditionValuesTranslates[$key] = $conditionValuesTranslate;
        }

        if ($conditionValue->load(Yii::$app->request->post()) &&
            Model::loadMultiple($conditionValuesTranslates, Yii::$app->request->post())) {

            if ($conditionValue->validate() && Model::validateMultiple($conditionValuesTranslates)) {

                $conditionValue->save();

                /** @var ConditionValueNamesForm $conditionValuesTranslate */
                foreach ($conditionValuesTranslates as $conditionValuesTranslate) {
                    $conditionValuesTranslate->save();
                }

                if (Yii::$app->request->post('_saveAndBack'))
                    $returnBack = true;
                else
                    $returnBack = false;

                return $this->renderAjax('/pjax/create-update-condition-value', [
                    'conditionTemplate'         => $conditionTemplate,
                    'conditionValue'            => $conditionValue,
                    'conditionValuesTranslates' => $conditionValuesTranslates,
                    'returnBack'                => $returnBack
                ]);
            }
        }

        return $this->renderAjax('/pjax/create-update-condition-value', [
            'conditionTemplate' => $conditionTemplate,
            'conditionValue'            => $conditionValue,
            'conditionValuesTranslates' => $conditionValuesTranslates,
        ]);
    }

    /**
     * Delete selected condition value
     * @param $conditionValueId
     * @param bool|false $deletePass
     * @return string
     * @throws BadRequestHttpException
     * @throws CommonException
     * @throws NotFoundHttpException
     */
    public function actionDeleteConditionValue($conditionValueId, $deletePass = false)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var ConditionValues $conditionValue */
        $conditionValue = ConditionValues::findOne($conditionValueId);

        if (!$conditionValue) throw new NotFoundHttpException('Wrong conditionValueId');

        if ($conditionValue->isConstraints())
            if (!Yii::$app->security->validatePassword($deletePass, CommonHashForm::DEV_HASH))
                throw new CommonException('Wrong dev password');

        $conditionTemplate = $conditionValue->getConditionTemplate();

        $conditionValues = ConditionValues::find()->where([
            'common_condition_template_id' => $conditionTemplate->id,
        ])->orderBy([
            'condition_value_order' => SORT_ASC
        ])->all();

        return $this->renderAjax('/pjax/conditions-value-list-container', [
            'conditionTemplate' => $conditionTemplate,
            'conditionValues'   => $conditionValues
        ]);

    }

    /**
     * Up condition value order
     * @param $conditionValueId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionConditionValueUpOrder($conditionValueId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var ConditionValues $conditionValue */
        $conditionValue = ConditionValues::findOne($conditionValueId);

        if (!$conditionValue) throw new NotFoundHttpException('Wrong conditionValueId');

        $conditionTemplate = $conditionValue->getConditionTemplate();

        $conditionValue->upOrder();

        $conditionValues = ConditionValues::find()->where([
            'common_condition_template_id' => $conditionTemplate->id,
        ])->orderBy([
            'condition_value_order' => SORT_ASC
        ])->all();

        return $this->renderAjax('/pjax/conditions-value-list-container', [
            'conditionTemplate' => $conditionTemplate,
            'conditionValues'   => $conditionValues
        ]);
    }

    /**
     * Down condition value order
     * @param $conditionValueId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionConditionValueDownOrder($conditionValueId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var ConditionValues $conditionValue */
        $conditionValue = ConditionValues::findOne($conditionValueId);

        if (!$conditionValue) throw new NotFoundHttpException('Wrong conditionValueId');

        $conditionTemplate = $conditionValue->getConditionTemplate();

        $conditionValue->downOrder();

        $conditionValues = ConditionValues::find()->where([
            'common_condition_template_id' => $conditionTemplate->id,
        ])->orderBy([
            'condition_value_order' => SORT_ASC
        ])->all();

        return $this->renderAjax('/pjax/conditions-value-list-container', [
            'conditionTemplate' => $conditionTemplate,
            'conditionValues'   => $conditionValues
        ]);
    }
}
