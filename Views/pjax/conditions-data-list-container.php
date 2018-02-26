<?php

/** @var $this \yii\web\View */
/** @var $conditionTemplate \Iliich246\YicmsCommon\Conditions\ConditionTemplate */

$js = <<<JS
;(function() {
    var conditionDataListModal = $('.condition-data-list-modal');

    var homeUrl = $(conditionDataListModal).data('homeUrl');

    var pjaxContainer   = $(conditionDataListModal).parent('.pjax-container');
    var pjaxContainerId = '#' + $(pjaxContainer).attr('id');

    var returnUrl       = $(pjaxContainer).data('returnUrl');

    var backButton        = $('.condition-data-list-back');
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

<div class="modal-content condition-data-list-modal"
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
            <span class="glyphicon glyphicon-arrow-left condition-data-list-back"
                  style="float: right;margin-right: 20px"></span>
        </h3>
    </div>
    <div class="modal-body">
        <button class="btn btn-primary add-new-field-button">
            Add new condition data
        </button>
        <hr>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</div>
