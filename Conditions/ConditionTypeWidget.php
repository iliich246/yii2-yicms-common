<?php

namespace Iliich246\YicmsCommon\Conditions;

use yii\base\Model;
use yii\bootstrap\Widget;
use Iliich246\YicmsCommon\Base\CommonException;

/**
 * Class ConditionTypeWidget
 *
 * This widget render concrete condition by his type
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionTypeWidget extends Widget
{
    /**
     * @var \yii\bootstrap\ActiveForm form, for render control elements in tabs
     */
    public $form;

    /**
     * @var Condition instance of model, that`s widget will be render
     */
    public $conditionModel;

    /**
     * @inheritdoc
     */
    public function run()
    {

    }
}
