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

    var emptyModalUrl                     = homeUrl + '/common/dev-conditions/empty-modal-dependent';
    var updateModalDependentUrl           = homeUrl + '/common/dev-conditions/load-modal-dependent';
    var conditionTemplateUpDependentUrl   = homeUrl + '/common/dev-conditions/condition-template-up-order-dependent';
    var conditionTemplateDownDependentUrl = homeUrl + '/common/dev-conditions/condition-template-down-order-dependent';

    var conditionTemplateReference = $(conditionsListModal).data('conditionTemplateReference');
    var pjaxContainerOwner     = $(conditionsListModal).data('pjaxContainerOwner');
    var modalOwner             = $(conditionsListModal).data('modalOwner');

    var pjaxContainer   = $(conditionsListModal).parent('.pjax-container');
    var pjaxContainerId = '#' + $(pjaxContainer).attr('id');

    var returnUrl       = $(pjaxContainer).data('returnUrl');

    var backButton        = $('.conditions-modal-list-back');
    var addNewConditionButton = $('.add-new-condition-button');

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

    $(addNewConditionButton).on('click', function() {

        $(pjaxContainer).data('returnUrlConditions', $(conditionsListModal).data('returnUrlConditions'));

        $.pjax({
            url: emptyModalUrl
                 + '?conditionTemplateReference=' + conditionTemplateReference
                 + '&pjaxName=' + pjaxContainerOwner
                 + '&modalName=' + modalOwner,
            container: pjaxContainerId,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $('.condition-item-modal').on('click', function() {

        $(pjaxContainer).data('returnUrlConditions', $(conditionsListModal).data('returnUrlConditions'));

        var conditionTemplateId = $(this).data('condition-template-id');

        updateModalDependent(conditionTemplateId);
    });

    $('.condition-arrow-up-modal').on('click', function() {
        $.pjax({
            url: conditionTemplateUpDependentUrl
                 + '?conditionTemplateId=' + $(this).data('conditionTemplateId')
                 + '&pjaxName=' + pjaxContainerOwner
                 + '&modalName=' + modalOwner,
            container: pjaxContainerId,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $('.condition-arrow-down-modal').on('click', function() {
        $.pjax({
            url: conditionTemplateDownDependentUrl
                 + '?conditionTemplateId=' + $(this).data('conditionTemplateId')
                 + '&pjaxName=' + pjaxContainerOwner
                 + '&modalName=' + modalOwner,
            container: pjaxContainerId,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    function updateModalDependent(conditionTemplateId) {
        $.pjax({
            url: updateModalDependentUrl
                 + '?conditionTemplateReference=' + conditionTemplateReference
                 + '&conditionTemplateId=' + conditionTemplateId
                 + '&pjaxName=' + pjaxContainerOwner
                 + '&modalName=' + modalOwner,
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

<div class="modal-content conditions-list-modal"
     data-home-url="<?= \yii\helpers\Url::base() ?>"
     data-condition-template-reference="<?= $conditionTemplateReference ?>"
     data-pjax-container-owner="<?= $pjaxName ?>"
     data-modal-owner="<?= $modalName ?>"
     data-return-url-conditions="<?= \yii\helpers\Url::toRoute([
         '/common/dev-conditions/update-conditions-list-container-dependent',
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
        <button class="btn btn-primary add-new-condition-button">
            Add new condition
        </button>
        <hr>
        <div class="list-block">
            <?php foreach ($conditionTemplates as $conditionTemplate): ?>
                <div class="row list-items condition-item-modal"
                     data-condition-template-id="<?= $conditionTemplate->id ?>"
                >
                    <div class="col-xs-10 list-title">
                        <p>
                            <?= $conditionTemplate->program_name ?> (<?= $conditionTemplate->getTypeName() ?>)
                        </p>
                    </div>
                    <div class="col-xs-2 list-controls">
                        <?php if ($conditionTemplate->editable): ?>
                            <span class="glyphicon glyphicon-pencil"></span>
                        <?php endif; ?>
                        <?php if ($conditionTemplate->canUpOrder()): ?>
                            <span class="glyphicon condition-arrow-up-modal glyphicon-arrow-up"
                                  data-condition-template-id="<?= $conditionTemplate->id ?>"></span>
                        <?php endif; ?>
                        <?php if ($conditionTemplate->canDownOrder()): ?>
                            <span class="glyphicon condition-arrow-down-modal glyphicon-arrow-down"
                                  data-condition-template-id="<?= $conditionTemplate->id ?>"></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</div>
