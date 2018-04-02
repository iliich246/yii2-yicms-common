<?php

/** @var $this yii\web\View */
/** @var $widget Iliich246\YicmsCommon\Conditions\ConditionTypeWidget */

?>

<?= $widget->form->field($widget->condition, $widget->condition->getKey())->radioList(
    $widget->condition->getValuesTranslatedArray()
)->label($widget->condition->getName()) ?>
