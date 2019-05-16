<?php

namespace app\yicms\Common\Controllers;

use Yii;
use yii\web\Controller;

/**
 * Class AdminConditionsController
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class AdminConditionsController extends Controller
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
}
