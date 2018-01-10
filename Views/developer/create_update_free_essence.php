<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\FreeEssences\FreeEssences;
use Iliich246\YicmsCommon\Fields\FieldsDevModalWidget;
use Iliich246\YicmsCommon\Files\FilesDevModalWidget;
use Iliich246\YicmsCommon\Images\ImagesDevModalWidget;

/* @var $this \yii\web\View */
/* @var $freeEssence FreeEssences */
/* @var $devFieldGroup \Iliich246\YicmsCommon\Fields\DevFieldsGroup */
/* @var $fieldTemplatesTranslatable FieldTemplate[] */
/* @var $fieldTemplatesSingle FieldTemplate[] */
/* @var $filesBlocks \Iliich246\YicmsCommon\Files\FilesBlock[] */
/* @var $devFilesGroup \Iliich246\YicmsCommon\Files\DevFilesGroup */
/* @var $imagesBlocks \Iliich246\YicmsCommon\Images\ImagesBlock[] */
/* @var $devImagesGroup \Iliich246\YicmsCommon\Images\DevImagesGroup */
/* @var $success bool */

$js = <<<JS
;(function() {
    var pjaxContainer = $('#update-free-essence-container');

    $(pjaxContainer).on('pjax:success', function() {
        $(".alert").hide().slideDown(500).fadeTo(500, 1);

        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 3000);
    });

    $(pjaxContainer).on('pjax:error', function(xhr, textStatus) {
        bootbox.alert({
            size: 'large',
            title: "There are some error on ajax request!",
            message: textStatus.responseText,
            className: 'bootbox-error'
        });
    });
})();
JS;

$this->registerJs($js, $this::POS_READY);




$jsConditions = <<<JS
;(function() {
    var addCondition = $('.add-conditions-template');

    var homeUrl = $(addCondition).data('homeUrl');
    var emptyModalUrl = homeUrl + '/common/dev-conditions/empty-modal';
    var loadModalUrl = homeUrl + '/common/dev-conditions/load-modal';
    var updateConditionListUrl = homeUrl + '/common/dev-conditions/update-conditions-list-container';
    var conditionTemplateUpUrl = homeUrl + '/common/dev-conditions/condition-template-up-order';
    var conditionTemplateDownUrl = homeUrl + '/common/dev-conditions/condition-template-down-order';

    var conditionTemplateReference = $(addCondition).data('conditionTemplateReference');
    var pjaxContainerName = '#' + $(addCondition).data('pjaxContainerName');
    var pjaxConditionsModalName = '#' + $(addCondition).data('conditionsModalName');
    var imageLoaderScr = $(addCondition).data('loaderImageSrc');

    $(pjaxContainerName).on('pjax:send', function() {
        $(pjaxConditionsModalName)
            .find('.modal-content')
            .empty()
            .append('<img src="' + imageLoaderScr + '" style="text-align:center">');
    });

    $(pjaxContainerName).on('pjax:success', function(event) {

        var isValidatorResponse = !!($('.validator-response').length);

        if (isValidatorResponse) return loadModal($(addCondition).data('currentSelectedImageTemplate'));

        if (!$(event.target).find('form').is('[data-yicms-saved]')) return false;

        $.pjax({
            url: updateConditionListUrl + '?conditionTemplateReference=' + conditionTemplateReference,
            container: '#update-conditions-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });

        if (!isValidatorResponse)
            $(pjaxContainerName).modal('hide');
    });

    $(document).on('click', '.condition-item p', function(event) {
        var conditionTemplate = $(this).data('conditionTemplateId');

        $(addCondition).data('currentSelectedConditionTemplate',conditionTemplate);

        loadModal(conditionTemplate);
    });

    $(addCondition).on('click', function() {
        $.pjax({
            url: emptyModalUrl + '?imageTemplateReference=' + conditionTemplateReference ,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.condition-arrow-up', function() {
        $.pjax({
            url: conditionTemplateUpUrl + '?conditionTemplateId=' + $(this).data('conditionTemplateId'),
            container: '#update-conditions-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.condition-arrow-down', function() {
        $.pjax({
            url: conditionTemplateDownUrl + '?conditionTemplateId=' + $(this).data('conditionTemplateId'),
            container: '#update-conditions-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    function loadModal(conditionTemplate) {
        $.pjax({
            url: loadModalUrl + '?conditionTemplateId=' + conditionTemplate,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });

        $(pjaxConditionsModalName).modal('show');
    }
})();
JS;

$this->registerJs($jsConditions, $this::POS_READY);

?>

<div class="col-sm-9 content">
    <div class="row content-block content-header">
        <?php if ($freeEssence->scenario == FreeEssences::SCENARIO_CREATE): ?>
            <h1>Create free essence</h1>
        <?php else: ?>
            <h1>Update free essence</h1>
            <h2>IMPORTANT! Do not change free essences in production without serious reason!</h2>
        <?php endif; ?>
    </div>

    <div class="row content-block breadcrumbs">
        <a href="<?= Url::toRoute(['free-essences-list']) ?>"><span>Free essences list</span></a> <span> / </span>
        <?php if ($freeEssence->scenario == FreeEssences::SCENARIO_CREATE): ?>
            <span>Create free essence</span>
        <?php else: ?>
            <span>Update free essence</span>
        <?php endif; ?>
    </div>

    <div class="row content-block form-block">
        <div class="col-xs-12">
            <div class="content-block-title">
                <?php if ($freeEssence->scenario == FreeEssences::SCENARIO_CREATE): ?>
                    <h3>Create free essence</h3>
                <?php else: ?>
                    <h3>Update free essence</h3>
                <?php endif; ?>
            </div>
            <?php if ($freeEssence->scenario == FreeEssences::SCENARIO_UPDATE): ?>
                <div class="row control-buttons">
                    <div class="col-xs-12">

                        <a href="<?= Url::toRoute(['free-essence-translates', 'id' => $freeEssence->id]) ?>"
                           class="btn btn-primary">
                            Free essence name translates
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php Pjax::begin([
                'options' => [
                    'id' => 'update-free-essence-container',
                ]
            ]) ?>
            <?php $form = ActiveForm::begin([
                'id' => 'create-update-free-essence-form',
                'options' => [
                    'data-pjax' => true,
                ],
            ]);
            ?>

            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                    <strong>Success!</strong> Free essence data updated.
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($freeEssence, 'program_name') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($freeEssence, 'editable')->checkbox() ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($freeEssence, 'visible')->checkbox() ?>
                </div>
            </div>

            <div class="row control-buttons">
                <div class="col-xs-12">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                    <?= Html::resetButton('Cancel', ['class' => 'btn btn-default cancel-button']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
            <?php Pjax::end() ?>
        </div>
    </div>

    <?php if ($freeEssence->scenario == FreeEssences::SCENARIO_CREATE): return; endif; ?>

    <?= $this->render('/pjax/update-fields-list-container', [
        'fieldTemplateReference' => $freeEssence->getFieldTemplateReference(),
        'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
        'fieldTemplatesSingle' => $fieldTemplatesSingle
    ]) ?>

    <?= FieldsDevModalWidget::widget([
        'devFieldGroup' => $devFieldGroup,
        'action' => Url::toRoute(['/common/dev/update-free-essence', 'id' => $freeEssence->id])
    ])
    ?>

    <?= $this->render('/pjax/update-files-list-container', [
        'fileTemplateReference' => $freeEssence->getFileTemplateReference(),
        'filesBlocks' => $filesBlocks,
    ]) ?>

    <?= FilesDevModalWidget::widget([
        'devFilesGroup' => $devFilesGroup,
        'action' => Url::toRoute(['/common/dev/update-free-essence', 'id' => $freeEssence->id])
    ]) ?>

    <?= $this->render('/pjax/update-images-list-container', [
        'imageTemplateReference' => $freeEssence->getImageTemplateReference(),
        'imagesBlocks' => $imagesBlocks,
    ]) ?>

    <?= ImagesDevModalWidget::widget([
        'devImagesGroup' => $devImagesGroup,
        'action' => Url::toRoute(['/common/dev/update-free-essence', 'id' => $freeEssence->id])
    ]) ?>
