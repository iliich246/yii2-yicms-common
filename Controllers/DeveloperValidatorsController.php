<?php

namespace Iliich246\YicmsCommon\Controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Class DeveloperValidatorController
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DeveloperValidatorsController extends Controller
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

    public function actionTest()
    {
        return 'test';
    }
}
