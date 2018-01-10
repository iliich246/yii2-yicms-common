<?php

namespace Iliich246\YicmsCommon\Controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\Base\DevFilter;

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

    }

    /**
     * Action for update conditions templates list container
     * @param $conditionTemplateReference
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionUpdateConditionsListContainer($conditionTemplateReference)
    {

    }

    /**
     * Action for delete conditions template
     * @param $conditionTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDeleteConditionsBlockTemplate($conditionTemplateId)
    {

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

    }
}
