<?php

namespace Iliich246\YicmsCommon\Controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\DevFilter;
use Iliich246\YicmsCommon\Fields\Field;
use Iliich246\YicmsCommon\Fields\FieldsGroup;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\DevFieldsGroup;
use Iliich246\YicmsCommon\Fields\FieldsDevModalWidget;

/**
 * Class DeveloperFieldsController
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DeveloperFieldsController extends Controller
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
     * Action for refresh dev fields modal window
     * @param $fieldTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws \Exception
     */
    public function actionLoadModal($fieldTemplateId)
    {
        if (Yii::$app->request->isPjax &&
            Yii::$app->request->post('_pjax') == '#' . FieldsDevModalWidget::getPjaxContainerId()
        ) {
            $devFieldGroup = new DevFieldsGroup();
            $devFieldGroup->initialize($fieldTemplateId);

            return FieldsDevModalWidget::widget([
                'devFieldGroup' => $devFieldGroup
            ]);
        }

        throw new BadRequestHttpException();
    }

    /**
     * Action for send empty fields modal window
     * @param $fieldTemplateReference
     * @return string
     * @throws BadRequestHttpException
     * @throws \Exception
     */
    public function actionEmptyModal($fieldTemplateReference)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        $devFieldGroup = new DevFieldsGroup();
        $devFieldGroup->setFieldTemplateReference($fieldTemplateReference);
        $devFieldGroup->initialize();

        return FieldsDevModalWidget::widget([
            'devFieldGroup' => $devFieldGroup
        ]);
    }

    /**
     * Action for update fields list container
     * @param $fieldTemplateReference
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionUpdateFieldsListContainer($fieldTemplateReference)
    {
        if (Yii::$app->request->isPjax &&
            Yii::$app->request->post('_pjax') == '#update-fields-list-container'
        ) {

            $fieldTemplatesTranslatable = FieldTemplate::getListQuery($fieldTemplateReference)
                ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE])
                ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                ->all();

            $fieldTemplatesSingle = FieldTemplate::getListQuery($fieldTemplateReference)
                ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE])
                ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                ->all();

            return $this->render('/pjax/update-fields-list-container', [
                'fieldTemplateReference' => $fieldTemplateReference,
                'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
                'fieldTemplatesSingle' => $fieldTemplatesSingle
            ]);
        }

        throw new BadRequestHttpException();
    }

    /**
     * Action for delete field template
     * @param $fieldTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDeleteFieldTemplate($fieldTemplateId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var FieldTemplate $fieldTemplate */
        $fieldTemplate = FieldTemplate::findOne($fieldTemplateId);

        if (!$fieldTemplate) throw new NotFoundHttpException('Wrong fieldTemplateId');

        $fieldTemplateReference = $fieldTemplate->field_template_reference;

        //TODO: for field templates with constraints makes request of root password
//        if ($fieldTemplate->isConstraints())
//            return $this->redirect(Url::toRoute(['xxx', 'id' => $id]));

        $fieldTemplate->delete();

        $fieldTemplatesTranslatable = FieldTemplate::getListQuery($fieldTemplateReference)
            ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE])
            ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
            ->all();

        $fieldTemplatesSingle = FieldTemplate::getListQuery($fieldTemplateReference)
            ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE])
            ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
            ->all();

        return $this->render('/pjax/update-fields-list-container', [
            'fieldTemplateReference' => $fieldTemplateReference,
            'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
            'fieldTemplatesSingle' => $fieldTemplatesSingle
        ]);
    }

    /**
     * Action for up field template order
     * @param $fieldTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionFieldTemplateUpOrder($fieldTemplateId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var FieldTemplate $fieldTemplate */
        $fieldTemplate = FieldTemplate::findOne($fieldTemplateId);

        if (!$fieldTemplate) throw new NotFoundHttpException('Wrong fieldTemplateId');

        $fieldTemplate->upOrder();

        $fieldTemplatesTranslatable = FieldTemplate::getListQuery($fieldTemplate->field_template_reference)
            ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE])
            ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
            ->all();

        $fieldTemplatesSingle = FieldTemplate::getListQuery($fieldTemplate->field_template_reference)
            ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE])
            ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
            ->all();

        return $this->render('/pjax/update-fields-list-container', [
            'fieldTemplateReference' => $fieldTemplate->field_template_reference,
            'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
            'fieldTemplatesSingle' => $fieldTemplatesSingle
        ]);
    }

    /**
     * Action for down field template order
     * @param $fieldTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionFieldTemplateDownOrder($fieldTemplateId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var FieldTemplate $fieldTemplate */
        $fieldTemplate = FieldTemplate::findOne($fieldTemplateId);

        if (!$fieldTemplate) throw new NotFoundHttpException('Wrong fieldTemplateId');

        $fieldTemplate->downOrder();

        $fieldTemplatesTranslatable = FieldTemplate::getListQuery($fieldTemplate->field_template_reference)
            ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE])
            ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
            ->all();

        $fieldTemplatesSingle = FieldTemplate::getListQuery($fieldTemplate->field_template_reference)
            ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE])
            ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
            ->all();

        return $this->render('/pjax/update-fields-list-container', [
            'fieldTemplateReference' => $fieldTemplate->field_template_reference,
            'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
            'fieldTemplatesSingle' => $fieldTemplatesSingle
        ]);
    }

    /**
     * This action invert field editable value
     * @param $fieldTemplateReference
     * @param $fieldId
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionChangeFieldEditable($fieldTemplateReference, $fieldId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var Field $field */
        $field = Field::findOne($fieldId);

        if (!$field) throw new BadRequestHttpException('Wrong fieldId = ' . $fieldId);

        $field->editable = !$field->editable;
        $field->save(false);

        $fieldsGroup = new FieldsGroup();
        $fieldsGroup->initializePjax($fieldTemplateReference, $field);

        return $this->render(CommonModule::getInstance()->yicmsLocation . '/Common/Views/pjax/fields', [
            'fieldsGroup' => $fieldsGroup,
            'fieldTemplateReference' => $fieldTemplateReference,
            'success' => true,
        ]);
    }

    /**
     * This action invert field visible value
     * @param $fieldTemplateReference
     * @param $fieldId
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionChangeFieldVisible($fieldTemplateReference, $fieldId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        if (!CommonModule::isUnderDev() && !CommonModule::isUnderAdmin()) throw new BadRequestHttpException();

        /** @var Field $field */
        $field = Field::findOne($fieldId);

        if (!$field) throw new BadRequestHttpException('Wrong fieldId = ' . $fieldId);

        $field->visible = !$field->visible;
        $field->save(false);

        $fieldsGroup = new FieldsGroup();
        $fieldsGroup->initializePjax($fieldTemplateReference, $field);

        return $this->render(CommonModule::getInstance()->yicmsLocation . '/Common/Views/pjax/fields', [
            'fieldsGroup' => $fieldsGroup,
            'fieldTemplateReference' => $fieldTemplateReference,
            'success' => true,
        ]);
    }
}