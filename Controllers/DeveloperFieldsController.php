<?php

namespace Iliich246\YicmsCommon\Controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\Base\DevFilter;
use Iliich246\YicmsCommon\Widgets\FieldsDevInputWidget;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\DevFieldsGroup;

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
//                'except' => ['login-as-root'],
//            ],
        ];
    }

    /**
     * Action for refresh dev fields modal window
     * @param $fieldTemplateReference
     * @return string
     * @throws BadRequestHttpException
     * @throws \Exception
     */
    public function actionLoadModal($fieldTemplateReference)
    {
        if (Yii::$app->request->isPjax &&
            Yii::$app->request->post('_pjax') == '#'.FieldsDevInputWidget::getPjaxContainerId())
        {
            $devFieldGroup = new DevFieldsGroup();
            $devFieldGroup->initialize($fieldTemplateReference);

            return FieldsDevInputWidget::widget([
                'devFieldGroup' => $devFieldGroup
            ]);
        }

        throw new BadRequestHttpException();
    }

    /**
     * @param $fieldTemplateReference
     */
    public function actionEmptyModal($fieldTemplateReference)
    {
        throw new BadRequestHttpException();
    }

    /**
     * @param $fieldTemplateReference
     */
    public function actionUpdateFieldsListContainer($fieldTemplateReference)
    {
        if (Yii::$app->request->isPjax &&
            Yii::$app->request->post('_pjax') == '#update-fields-list-container') {

            $fieldTemplatesTranslatable = FieldTemplate::getListQuery($fieldTemplateReference)
                ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE])
                ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                ->all();

            $fieldTemplatesSingle = FieldTemplate::getListQuery($fieldTemplateReference)
                ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE])
                ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                ->all();

            return $this->render('/pjax/update-fields-list-container', [
                'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
                'fieldTemplatesSingle' => $fieldTemplatesSingle
            ]);
        }

        throw new BadRequestHttpException();
    }
}
