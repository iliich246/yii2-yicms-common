<?php

use yii\bootstrap\ActiveForm;

/** @var $fieldsGroup \Iliich246\YicmsCommon\Fields\FieldsGroup */
/** @var $fieldTemplateReference string */
/** @var $form ActiveForm */
/** @var $refreshUrl string */

$js = <<<JS

;(function(){
    var modalFieldsInfo = $('.modal-fields-info');

    var pjaxContainer   = $(modalFieldsInfo).closest('.pjax-container');
    var pjaxContainerId = '#' + $(pjaxContainer).attr('id');

    var homeUrl                = $(modalFieldsInfo).data('homeUrl');
    var fieldTemplateReference = $(modalFieldsInfo).data('fieldTemplateReference');
    var refreshUrl             = $(modalFieldsInfo).data('refreshUrl');

    var changeVisibleUrlAjax  = homeUrl + '/common/admin-fields/change-field-visible-ajax';
    var changeEditableUrlAjax = homeUrl + '/common/dev-fields/change-field-editable-ajax';

    $('.field-visible-link-modal').on('click', function() {
        $.ajax({
            url: changeVisibleUrlAjax + '?fieldTemplateReference=' + fieldTemplateReference
                + '&fieldId=' + $(this).data('fieldId'),
            complete: refresh
        });
    });

    $('.field-editable-link-modal').on('click', function() {
        $.ajax({
            url: changeEditableUrlAjax + '?fieldTemplateReference=' + fieldTemplateReference
                + '&fieldId=' + $(this).data('fieldId'),
            complete: refresh
        })
    });

    function refresh() {
         $.pjax({
             url: refreshUrl,
             container: pjaxContainerId,
             scrollTo: false,
             push: false,
             type: "POST",
             timeout: 2500
         });
    }
})();
JS;

$this->registerJs($js);

?>

<div class="modal-fields-info"
     data-home-url="<?= \yii\helpers\Url::base() ?>"
     data-field-template-reference="<?= $fieldsGroup->getCurrentFieldTemplateReference() ?>"
     data-refresh-url="<?= $refreshUrl ?>"
></div>

<?php if (isset($success) && $success): ?>
    <div class="alert alert-success alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                aria-hidden="true">?</span></button>
        <strong>Success!</strong> Data on page updated.
    </div>
<?php endif; ?>

<?= $fieldsGroup->render($form, true) ?>
