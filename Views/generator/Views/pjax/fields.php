<?php //template

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;

/** @var $fieldsGroup \Iliich246\YicmsCommon\Fields\FieldsGroup */
/** @var $fieldTemplateReference string */

$js = <<<JS

;(function(){
    var pjaxContainer = $('#edit-fields-container');

    $(pjaxContainer).on('pjax:success', function() {

        $(".alert").hide().slideDown(500).fadeTo(500, 1);

        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 3000);

        $(pjaxContainer).on('pjax:error', function(xhr, textStatus) {
            bootbox.alert({
                size: 'large',
                title: "There are some error on ajax request!",
                message: textStatus.responseText,
                className: 'bootbox-error'
            });
        });
    });
})();
JS;

$this->registerJs($js);

\Iliich246\YicmsCommon\Assets\FieldsAsset::register($this);

$js2 = <<<JS
;(function(){
    $('[data-toggle="tooltip"]').tooltip();
})();
JS;

?>

<?php Pjax::begin([
    'options' => [
        'id'                            => 'edit-fields-container',
        'class'                         => 'pjax-container',
        'data-home-url'                 => \yii\helpers\Url::base(),
        'data-field-template-reference' => $fieldTemplateReference
    ],
    'enablePushState'    => false,
    'enableReplaceState' => false
]) ?>

<?php $form = ActiveForm::begin([
    'id'      => 'edit-fields-form',
    'options' => [
        'data-pjax' => true,
    ],
]);
?>

<?php $this->registerJs($js2); ?>

<?php if (isset($success) && $success): ?>
    <div class="alert alert-success alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                aria-hidden="true">×</span></button>
        <strong>Success!</strong> Data on page updated.
    </div>
<?php endif; ?>

<?= $fieldsGroup->render($form) ?>

<div class="row control-buttons">
    <div class="col-xs-12">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton('Cancel', ['class' => 'btn btn-default cancel-button']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php Pjax::end() ?>
