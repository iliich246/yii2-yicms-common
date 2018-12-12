<?php

/** @var $this yii\web\View */
/** @var $widget Iliich246\YicmsCommon\Conditions\ConditionTypeWidget */

if (trim($widget->condition->getDescription())) {
    $labelOptions = [
        'class'          => 'condition-label',
        'data-toggle'    => 'tooltip',
        'data-placement' => 'top',
        'title'          => $widget->condition->getDescription()
    ];
} else {
    $labelOptions = [
        'class' => 'condition-label',
    ];
}

?>

<?php if ($widget->condition->isValues()): ?>
    <?= $widget->form->field($widget->condition, $widget->condition->getKey(), [
        'labelOptions' => $labelOptions
    ])->radioList(
        $widget->condition->getValuesTranslatedArray()
    )->label($widget->condition->getName()) ?>
<?php else: ?>
    <?php if (\Iliich246\YicmsCommon\CommonModule::isUnderDev()): ?>
        <div class="form-group">
            <label class="control-label">
                (DEV:) <?= $widget->condition->getName() ?> (Condition has no values)
            </label>
        </div>
    <?php endif; ?>
<?php endif; ?>
