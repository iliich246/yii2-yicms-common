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
//$deleteLink = $widget->deleteLink . '?fieldTemplateId=';

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
                </div>
                <div class="row">
                    <div class="col-sm-4 col-xs-12 ">
                        <?= $form->field($widget->devFilesGroup->filesBlock, 'visible')->checkbox() ?>
                    </div>
                    <div class="col-sm-4 col-xs-12 ">
                        <?= $form->field($widget->devFilesGroup->filesBlock, 'editable')->checkbox() ?>
                    </div>
                    <div class="col-sm-4 col-xs-12 ">
                        <?= $form->field($widget->devFilesGroup->filesBlock, 'createStandardFields')->checkbox() ?>
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
                                    id="field-delete"
<!--                                    data-field-template-reference="--><?//= $widget->devFilesGroup->fieldTemplate->field_template_reference ?><!--"-->
<!--                                    data-field-template-id="--><?//= $widget->devFieldGroup->fieldTemplate->id ?><!--">-->
                                >
                                Delete field
                            </button>
                        </div>
                    </div>
                    <hr>

<!--                    --><?//= ValidatorsListWidget::widget([
//                        'validatorReference' => $widget->devFieldGroup->fieldTemplate,
//                        'ownerPjaxContainerName' => FieldsDevModalWidget::getPjaxContainerId(),
//                        'returnUrl' => \yii\helpers\Url::toRoute([
//                            '/common/dev-fields/load-modal',
//                            'fieldTemplateId' => $widget->devFieldGroup->fieldTemplate->id,
//                        ])
//                    ]) ?>

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

