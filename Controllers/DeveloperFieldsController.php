<?php

namespace Iliich246\YicmsCommon\Controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\Base\DevFilter;
use Iliich246\YicmsCommon\Widgets\FieldsDevInputWidget;
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
     * @return string
     * @throws BadRequestHttpException
     * @throws \Exception
     */
    public function actionRefreshModal()
    {
        if (Yii::$app->request->isPjax &&
            Yii::$app->request->post('_pjax') == '#'.FieldsDevInputWidget::getPjaxContainerId())
        {
            $devFieldGroup = new DevFieldsGroup();
            //$devFieldGroup->setFieldsReferenceAble($page);
            $devFieldGroup->initialize( Yii::$app->request->post('fieldTemplateReference' ));

            return FieldsDevInputWidget::widget([
                //'devFieldGroup' => $devFieldGroup
            ]);
        }

        throw new BadRequestHttpException();
    }
}
