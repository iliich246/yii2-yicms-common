<?php

/** @var $this yii\web\View */
/** @var $widget Iliich246\YicmsCommon\Conditions\ConditionRenderWidget */
/** @var $conditionsArray array */

?>

<?php foreach($conditionsArray as $condition): ?>
    <?= \Iliich246\YicmsCommon\Conditions\ConditionTypeWidget::widget([
        'form'       => $widget->form,
        'condition'  => $condition,
        'isModal'    => $widget->isModal
    ]) ?>
<?php endforeach; ?>
