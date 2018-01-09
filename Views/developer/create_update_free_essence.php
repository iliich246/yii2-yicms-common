<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsCommon\Assets\FieldsDevAsset;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\FreeEssences\FreeEssences;
use Iliich246\YicmsCommon\Fields\FieldsDevModalWidget;
use Iliich246\YicmsCommon\Assets\FilesDevAsset;
use Iliich246\YicmsCommon\Files\FilesDevModalWidget;

/* @var $this \yii\web\View */
/* @var $freeEssence FreeEssences */
/* @var $devFieldGroup \Iliich246\YicmsCommon\Fields\DevFieldsGroup */
/* @var $fieldTemplatesTranslatable FieldTemplate[] */
/* @var $fieldTemplatesSingle FieldTemplate[] */
/* @var $filesBlocks \Iliich246\YicmsCommon\Files\FilesBlock[] */
/* @var $devFilesGroup \Iliich246\YicmsCommon\Files\DevFilesGroup */
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

FieldsDevAsset::register($this);
//FilesDevAsset::register($this);

$jsFiles = <<<JS
;(function() {
    var addFile = $('.add-file-block');

    var homeUrl = $(addFile).data('homeUrl');
    var emptyModalUrl = homeUrl + '/common/dev-files/empty-modal';
    var loadModalUrl = homeUrl + '/common/dev-files/load-modal';
    var updateFileListUrl = homeUrl + '/common/dev-files/update-files-list-container';
    var fileTemplateUpUrl = homeUrl + '/common/dev-files/file-template-up-order';
    var filedTemplateDownUrl = homeUrl + '/common/dev-files/file-template-down-order';

    var fileTemplateReference = $(addFile).data('fileTemplateReference');
    var pjaxContainerName = '#' + $(addFile).data('pjaxContainerName');
    var pjaxFilesModalName = '#' + $(addFile).data('filesModalName');
    var imageLoaderScr = $(addFile).data('loaderImageSrc');

    $(pjaxContainerName).on('pjax:send', function() {
        $(pjaxFilesModalName)
            .find('.modal-content')
            .empty()
            .append('<img src="' + imageLoaderScr + '" style="text-align:center">');
    });

    $(pjaxContainerName).on('pjax:success', function(event) {

        var isValidatorResponse = !!($('.validator-response').length);

        if (isValidatorResponse) return loadModal($(addFile).data('currentSelectedFileTemplate'));

        if (!$(event.target).find('form').is('[data-yicms-saved]')) return false;

        $.pjax({
            url: updateFileListUrl + '?fileTemplateReference=' + fileTemplateReference,
            container: '#update-files-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });

        if (!isValidatorResponse)
            $(pjaxFilesModalName).modal('hide');
    });

    $(document).on('click', '.file-item p', function(event) {
        var fileTemplate = $(this).data('file-template-id');

        $(addFile).data('currentSelectedFileTemplate',fileTemplate);

        loadModal(fileTemplate);
    });

    $(addFile).on('click', function() {
        $.pjax({
            url: emptyModalUrl + '?fileTemplateReference=' + fileTemplateReference ,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.file-arrow-up', function() {
        $.pjax({
            url: fileTemplateUpUrl + '?fileTemplateId=' + $(this).data('fileTemplateId'),
            container: '#update-files-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.file-arrow-down', function() {
        $.pjax({
            url: filedTemplateDownUrl + '?fileTemplateId=' + $(this).data('fileTemplateId'),
            container: '#update-files-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    function loadModal(fileTemplate) {
        $.pjax({
            url: loadModalUrl + '?fileTemplateId=' + fileTemplate,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });

        $(pjaxFilesModalName).modal('show');
    }
})();
JS;

$this->registerJs($jsFiles, $this::POS_READY);

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
