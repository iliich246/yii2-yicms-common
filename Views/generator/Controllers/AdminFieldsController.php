<?php

namespace app\yicms\Common\Controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Fields\Field;
use Iliich246\YicmsCommon\Fields\FieldsGroup;

/**
 * Class AdminFieldsController
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class AdminFieldsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            'root' => [
//                'class' => AdminFilter::className(),
//                'except' => ['upload-file'],
//            ],
        ];
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
            'fieldsGroup'            => $fieldsGroup,
            'fieldTemplateReference' => $fieldTemplateReference,
            'success'                => true,
        ]);
    }

    /**
     * This action invert field visible value via ajax without render of template
     * @param $fieldTemplateReference
     * @param $fieldId
     * @return bool
     * @throws BadRequestHttpException
     * @throws CommonException
     */
    public function actionChangeFieldVisibleAjax($fieldTemplateReference, $fieldId)
    {
        if (!Yii::$app->request->isAjax) throw new BadRequestHttpException();

        if (!CommonModule::isUnderDev() && !CommonModule::isUnderAdmin()) throw new BadRequestHttpException();

        /** @var Field $field */
        $field = Field::findOne($fieldId);

        if (!$field) throw new BadRequestHttpException('Wrong fieldId = ' . $fieldId);

        $field->visible = !$field->visible;

        if ($field->save(false)) return true;

        throw new CommonException('Can`t update field');
    }
}
