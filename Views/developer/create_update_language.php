<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Languages\LanguagesDb;

/* @var $this \yii\web\View */
/* @var $model Iliich246\YicmsCommon\Languages\LanguagesDb */

?>

<div class="col-sm-9 content">
    <div class="row content-block content-header">
        <?php if ($model->scenario == LanguagesDb::SCENARIO_CREATE): ?>
            <h1>Create new language</h1>
        <?php else: ?>
            <h1>Edit language</h1>
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
            <?php $pjax1 = Pjax::begin(); ?>
            <?php $form = ActiveForm::begin([
                'id' => 'create-language-form',
                'options' => [
                    'data-pjax' => true,
                ],
            ]);
            ?>
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
                In this situation  you can deactivate language, and admin and users will not see him.
            </p>


            <!--            --><?php //if ($model->scenario == LanguagesModel::SCENARIO_UPDATE): ?>
            <!--                --><? //= DeleteButtonWidget::widget([
            //                    'url' => Url::toRoute(['delete-language', 'id' => $model->getLanguageDb()->id]),
            //                    'title' => 'Delete language \'' . $model->name . '\'',
            //                    'caption' => 'Delete Language',
            //                    'message' => 'This action will delete language record from db. All files with translates will not be deleted.',
            //                    'withBlock' => true,
            //                ]) ?>

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