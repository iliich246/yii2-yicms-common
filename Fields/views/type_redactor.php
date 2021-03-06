<?php

/** @var $widget Iliich246\YicmsCommon\Fields\FieldTypeWidget */

$infoString = '';
$optionsTemplate = '';
if (!$widget->fieldModel->isFictive()) {
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

    $optionsTemplate = '<div class="dropdown field-dropdown" style="display: inline; float: right">
                        <a id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            options<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dLabel">';

    $widget->isModal ? $fieldVisibleClass = 'field-visible-link-modal' : $fieldVisibleClass = 'field-visible-link';

    if ($widget->fieldModel->isVisible())
        $optionsTemplate .=
            '<li data-field-id="' . $widget->fieldModel->getFieldId() . '"
             class="' . $fieldVisibleClass . '">
            <p>Change to invisible</p>
        </li>';
    else
        $optionsTemplate .=
            '<li data-field-id="' . $widget->fieldModel->getFieldId() . '"
             class="' . $fieldVisibleClass . '">
            <p>Change to visible</p>
        </li>';

    if (\Iliich246\YicmsCommon\CommonModule::isUnderDev()) {

        $widget->isModal ? $fieldEditableClass = 'field-editable-link-modal' : $fieldEditableClass = 'field-editable-link';

        if ($widget->fieldModel->isEditable())
            $optionsTemplate .=
                '<li data-field-id="' . $widget->fieldModel->getFieldId() . '"
                 class="' . $fieldEditableClass . '">
                <p>Change to not editable(Dev)</p>
            </li>';
        else
            $optionsTemplate .=
                '<li data-field-id="' . $widget->fieldModel->getFieldId() . '"
                 class="' . $fieldEditableClass . '">
                 <p>Change to editable(Dev)</p>
            </li>';
    }

    $optionsTemplate .= '   </ul>
                    </div>';
}

if ($widget->fieldModel->getFieldDescription()) {
    $labelOptions = [
        'class'          => 'redactor-label',
        'data-toggle'    => 'tooltip',
        'data-placement' => 'top',
        'title' => $widget->fieldModel->getFieldDescription()
    ];
} else {
    $labelOptions = [
        'class' => 'redactor-label',
    ];
}

?>

<?= $widget->form->field($widget->fieldModel, $widget->fieldModel->getKey(), [
    'template' => '
        {label}
        ' . $infoString . $optionsTemplate . '
        {input}
        {error}
    ',
    'labelOptions' => $labelOptions
])
    ->widget(\yii\redactor\widgets\Redactor::className())
?>
