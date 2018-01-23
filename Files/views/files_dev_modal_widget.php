<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Files\FilesDevModalWidget;
use Iliich246\YicmsCommon\Files\DevFilesGroup;
use Iliich246\YicmsCommon\Files\FilesBlock;
use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;
use Iliich246\YicmsCommon\Validators\ValidatorsListWidget;

/** @var $widget FilesDevModalWidget */
/** @var \Iliich246\YicmsCommon\Assets\DeveloperAsset $bundle */

$bundle = \Iliich246\YicmsCommon\Assets\DeveloperAsset::register($this);

$modalName = FilesDevModalWidget::getModalWindowName();
$deleteLink = $widget->deleteLink . '?fileTemplateId=';

$js = <<<JS
;(function() {
    $(document).on('click', '#file-delete', function() {
        var button = ('#file-delete');

        if (!$(button).is('[data-file-template-id]')) return;

        var fileTemplateId = $(button).data('fileTemplateId');

        if (!($(this).hasClass('file-confirm-state'))) {
            $(this).before('<span>Are you sure? </span>');
            $(this).text('Yes, I`am sure!');
            $(this).addClass('file-confirm-state');
        } else {
            $.pjax({
                url: '{$deleteLink}' + fileTemplateId,
                container: '#update-files-list-container',
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500
            });

            var deleteActived = true;

            $('#update-files-list-container').on('pjax:success', function(event) {

                if (!deleteActived) return false;

                $('#{$modalName}').modal('hide');
                deleteActived = false;
            });
        }
    });
})();
JS;

$this->registerJs($js, $this::POS_READY);

?>

<div class="modal fade"
     id="<?= FilesDevModalWidget::getModalWindowName() ?>"
     tabindex="-1"
     role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <?php Pjax::begin([
            'options' => [
                'id' => FilesDevModalWidget::getPjaxContainerId(),
                'class' => 'pjax-container',
                'data-return-url' => '0',
            ],
        ]); ?>
        <?php $form = ActiveForm::begin([
            'id' => FilesDevModalWidget::getFormName(),
            'action' => $widget->action,
            'options' => [
                'data-pjax' => true,
                'data-yicms-saved' => $widget->dataSaved,
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
                    'form' => $form,
                    'translateModels' => $widget->devFilesGroup->filesNameTranslates,
                ])
                ?>
                <?php if ($widget->devFilesGroup->scenario == DevFilesGroup::SCENARIO_UPDATE): ?>
                    <div class="row">
                        <div class="col-xs-12">

                            <br>

                            <p>IMPORTANT! Do not delete file blocks without serious reason!</p>
                            <button type="button"
                                    class="btn btn-danger"
                                    id="file-delete"
                                    data-file-template-reference="<?= $widget->devFilesGroup->filesBlock->file_template_reference ?>"
                                    data-file-template-id="<?= $widget->devFilesGroup->filesBlock->id ?>">
                                Delete file block template
                            </button>
                        </div>
                    </div>
                    <hr>

                    <a href="<?= \yii\helpers\Url::toRoute([
                        '/common/dev-files/show-file-block-fields',
                        'fileTemplateId' => $widget->devFilesGroup->filesBlock->id
                    ]) ?>"
                       class="btn btn-primary">
                        View file block fields
                    </a>

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
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        <?php Pjax::end() ?>
    </div>
</div>
