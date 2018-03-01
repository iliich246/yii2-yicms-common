<?php

use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Assets\FieldsDevAsset;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\FieldsDevModalWidget;

/** @var $this \yii\web\View */
/** @var $fieldTemplateReference integer */
/** @var $fieldTemplatesTranslatable FieldTemplate[] */
/** @var $fieldTemplatesSingle FieldTemplate[] */
/** @var $pjaxName string */
/** @var $modalName string */

$js = <<<JS
;(function() {
    var fieldsListModal = $('.fields-list-modal');

    var homeUrl = $(fieldsListModal).data('homeUrl');

    var emptyModalUrl                 = homeUrl + '/common/dev-fields/empty-modal-dependent';
    var updateModalDependentUrl       = homeUrl + '/common/dev-fields/load-modal-dependent';
    var fieldTemplateUpDependentUrl   = homeUrl + '/common/dev-fields/field-template-up-order-dependent';
    var fieldTemplateDownDependentUrl = homeUrl + '/common/dev-fields/field-template-down-order-dependent';

    var fieldTemplateReference = $(fieldsListModal).data('fieldTemplateReference');
    var pjaxContainerOwner     = $(fieldsListModal).data('pjaxContainerOwner');
    var modalOwner             = $(fieldsListModal).data('modalOwner');

    var pjaxContainer   = $(fieldsListModal).parent('.pjax-container');
    var pjaxContainerId = '#' + $(pjaxContainer).attr('id');

    var returnUrl       = $(pjaxContainer).data('returnUrl');
    var returnUrlFields = $(pjaxContainer).data('returnUrlFields');

    var backButton        = $('.fields-modal-list-back');
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

    $(addNewFieldButton).on('click', function() {

        $(pjaxContainer).data('returnUrlFields', $(fieldsListModal).data('returnUrlFields'));

        $.pjax({
            url: emptyModalUrl
                 + '?fieldTemplateReference=' + fieldTemplateReference
                 + '&pjaxName=' + pjaxContainerOwner
                 + '&modalName=' + modalOwner,
            container: pjaxContainerId,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $('.field-item-modal').on('click', function() {

        $(pjaxContainer).data('returnUrlFields', $(fieldsListModal).data('returnUrlFields'));

        var fieldTemplate = $(this).data('field-template-id');

        updateModalDependent(fieldTemplate);
    });

    $('.field-arrow-up-modal').on('click', function() {
        $.pjax({
            url: fieldTemplateUpDependentUrl
                 + '?fieldTemplateId=' + $(this).data('fieldTemplateId')
                 + '&pjaxName=' + pjaxContainerOwner
                 + '&modalName=' + modalOwner,
            container: pjaxContainerId,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $('.field-arrow-down-modal').on('click', function() {
        $.pjax({
            url: fieldTemplateDownDependentUrl
                 + '?fieldTemplateId=' + $(this).data('fieldTemplateId')
                 + '&pjaxName=' + pjaxContainerOwner
                 + '&modalName=' + modalOwner,
            container: pjaxContainerId,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    function updateModalDependent(fieldTemplateId) {
        $.pjax({
            url: updateModalDependentUrl
                 + '?fieldTemplateReference=' + fieldTemplateReference
                 + '&fieldTemplateId=' + fieldTemplateId
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

<div class="modal-content fields-list-modal"
     data-home-url="<?= \yii\helpers\Url::base() ?>"
     data-field-template-reference="<?= $fieldTemplateReference ?>"
     data-pjax-container-owner="<?= $pjaxName ?>"
     data-modal-owner="<?= $modalName ?>"
     data-return-url-fields="<?= \yii\helpers\Url::toRoute([
         '/common/dev-fields/update-fields-list-container-dependent',
         'fieldTemplateReference' => $fieldTemplateReference,
         'pjaxName' => $pjaxName,
         'modalName' => $modalName,
     ]) ?>"
>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title">
            Fields list
            <span class="glyphicon glyphicon-arrow-left fields-modal-list-back"
                  style="float: right;margin-right: 20px"></span>
        </h3>
    </div>
    <div class="modal-body">
        <button class="btn btn-primary add-new-field-button">
            Add new field
        </button>
        <hr>
        <?php if (isset($fieldTemplatesTranslatable)): ?>
            <div class="col-xs-12">
                <div class="row content-block-title">
                    <h4>Translatable fields:</h4>
                </div>
            </div>
            <?php foreach ($fieldTemplatesTranslatable as $fieldTemplate): ?>
                <div class="row list-items">
                    <div class="col-xs-9 list-title">
                        <p class="field-item-modal"
                           data-field-template="<?= $fieldTemplate->field_template_reference ?>"
                           data-field-template-id="<?= $fieldTemplate->id ?>"
                        >
                            <?= $fieldTemplate->program_name ?> (<?= $fieldTemplate->getTypeName() ?>)
                        </p>
                    </div>
                    <div class="col-xs-3 list-controls">
                        <?php if ($fieldTemplate->visible): ?>
                            <span class="glyphicon glyphicon-eye-open"></span>
                        <?php else: ?>
                            <span class="glyphicon glyphicon-eye-close"></span>
                        <?php endif; ?>
                        <?php if ($fieldTemplate->editable): ?>
                            <span class="glyphicon glyphicon-pencil"></span>
                        <?php endif; ?>
                        <?php if ($fieldTemplate->is_main): ?>
                            <span class="glyphicon glyphicon-tower"></span>
                        <?php endif; ?>
                        <?php if ($fieldTemplate->canUpOrder()): ?>
                            <span class="glyphicon field-arrow-up-modal glyphicon-arrow-up"
                                  data-field-template-id="<?= $fieldTemplate->id ?>"></span>
                        <?php endif; ?>
                        <?php if ($fieldTemplate->canDownOrder()): ?>
                            <span class="glyphicon field-arrow-down-modal glyphicon-arrow-down"
                                  data-field-template-id="<?= $fieldTemplate->id ?>"></span>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif ?>
        <?php if (isset($fieldTemplatesSingle)): ?>
            <div class="col-xs-12">
                <div class="row ">
                    <br>
                    <h4>Single fields:</h4>
                </div>
            </div>
            <?php foreach ($fieldTemplatesSingle as $fieldTemplate): ?>
                <div class="row list-items">
                    <div class="col-xs-9 list-title">
                        <p class="field-item-modal"
                           data-field-template="<?= $fieldTemplate->field_template_reference ?>"
                           data-field-template-id="<?= $fieldTemplate->id ?>"
                            >
                            <?= $fieldTemplate->program_name ?> (<?= $fieldTemplate->getTypeName() ?>)
                        </p>
                    </div>
                    <div class="col-xs-3 list-controls">
                        <?php if ($fieldTemplate->visible): ?>
                            <span class="glyphicon glyphicon-eye-open"></span>
                        <?php else: ?>
                            <span class="glyphicon glyphicon-eye-close"></span>
                        <?php endif; ?>
                        <?php if ($fieldTemplate->editable): ?>
                            <span class="glyphicon glyphicon-pencil"></span>
                        <?php endif; ?>
                        <?php if ($fieldTemplate->is_main): ?>
                            <span class="glyphicon glyphicon-tower"></span>
                        <?php endif; ?>
                        <?php if ($fieldTemplate->canUpOrder()): ?>
                            <span class="glyphicon field-arrow-up-modal glyphicon-arrow-up"
                                  data-field-template-id="<?= $fieldTemplate->id ?>"></span>
                        <?php endif; ?>
                        <?php if ($fieldTemplate->canDownOrder()): ?>
                            <span class="glyphicon field-arrow-down-modal glyphicon-arrow-down"
                                  data-field-template-id="<?= $fieldTemplate->id ?>"></span>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</div>
