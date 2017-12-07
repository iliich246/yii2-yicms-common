<?php

/** @var $widget Iliich246\YicmsCommon\Fields\FieldTypeWidget*/

?>

<?= $widget->form->field($widget->fieldModel, $widget->fieldModel->getKey())
    ->widget(\yii\redactor\widgets\Redactor::className())
?>
