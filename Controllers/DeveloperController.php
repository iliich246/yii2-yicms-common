<?php

namespace Iliich246\YicmsCommon\Controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\Base\DevFilter;
use Iliich246\YicmsCommon\Base\CommonUser;
use Iliich246\YicmsCommon\Base\CommonHashForm;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use Iliich246\YicmsCommon\Languages\DefaultLanguageForm;
use Iliich246\YicmsCommon\Widgets\ReloadAlertWidget;
use Iliich246\YicmsCommon\Fields\DevFieldsGroup;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\FreeEssences\FreeEssences;
use Iliich246\YicmsCommon\Widgets\FieldsDevInputWidget;

/**
 * Class DeveloperController
 *
 * Controller for developer section in common module
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DeveloperController extends Controller
{
    /** @inheritdoc */
    public $layout = '@yicms-common/Views/layouts/developer';
    /** @inheritdoc */
    public $defaultAction = 'languages-list';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            'root' => [
//                'class' => DevFilter::className(),
//                'except' => ['login-as-root'],
//            ],
        ];
    }

    /**
     * Return list of languages and some language configs
     * @return string
     * @throws CommonException
     */
    public function actionLanguagesList()
    {
        $defaultLanguageForm = new DefaultLanguageForm();

        if ($defaultLanguageForm->load(Yii::$app->request->post()) && $defaultLanguageForm->validate()) {
            if ($defaultLanguageForm->save())
                return $this->render('/developer/languages_list', [
                    'defaultLanguageModel' => $defaultLanguageForm,
                    'success' => true,
                ]);
            else
                throw new CommonException('Can`t save data in database');
        }

        $languages = LanguagesDb::find()->all();

        return $this->render('/developer/languages_list', [
            'pjaxError' => false,
            'languages' => $languages,
            'defaultLanguageModel' => $defaultLanguageForm
        ]);
    }

    /**
     * Creates new system language
     * @return string|\yii\web\Response
     * @throws CommonException
     */
    public function actionCreateLanguage()
    {
        $model = new LanguagesDb();
        $model->scenario = LanguagesDb::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                ReloadAlertWidget::setSuccessModal('Language created',
                    'Language successfully created');

                return $this->redirect(Url::toRoute(['languages-list'])); }
            else
                throw new CommonException('Can`t save data in database');
        }

        return $this->render('/developer/create_update_language', [
            'model' => $model,
        ]);
    }

    /**
     * Updates system language
     * @param $id
     * @return string
     * @throws BadRequestHttpException
     * @throws CommonException
     */
    public function actionUpdateLanguage($id)
    {
        /** @var LanguagesDb $model */
        $model = LanguagesDb::findOne($id);

        if (!$model)
            throw new BadRequestHttpException('No language with same id in system');

        $model->scenario = LanguagesDb::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                return $this->render('/developer/create_update_language', [
                    'model' => $model,
                    'success' => true,
                ]);
            } else
                throw new CommonException('Can`t save data in database');
        }

        Url::remember(Url::toRoute(['update-language', 'id' => $id]), 'update-language');

        return $this->render('/developer/create_update_language', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws CommonException
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionDeleteLanguage($id)
    {
        if (!Yii::$app->request->isPjax)
            return $this->redirect(Url::previous('update-language'));

        /** @var LanguagesDb $language */
        $language = LanguagesDb::findOne($id);

        if (!$language)
            return $this->redirect(Url::previous('update-language'));

        if ($language->delete()) {
            ReloadAlertWidget::setSuccessModal('Language deleted',
                'Language successfully deleted');

            return $this->redirect(Url::toRoute('languages-list'));
        }

        throw new CommonException();
    }

    /**
     * Action for login as dev
     * Need to send: <site>/common/dev/login-as-dev?hash=<hash>&asDev
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionLoginAsDev()
    {
        $commonUser = new CommonUser();

        if ($commonUser->loginAsDev())
            return $this->redirect(Url::toRoute('languages-list'));

        throw new NotFoundHttpException();
    }

    /**
     * Action for change dev hash
     * @return string|\yii\web\Response
     * @throws CommonException
     */
    public function actionChangeDevHash()
    {
        $model = new CommonHashForm();
        $model->scenario = CommonHashForm::SCENARIO_CHANGE_DEV;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->changeDev()) {
                ReloadAlertWidget::setSuccessModal('Dev hash changed',
                    'Dev hash successfully changes');

                return $this->redirect(Url::toRoute('languages-list'));
            }
            throw new CommonException('Can`t change dev hash');
        }

        return $this->render('/developer/change_hash', [
            'model' => $model
        ]);
    }

    /**
     * Action for change admin hash
     * @return string|\yii\web\Response
     * @throws CommonException
     */
    public function actionChangeAdminHash()
    {
        $model = new CommonHashForm();
        $model->scenario = CommonHashForm::SCENARIO_CHANGE_ADMIN;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->changeAdmin()) {
                ReloadAlertWidget::setSuccessModal('Admin hash changed',
                    'Admin hash successfully changes');

                return $this->redirect(Url::toRoute('languages-list'));
            }
            throw new CommonException('Can`t change admin hash');
        }

        return $this->render('/developer/change_hash', [
            'model' => $model
        ]);
    }

    /**
     * Render list of all free essences
     * @return string
     */
    public function actionFreeEssencesList()
    {
        $freeEssences = FreeEssences::find()->all();

        return $this->render('/developer/free_essences_list', [
            'freeEssences' => $freeEssences,
        ]);
    }

    /**
     * Creates new free essence
     * @return string|\yii\web\Response
     */
    public function actionCreateFreeEssence()
    {
        $freeEssence = new FreeEssences();
        $freeEssence->scenario = FreeEssences::SCENARIO_CREATE;

        if ($freeEssence->load(Yii::$app->request->post()) && $freeEssence->validate()) {

            if ($freeEssence->save()) {
                return $this->redirect(Url::toRoute(['update-free-essence', 'id' => $freeEssence->id]));
            } else {
                //TODO: add bootbox error
            }
        }

        return $this->render('/developer/create_update_free_essence', [
            'freeEssence' => $freeEssence,
        ]);
    }

    /**
     * Updates free essence
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionUpdateFreeEssence($id)
    {
        /** @var FreeEssences $freeEssence */
        $freeEssence = FreeEssences::findOne($id);

        if (!$freeEssence)
            throw new NotFoundHttpException('Wrong page ID');

        $freeEssence->scenario = FreeEssences::SCENARIO_UPDATE;

        //update page via pjax
        if ($freeEssence->load(Yii::$app->request->post()) && $freeEssence->validate()) {

            if ($freeEssence->save()) {
                $success = true;
            } else {
                $success = false;
            }

            return $this->render('/developer/create_update_free_essence', [
                'freeEssence' => $freeEssence,
                'success' => $success
            ]);
        }

        //initialize fields group
        $devFieldGroup = new DevFieldsGroup();
        $devFieldGroup->setFieldTemplateReference($freeEssence->getFieldTemplateReference());
        $devFieldGroup->initialize(Yii::$app->request->post('_fieldTemplateId'));

        //try to load validate and save field via pjax
        if ($devFieldGroup->load(Yii::$app->request->post()) && $devFieldGroup->validate()) {

            if (!$devFieldGroup->save()) {
                //TODO: bootbox error
            }

            return FieldsDevInputWidget::widget([
                'devFieldGroup' => $devFieldGroup,
                'dataSaved' => true,
            ]);
        }

        $fieldTemplatesTranslatable = FieldTemplate::getListQuery($freeEssence->getFieldTemplateReference())
                                        ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE])
                                        ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                                        ->all();

        $fieldTemplatesSingle = FieldTemplate::getListQuery($freeEssence->getFieldTemplateReference())
                                        ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE])
                                        ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                                        ->all();

        return $this->render('/developer/create_update_free_essence', [
            'freeEssence' => $freeEssence,
            'devFieldGroup' => $devFieldGroup,
            'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
            'fieldTemplatesSingle' => $fieldTemplatesSingle
        ]);
    }

    public function actionDeleteFreeEssence($id)
    {

    }

}
