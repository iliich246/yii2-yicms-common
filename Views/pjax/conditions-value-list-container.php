<?php

/** @var $this \yii\web\View */
/** @var $conditionTemplate \Iliich246\YicmsCommon\Conditions\ConditionTemplate */
/** @var $conditionValues  \Iliich246\YicmsCommon\Conditions\ConditionValues[] */

$js = <<<JS
;(function() {
    var conditionDataListModal = $('.condition-values-list-modal');

    var homeUrl = $(conditionDataListModal).data('homeUrl');

    var createConditionValueUrl        = homeUrl + '/common/dev-conditions/create-condition-value';
    var updateConditionValueUrl        = homeUrl + '/common/dev-conditions/update-condition-value';
    var conditionValueUpDependentUrl   = homeUrl + '/common/dev-conditions/condition-value-up-order';
    var conditionValueDownDependentUrl = homeUrl + '/common/dev-conditions/condition-value-down-order';

    var pjaxContainer   = $(conditionDataListModal).parent('.pjax-container');
    var pjaxContainerId = '#' + $(pjaxContainer).attr('id');

    var returnUrl       = $(pjaxContainer).data('returnUrlConditions');

    var backButton        = $('.condition-values-list-back');
    var addNewValueButton = $('.add-new-condition-value-button');

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

    $(addNewValueButton).on('click', function() {
        $.pjax({
            url: createConditionValueUrl + '?conditionTemplateId=' + $(this).data('conditionTemplateId'),
            container: pjaxContainerId,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500,
        });
    });

    $('.condition-value-arrow-up-modal').on('click', function() {
        $.pjax({
            url: conditionValueUpDependentUrl
                 + '?conditionValueId=' + $(this).data('conditionValueId'),
            container: pjaxContainerId,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $('.condition-value-arrow-down-modal').on('click', function() {
        $.pjax({
            url: conditionValueDownDependentUrl
                 + '?conditionValueId=' + $(this).data('conditionValueId'),
            container: pjaxContainerId,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $('.condition-value-block-item').on('click', function() {
        $.pjax({
            url: updateConditionValueUrl
                 + '?conditionValueId=' + $(this).data('conditionValueId'),
            container: pjaxContainerId,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

})();
JS;

$this->registerJs($js);

?>

<div class="modal-content condition-values-list-modal"
     data-home-url="<?= \yii\helpers\Url::base() ?>"
     data-condition-template-reference="<?= $conditionTemplate->condition_template_reference ?>"
     data-return-url-fields="<?= \yii\helpers\Url::toRoute([
         '/common/dev-fields/update-fields-list-container-dependent',
         'conditionTemplateReference' => $conditionTemplate->condition_template_reference,
     ]) ?>"
    >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title">
            Conditions data list
            <span class="glyphicon glyphicon-arrow-left condition-values-list-back"
                  style="float: right;margin-right: 20px"></span>
        </h3>
        <?php if ($conditionTemplate->type == \Iliich246\YicmsCommon\Conditions\ConditionTemplate::TYPE_CHECKBOX): ?>
        <h4>For "checkbox" condition type used only first value, other values will be ignored</h4>
        <?php endif; ?>
    </div>
    <div class="modal-body">
        <button class="btn btn-primary add-new-condition-value-button"
                data-condition-template-id="<?= $conditionTemplate->id ?>">
            Add new condition data
        </button>
        <hr>
        <?php foreach($conditionValues as $conditionValue): ?>
            <div class="row list-items">
                <div class="col-xs-9 list-title">
                    <p data-condition-value-id="<?= $conditionValue->id ?>"
                       class="condition-value-block-item">
                        <?= $conditionValue->value_name ?>
                    </p>
                </div>
                <div class="col-xs-3 list-controls">
                    <?php if ($conditionValue->is_default): ?>
                        <span class="glyphicon glyphicon-tower"></span>
                    <?php endif; ?>
                    <?php if ($conditionValue->canUpOrder()): ?>
                        <span class="glyphicon condition-value-arrow-up-modal glyphicon-arrow-up"
                              data-condition-value-id="<?= $conditionValue->id ?>"></span>
                    <?php endif; ?>
                    <?php if ($conditionValue->canDownOrder()): ?>
                        <span class="glyphicon condition-value-arrow-down-modal glyphicon-arrow-down"
                              data-condition-value-id="<?= $conditionValue->id ?>"></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</div>
