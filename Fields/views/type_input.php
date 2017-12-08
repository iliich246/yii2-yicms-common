<?php

/** @var $widget Iliich246\YicmsCommon\Fields\FieldTypeWidget*/

?>

<?= $widget->form->field($widget->fieldModel, $widget->fieldModel->getKey(), [
    'template' => '
        {label} <span class="glyphicon glyphicon-search" aria-hidden="true" style="float: right"></span>
        {input}
        {error}
        ',
    'labelOptions' => [
        'class' => 'penis',
    ]
])->textInput() ?>
