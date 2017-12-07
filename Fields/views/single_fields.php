<?php
/** @var $widget \Iliich246\YicmsCommon\Fields\FieldsRenderWidget */
/** @var $fieldsArray array */
?>


<?php foreach($fieldsArray as $field): ?>

    <?= \Iliich246\YicmsCommon\Fields\FieldTypeWidget::widget([
        'form' => $widget->form,
        'fieldModel' => $field
    ]) ?>

<?php endforeach; ?>
