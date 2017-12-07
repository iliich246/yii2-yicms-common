<?php

/** @var $widget Iliich246\YicmsCommon\Fields\FieldTypeWidget*/

?>

<?= $widget->form->field($widget->fieldModel, $widget->fieldModel->getKey(), [

])->textInput() ?>
<?php /*

     <?= $form->field($model, 'name',[
        'template' => "
            <div class=\"col-md-12\">
                <div class=\"form-group\">
                    {input}
                </div>
            </div>",
        'inputOptions' => [
            'class' => 'form-control autosize',
            'placeholder' => 'Название организации'
        ],
        'options' => [
            'class' => 'row'
        ]
    ])->textarea()
      ->label(false)
    ?>


 */ ?>