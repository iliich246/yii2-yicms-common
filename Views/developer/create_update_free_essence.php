<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsCommon\Assets\FieldsDevAsset;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\FreeEssences\FreeEssences;
use Iliich246\YicmsCommon\Fields\FieldsDevModalWidget;

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

    <?= \Iliich246\YicmsCommon\Files\FilesDevModalWidget::widget([
        'devFilesGroup' => $devFilesGroup,
        'action' => Url::toRoute(['/common/dev/update-free-essence', 'id' => $freeEssence->id])
    ]) ?>


