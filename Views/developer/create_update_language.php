<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Languages\LanguagesDb;

/* @var $this \yii\web\View */
/* @var $model Iliich246\YicmsCommon\Languages\LanguagesDb */

if ($model->scenario == LanguagesDb::SCENARIO_CREATE)
    $this->title = 'Create new language';
else
    $this->title = 'Update language';

$js = <<<JS
;(function(){
    var pjaxContainer = $('#pjax-language-create-update-container');
    var pjaxDeleteContainer = $('#pjax-language-delete-container');

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

    $(pjaxDeleteContainer).on('pjax:error', function(xhr, textStatus) {
        bootbox.alert({
            size: 'large',
            title: "There are some error on ajax request!",
            message: textStatus.responseText,
            className: 'bootbox-error'
        });
    });
})();
JS;

$this->registerJs($js);
?>

<div class="col-sm-9 content">
    <div class="row content-block content-header">
        <?php if ($model->scenario == LanguagesDb::SCENARIO_CREATE): ?>
            <h1>Create new language page</h1>
        <?php else: ?>
            <h1>Edit language page</h1>
        <?php endif; ?>
    </div>

    <div class="row content-block breadcrumbs">
        <a href="<?= Url::toRoute(['languages-list']) ?>"><span>Languages list</span></a> <span> / </span>
        <?php if ($model->scenario == LanguagesDb::SCENARIO_CREATE): ?>
            <span>Create language</span>
        <?php else: ?>
            <span>Update language</span>
        <?php endif; ?>
    </div>

    <div class="row content-block form-block">
        <div class="col-xs-12">
            <div class="content-block-title">
                <?php if ($model->scenario == LanguagesDb::SCENARIO_CREATE): ?>
                    <h3>Create language</h3>
                <?php else: ?>
                    <h3>Update language</h3>
                <?php endif; ?>
            </div>
            <?php Pjax::begin([
                'options' => [
                    'id' => 'pjax-language-create-update-container',
                ],
                'enablePushState'    => false,
                'enableReplaceState' => false
            ]); ?>
            <?php $form = ActiveForm::begin([
                'id' => 'create-update-language-form',
                'options' => [
                    'data-pjax' => true,
                ],
            ]); ?>

            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                    <strong>Success!</strong> Language updated.
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($model, 'name') ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($model, 'code') ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($model, 'used')->checkbox() ?>
                </div>
            </div>

            <?php if ($model->scenario == LanguagesDb::SCENARIO_UPDATE): ?>

                <p>
                    You can delete only newly created languages. If language table has constraints, he can`t be deleted.
                    In this situation you can deactivate language, and admin and users will not see him.
                </p>

                <?php Pjax::begin([
                    'options' => [
                        'id' => 'pjax-language-delete-container',
                    ]
                ]); ?>

                <?= \Iliich246\YicmsCommon\Widgets\LinkWithConfirmationWidget::widget([
                    'url' => Url::toRoute(['delete-language', 'id' => $model->id]),
                    'title' => 'Delete language \'' . $model->name . '\'',
                    'caption' => 'Delete Language',
                    'message' => 'This action will try to delete language record from db. All files with translates will not be deleted.',
                    'withBlock' => true,
                    'viaPjax' => true,
                    'pjaxContainer' => 'pjax-language-delete-container',
                ]) ?>

                <?php Pjax::end() ?>

            <?php endif; ?>

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
</div>
