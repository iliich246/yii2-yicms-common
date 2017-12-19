<?php

/** @var $widget Iliich246\YicmsCommon\Fields\FieldTypeWidget*/

?>

<?php if ($widget->fieldModel->getLanguageType() == \Iliich246\YicmsCommon\Fields\FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE): ?>
    <?= $widget->form->field($widget->fieldModel, $widget->fieldModel->getKey(), [
        'template' => '
        {label}

        <div class="dropdown field-dropdown" style="display: inline; float: right">
              <a id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                options<span class="caret"></span>
              </a>
              <ul class="dropdown-menu" aria-labelledby="dLabel">
                    <li><a href="#">1</a></li>
                    <li><a href="#">2</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="#">3</a></li>
                    <li><a href="#">4</a></li>
              </ul>
            </div>

        {input}
        {error}
        ',
        'labelOptions' => [
            'class' => 'penis',
        ]
    ])->textInput() ?>
<?php else: ?>
    <?= $widget->form->field($widget->fieldModel, $widget->fieldModel->getKey(), [
        'template' => '
        {label}' .
            $widget->fieldModel->getKey()
        .'<div class="dropdown field-dropdown" style="display: inline; float: right">
              <a id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                options<span class="caret"></span>
              </a>
              <ul class="dropdown-menu" aria-labelledby="dLabel">
                    <li><a href="#">3</a></li>
                    <li><a href="#">4</a></li>
              </ul>
            </div>
        {input}
        {error}
        ',
        'labelOptions' => [
            'class' => 'penis',
        ]
    ])->textInput() ?>
<?php endif; ?>
