<?php

/** @var $this yii\web\View */
/** @var $widget Iliich246\YicmsCommon\Conditions\ConditionTypeWidget */

if (trim($widget->condition->getDescription())) {
    $labelOptions = [
        'class'          => 'condition-label',
        'data-toggle'    => 'tooltip',
        'data-placement' => 'top',
        'title'          => $widget->condition->getDescription()
    ];
} else {
    $labelOptions = [
        'class' => 'condition-label',
    ];
}

?>

<?= $widget->form->field($widget->condition, $widget->condition->getKey(), [
    'labelOptions' => $labelOptions
])->checkbox(

)->label($widget->condition->getName()) ?>
