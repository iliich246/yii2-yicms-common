<?php

/** @var $this yii\web\View */
/** @var $widget Iliich246\YicmsCommon\Conditions\ConditionTypeWidget */

?>

<?= $widget->form->field($widget->condition, $widget->condition->getKey())->dropDownList(
    $widget->condition->getValuesTranslatedArray()
)->label($widget->condition->getName()) ?>
