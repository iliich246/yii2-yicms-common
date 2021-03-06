<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsCommon\Files\FilesDevModalWidget;
use Iliich246\YicmsCommon\Files\DevFilesGroup;
use Iliich246\YicmsCommon\Files\FilesBlock;
use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;
use Iliich246\YicmsCommon\Validators\ValidatorsListWidget;

/** @var $this \yii\web\View */
/** @var $widget FilesDevModalWidget */
/** @var $bundle \Iliich246\YicmsCommon\Assets\DeveloperAsset */

$bundle = \Iliich246\YicmsCommon\Assets\DeveloperAsset::register($this);

$modalName = FilesDevModalWidget::getModalWindowName();
$deleteLink = $widget->deleteLink . '?fileTemplateId=';

$js = <<<JS
;(function() {
    $(document).on('click', '#file-delete', function() {
        var button = ('#file-delete');

        if (!$(button).is('[data-file-template-id]')) return;

        var fileTemplateId          = $(button).data('fileTemplateId');
        var fileBlockHasConstraints = $(button).data('fileBlockHasConstraints');
        var pjaxContainer           = $('#update-files-list-container');

        if (!($(this).hasClass('file-confirm-state'))) {
            $(this).before('<span>Are you sure? </span>');
            $(this).text('Yes, I`am sure!');
            $(this).addClass('file-confirm-state');
        } else {
            if (!fileBlockHasConstraints) {
                $.pjax({
                    url: '{$deleteLink}' + fileTemplateId,
                    container: '#update-files-list-container',
                    scrollTo: false,
                    push: false,
                    type: "POST",
                    timeout: 2500
                });

                var deleteActive = true;

                $(pjaxContainer).on('pjax:success', function(event) {

                    if (!deleteActive) return false;

                    $('#{$modalName}').modal('hide');
                    deleteActive = false;
                });
            } else {
                var deleteButtonRow = $('.delete-button-row-files');

                var template = _.template($('#delete-with-pass-template').html());
                $(deleteButtonRow).empty();
                $(deleteButtonRow).append(template);

                var passwordInput = $('#file-block-delete-password-input');
                var buttonDelete  = $('#file-button-delete-with-pass');

                $(buttonDelete).on('click', function() {
                    $.pjax({
                        url: '{$deleteLink}' + fileTemplateId + '&deletePass=' + $(passwordInput).val(),
                        container: '#update-files-list-container',
                        scrollTo: false,
                        push: false,
                        type: "POST",
                        timeout: 2500
                    });

                    var deleteActive = true;

                    $(pjaxContainer).on('pjax:success', function(event) {

                        if (!deleteActive) return false;

                        $('#{$modalName}').modal('hide');
                        deleteActive = false;
                    });

                    $(pjaxContainer).on('pjax:error', function(event) {

                         $('#{$modalName}').modal('hide');

                         bootbox.alert({
                             size: 'large',
                             title: "Wrong dev password",
                             message: "Files block template has not deleted",
                             className: 'bootbox-error'
                         });
                    });
                });

                $('#{$modalName}').on('hide.bs.modal', function() {
                    $(pjaxContainer).off('pjax:error');
                    $(pjaxContainer).off('pjax:success');
                    $('#{$modalName}').off('hide.bs.modal');
                });
            }
        }
    });
})();
JS;

$this->registerJs($js, $this::POS_READY);

$this->registerAssetBundle(\Iliich246\YicmsCommon\Assets\LodashAsset::className());

if ($widget->devFilesGroup->scenario == DevFilesGroup::SCENARIO_CREATE &&
    $widget->devFilesGroup->justSaved)
    $redirectToUpdate = 'true';
else
    $redirectToUpdate = 'false';

if ($redirectToUpdate == 'true')
    $fileBlockIdForRedirect = $widget->devFilesGroup->filesBlock->id;
else
    $fileBlockIdForRedirect = '0';

?>

<div class="modal fade"
     id="<?= FilesDevModalWidget::getModalWindowName() ?>"
     tabindex="-1"
     role="dialog"
     data-backdrop="static"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <?php Pjax::begin([
            'options' => [
                'id'                               => FilesDevModalWidget::getPjaxContainerId(),
                'class'                            => 'pjax-container',
                'data-return-url'                  => '0',
                'data-return-url-validators'       => '0',
                'data-return-url-fields'           => '0',
                'data-return-url-conditions'       => '0',
                'data-return-url-conditions-list'  => '0',
                'data-return-url-conditions-value' => '0',
                'data-annotate-url'                => '0'
            ],
        ]); ?>
        <?php $form = ActiveForm::begin([
            'id'      => FilesDevModalWidget::getFormName(),
            'action'  => $widget->action,
            'options' => [
                'data-pjax'                    => true,
                'data-yicms-saved'             => $widget->dataSaved,
                'data-save-and-exit'           => $widget->saveAndExit,
                'data-redirect-to-update-file' => $redirectToUpdate,
                'data-file-block-id-redirect'  => $fileBlockIdForRedirect
            ],
        ]);
        ?>

        <?php if ($widget->devFilesGroup->scenario == DevFilesGroup::SCENARIO_UPDATE): ?>
            <?= Html::hiddenInput('_fileTemplateId', $widget->devFilesGroup->filesBlock->id, [
                'id' => 'file-template-id-hidden'
            ]) ?>
        <?php endif; ?>

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="myModalLabel">
                    <?php if ($widget->devFilesGroup->scenario == DevFilesGroup::SCENARIO_CREATE): ?>
                        Create new file block template
                    <?php else: ?>
                        Update existed file block template (<?= $widget->devFilesGroup->filesBlock->program_name ?>)
                    <?php endif; ?>
                </h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4 col-xs-12">
                        <?= $form->field($widget->devFilesGroup->filesBlock, 'program_name') ?>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <?= $form->field($widget->devFilesGroup->filesBlock, 'type')->dropDownList(
                            FilesBlock::getTypes())
                        ?>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <?= $form->field($widget->devFilesGroup->filesBlock, 'language_type')->dropDownList(
                            FilesBlock::getLanguageTypes())
                        ?>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <?= $form->field($widget->devFilesGroup->filesBlock, 'max_files') ?>
                    </div>
                    <div class="col-sm-8 col-xs-12">
                        <br>
                        <p>zero value - infinite count of files in block</p>
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-4 col-xs-12 ">
                        <?= $form->field($widget->devFilesGroup->filesBlock, 'visible')->checkbox() ?>
                    </div>
                    <div class="col-sm-4 col-xs-12 ">
                        <?= $form->field($widget->devFilesGroup->filesBlock, 'editable')->checkbox() ?>
                    </div>
                </div>
                <?= SimpleTabsTranslatesWidget::widget([
                    'form'            => $form,
                    'translateModels' => $widget->devFilesGroup->filesNameTranslates,
                ])
                ?>
                <?php if ($widget->devFilesGroup->scenario == DevFilesGroup::SCENARIO_UPDATE): ?>
                    <div class="row delete-button-row-files">
                        <div class="col-xs-12">

                            <br>

                            <p>IMPORTANT! Do not delete file blocks without serious reason!</p>
                            <button type="button"
                                    class="btn btn-danger"
                                    id="file-delete"
                                    data-file-template-reference="<?= $widget->devFilesGroup->filesBlock->file_template_reference ?>"
                                    data-file-template-id="<?= $widget->devFilesGroup->filesBlock->id ?>"
                                    data-file-block-has-constraints="<?= (int)$widget->devFilesGroup->filesBlock->isConstraints() ?>"
                                >
                                Delete file block template
                            </button>
                        </div>
                    </div>
                    <script type="text/template" id="delete-with-pass-template">
                        <div class="col-xs-12">
                            <br>
                            <label for="file-block-delete-password-input">
                                File block has constraints. Enter dev password for delete file block template
                            </label>
                            <input type="password"
                                   id="file-block-delete-password-input"
                                   class="form-control" name=""
                                   value=""
                                   aria-required="true"
                                   aria-invalid="false">
                            <br>
                            <button type="button"
                                    class="btn btn-danger"
                                    id="file-button-delete-with-pass"
                                >
                                Yes, i am absolutely seriously!!!
                            </button>
                        </div>
                    </script>
                    <hr>

                    <span class="btn btn-primary view-files-block-fields"
                       data-field-template-id="<?= $widget->devFilesGroup->filesBlock->getFieldTemplateReference()  ?>"
                       data-return-url="<?= \yii\helpers\Url::toRoute([
                           '/common/dev-files/load-modal',
                           'fileTemplateId' => $widget->devFilesGroup->filesBlock->id,
                       ]) ?>"
                    >
                        View file block fields

                    </span>

                    <span class="btn btn-primary view-files-block-conditions"
                        data-condition-template-id="<?= $widget->devFilesGroup->filesBlock->getConditionTemplateReference() ?>"
                        data-return-url="<?= \yii\helpers\Url::toRoute([
                            '/common/dev-files/load-modal',
                            'fileTemplateId' => $widget->devFilesGroup->filesBlock->id,
                        ]) ?>"
                    >
                        View file block conditions
                    </span>

                    <hr>

                    <?= ValidatorsListWidget::widget([
                        'validatorReference' => $widget->devFilesGroup->filesBlock,
                        'ownerPjaxContainerName' => FilesDevModalWidget::getPjaxContainerId(),
                        'ownerModalId' => FilesDevModalWidget::getModalWindowName(),
                        'returnUrl' => \yii\helpers\Url::toRoute([
                            '/common/dev-files/load-modal',
                            'fileTemplateId' => $widget->devFilesGroup->filesBlock->id,
                        ])
                    ]) ?>

                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                <?= Html::submitButton('Save and exit', ['class' => 'btn btn-success',
                    'value' => 'true', 'name' => '_saveAndExit']) ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        <?php Pjax::end() ?>
    </div>
</div>
