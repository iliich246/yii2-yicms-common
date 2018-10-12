<?php

namespace Iliich246\YicmsCommon\Conditions;

use yii\bootstrap\Widget;

/**
 * Class ConditionRenderWidget
 *
 * This widget render concrete condition by his type
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionRenderWidget extends Widget
{
    /** @var \yii\bootstrap\ActiveForm form, for render control elements in tabs */
    public $form;
    /** @var Condition instance of model, that`s widget will be render */
    public $conditionsArray;
    /** @var bool if true widget will render in modal window mode */
    public $isModal;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!$this->conditionsArray) return false;

        return $this->render('condition_fields', [
            'widget'          => $this,
            'conditionsArray' => $this->conditionsArray,
        ]);
    }
}
