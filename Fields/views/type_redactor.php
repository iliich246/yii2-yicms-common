<?php

/** @var $widget Iliich246\YicmsCommon\Fields\FieldTypeWidget*/

$infoString = ' ';

if ($widget->fieldModel->isVisible())
    $infoString .= '<span class="glyphicon glyphicon-eye-open" aria-hidden="true"
                          data-toggle="tooltip" data-placement="top" title="Field is visible"></span>';
else
    $infoString .= '<span class="glyphicon glyphicon-eye-close" aria-hidden="true"
                          data-toggle="tooltip" data-placement="top" title="Field is invisible"></span>';

if (\Iliich246\YicmsCommon\CommonModule::isUnderDev()) {
    $infoString .= ' |DEV: ';

    if ($widget->fieldModel->isEditable())
        $infoString .= '<span class="glyphicon glyphicon-pencil" aria-hidden="true"
                              data-toggle="tooltip" data-placement="top" title="This field editable for admin"></span>';
    else
        $infoString .= '<span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"
                              data-toggle="tooltip" data-placement="top" title="This field editable only for developer"></span>';

    $infoString .= ' | ';

    if ($widget->fieldModel->getTemplate()->is_main)
        $infoString .= '<span class="glyphicon glyphicon-tower" aria-hidden="true"
                          data-toggle="tooltip" data-placement="top" title="This is main field in template"></span>';

    if ($widget->fieldModel->getTemplate()->visible)
        $infoString .= '<span class="glyphicon glyphicon-eye-open" aria-hidden="true"
                          data-toggle="tooltip" data-placement="top" title="Field template is visible"></span>';
    else
        $infoString .= '<span class="glyphicon glyphicon-eye-close" aria-hidden="true"
                          data-toggle="tooltip" data-placement="top" title="Field template is invisible"></span>';

    if ($widget->fieldModel->getTemplate()->editable)
        $infoString .= '<span class="glyphicon glyphicon-pencil" aria-hidden="true"
                          data-toggle="tooltip" data-placement="top" title="Field template is editable for admin"></span>';
    else
        $infoString .= '<span class="glyphicon glyphicon-remove" aria-hidden="true"
                          data-toggle="tooltip" data-placement="top" title="Field template editable only for developer"></span>';
}

?>

<?= $widget->form->field($widget->fieldModel, $widget->fieldModel->getKey(), [
    'template' => '
        {label}
        ' . $infoString . '
        {input}
        {error}
    ',
])
    ->widget(\yii\redactor\widgets\Redactor::className())
?>
