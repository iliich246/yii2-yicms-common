<?php

use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\FieldsDevModalWidget;

/* @var $this \yii\web\View */
/* @var $fieldTemplateReference integer */
/* @var $fieldTemplatesTranslatable FieldTemplate[] */
/* @var $fieldTemplatesSingle FieldTemplate[] */
/* @var $isInModal bool */

$bundle = \Iliich246\YicmsCommon\Assets\DeveloperAsset::register($this);
$src = $bundle->baseUrl . '/loader.svg';

$js = <<<JS
;(function() {
    var addField = $('.add-field');

    var homeUrl = $(addField).data('homeUrl');
    var emptyModalUrl = homeUrl + '/common/dev-fields/empty-modal';
    var loadModalUrl = homeUrl + '/common/dev-fields/load-modal';
    var updateFieldsListUrl = homeUrl + '/common/dev-fields/update-fields-list-container';
    var fieldTemplateUpUrl = homeUrl + '/common/dev-fields/field-template-up-order';
    var fieldTemplateDownUrl = homeUrl + '/common/dev-fields/field-template-down-order';

    var fieldTemplateReference = $(addField).data('fieldTemplateReference');
    var pjaxContainerName = '#' + $(addField).data('pjaxContainerName');
    var pjaxFieldsModalName = '#' + $(addField).data('fieldsModalName');
    var imageLoaderScr = $(addField).data('loaderImageSrc');

    $(pjaxContainerName).on('pjax:send', function() {
        $(pjaxFieldsModalName)
            .find('.modal-content')
            .empty()
            .append('<img src="' + imageLoaderScr + '" style="text-align:center">');
    });

    $(pjaxContainerName).on('pjax:success', function(event) {

        var isValidatorResponse = !!($('.validator-response').length);

        if (isValidatorResponse) return loadModal($(addField).data('currentSelectedFieldTemplate'));

        if (!$(event.target).find('form').is('[data-yicms-saved]')) return false;

        $.pjax({
            url: updateFieldsListUrl + '?fieldTemplateReference=' + fieldTemplateReference,
            container: '#update-fields-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });

        if (!isValidatorResponse)
            $(pjaxFieldsModalName).modal('hide');
    });

    $(document).on('click', '.field-item p', function(event) {
        var fieldTemplate = $(this).data('field-template-id');

        $(addField).data('currentSelectedFieldTemplate',fieldTemplate);

        loadModal(fieldTemplate);
    });

    $(addField).on('click', function() {
        $.pjax({
            url: emptyModalUrl + '?fieldTemplateReference=' + fieldTemplateReference ,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.field-arrow-up', function() {
        $.pjax({
            url: fieldTemplateUpUrl + '?fieldTemplateId=' + $(this).data('fieldTemplateId'),
            container: '#update-fields-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.field-arrow-down', function() {
        $.pjax({
            url: fieldTemplateDownUrl + '?fieldTemplateId=' + $(this).data('fieldTemplateId'),
            container: '#update-fields-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    function loadModal(fieldTemplate) {
        $.pjax({
            url: loadModalUrl + '?fieldTemplateId=' + fieldTemplate,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });

        $(pjaxFieldsModalName).modal('show');
    }
})();

JS;

//$this->registerJs($js, $this::POS_READY);

?>

<div class="row <?php if (!isset($isInModal)): ?>content-block form-block <?php endif; ?>">
    <div class="col-xs-12">
        <div class="content-block-title">
            <h3>List of fields</h3>
        </div>
        <div class="row control-buttons">
            <div class="col-xs-12">
                <button class="btn btn-primary add-field"
                        data-toggle="modal"
                        data-target="#fieldsDevModal"
                        data-field-template-reference="<?= $fieldTemplateReference ?>"
                        data-home-url="<?= \yii\helpers\Url::base() ?>"
                        data-pjax-container-name="<?= FieldsDevModalWidget::getPjaxContainerId() ?>"
                        data-fields-modal-name="<?= FieldsDevModalWidget::getModalWindowName() ?>"
                        data-loader-image-src="<?= $src ?>"
                        data-current-selected-field-template="null"
                    >
                    <span class="glyphicon glyphicon-plus-sign"></span> Add new field
                </button>
            </div>
        </div>
        <?php if (isset($fieldTemplatesTranslatable) || isset($fieldTemplatesSingle)): ?>
            <?php Pjax::begin([
                'options' => [
                    'id' => 'update-fields-list-container'
                ]
            ]) ?>
            <div class="list-block">
                <?php if (isset($fieldTemplatesTranslatable)): ?>
                    <div class="row content-block-title">
                        <h4>Translatable fields:</h4>
                    </div>

                    <?php foreach ($fieldTemplatesTranslatable as $fieldTemplate): ?>
                        <div class="row list-items field-item">
                            <div class="col-xs-10 list-title">
                                <p data-field-template-id="<?= $fieldTemplate->id ?>">
                                    <?= $fieldTemplate->program_name ?> (<?= $fieldTemplate->getTypeName() ?>)
                                </p>
                            </div>
                            <div class="col-xs-2 list-controls">
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
                                    <span class="glyphicon field-arrow-up glyphicon-arrow-up"
                                          data-field-template-id="<?= $fieldTemplate->id ?>"></span>
                                <?php endif; ?>
                                <?php if ($fieldTemplate->canDownOrder()): ?>
                                    <span class="glyphicon field-arrow-down glyphicon-arrow-down"
                                          data-field-template-id="<?= $fieldTemplate->id ?>"></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif ?>
                <?php if (isset($fieldTemplatesSingle)): ?>
                    <div class="row content-block-title">
                        <br>
                        <h4>Single fields:</h4>
                    </div>
                    <?php foreach ($fieldTemplatesSingle as $fieldTemplate): ?>
                        <div class="row list-items field-item">
                            <div class="col-xs-10 list-title">
                                <p data-field-template="<?= $fieldTemplate->field_template_reference ?>"
                                   data-field-template-id="<?= $fieldTemplate->id ?>"
                                    >
                                    <?= $fieldTemplate->program_name ?> (<?= $fieldTemplate->getTypeName() ?>)
                                </p>
                            </div>
                            <div class="col-xs-2 list-controls">
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
                                <span class="glyphicon field-arrow-up glyphicon-arrow-up"
                                      data-field-template-id="<?= $fieldTemplate->id ?>"></span>
                                <?php endif; ?>
                                <?php if ($fieldTemplate->canDownOrder()): ?>
                                    <span class="glyphicon field-arrow-down glyphicon-arrow-down"
                                          data-field-template-id="<?= $fieldTemplate->id ?>"></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php Pjax::end() ?>
        <?php endif; ?>
    </div>
</div>
