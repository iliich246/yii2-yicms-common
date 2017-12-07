<?php
/** @var $widget \Iliich246\YicmsCommon\Fields\FieldsRenderWidget */
/** @var $fieldsArray array */
?>


<?php foreach($fieldsArray as $field) ?>

    <?= \Iliich246\YicmsCommon\Fields\FieldTypeWidget::widget([
        'form' => $widget->form,
        'fieldsArray' => $field
    ]) ?>

<?php /* foreach($translateModelList as $fieldKey => $fieldTranslateModel): ?>

    <?= PageFieldWidget::widget([
        'form' => $form,
        'languageKey' => $languageKey,
        'fieldKey' => $fieldKey,
        'fieldTranslateModel' => $fieldTranslateModel
    ]) ?>

<?php endforeach; */ ?>


