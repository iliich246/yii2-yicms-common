<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Base\CommonHashForm;

/* @var $this \yii\web\View */
/* @var $model CommonHashForm */

    if ($model->scenario == CommonHashForm::SCENARIO_CHANGE_DEV)
        $this->title = 'Change developer hash page';
    else
        $this->title = 'Change admin hash page';
?>

<div class="col-sm-9 content">
    <div class="row content-block content-header">
        <?php if ($model->scenario == CommonHashForm::SCENARIO_CHANGE_DEV): ?>
            <h1>Change developer hash page</h1>
        <?php else: ?>
            <h1>Change admin hash page</h1>
        <?php endif; ?>
    </div>

    <div class="row content-block form-block">
        <div class="col-xs-12">
            <div class="content-block-title">
                <?php if ($model->scenario == CommonHashForm::SCENARIO_CHANGE_DEV): ?>
                    <h3>Change developer hash</h3>
                <?php else: ?>
                    <h3>Change admin hash</h3>
                <?php endif; ?>
            </div>
            <?php $pjax = Pjax::begin() ?>
            <?php $form = ActiveForm::begin([
                'id' => 'change-hash-form',
            ]);
            ?>

            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">?</span></button>
                    <strong>Success!</strong> Language updated.
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($model, 'hash') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($model, 'confirmHash') ?>
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
</div>
