<?php

/** @var $this \yii\web\View */
/** @var $conditionTemplateReference string */
/** @var $conditionTemplates \Iliich246\YicmsCommon\Conditions\ConditionTemplate[] */
/** @var $pjaxName string */
/** @var $modalName string */

$js = <<<JS
;(function() {
    var conditionsListModal = $('.conditions-list-modal');

    var homeUrl = $(conditionsListModal).data('homeUrl');

    var emptyModalUrl                 = homeUrl + '/common/dev-conditions/empty-modal-dependent';
    var updateModalDependentUrl       = homeUrl + '/common/dev-conditions/load-modal-dependent';
    var fieldTemplateUpDependentUrl   = homeUrl + '/common/dev-conditions/condition-template-up-order-dependent';
    var fieldTemplateDownDependentUrl = homeUrl + '/common/dev-conditions/condition-template-down-order-dependent';

    var fieldTemplateReference = $(conditionsListModal).data('conditionTemplateReference');
    var pjaxContainerOwner     = $(conditionsListModal).data('pjaxContainerOwner');
    var modalOwner             = $(conditionsListModal).data('modalOwner');

    var pjaxContainer   = $(conditionsListModal).parent('.pjax-container');
    var pjaxContainerId = '#' + $(pjaxContainer).attr('id');

    var returnUrl       = $(pjaxContainer).data('returnUrl');

    var backButton        = $('.conditions-modal-list-back');
    var addNewFieldButton = $('.add-new-field-button');

    $(backButton).on('click', goBack);

    function goBack() {
        $.pjax({
            url: returnUrl,
            container: pjaxContainerId,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500,
        });
    }


})();
JS;

$this->registerJs($js);
?>

<div class="modal-content conditions-list-modal"
     data-home-url="<?= \yii\helpers\Url::base() ?>"
     data-condition-template-reference="<?= $conditionTemplateReference ?>"
     data-pjax-container-owner="<?= $pjaxName ?>"
     data-modal-owner="<?= $modalName ?>"
     data-return-url-fields="<?= \yii\helpers\Url::toRoute([
         '/common/dev-fields/update-fields-list-container-dependent',
         'conditionTemplateReference' => $conditionTemplateReference,
         'pjaxName' => $pjaxName,
         'modalName' => $modalName,
     ]) ?>"
>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title">
            Conditions list
            <span class="glyphicon glyphicon-arrow-left conditions-modal-list-back"
                  style="float: right;margin-right: 20px"></span>
        </h3>
    </div>
    <div class="modal-body">
        <button class="btn btn-primary add-new-field-button">
            Add new condition
        </button>
        <hr>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</div>