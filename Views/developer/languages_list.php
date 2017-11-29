<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use yii\widgets\Pjax;

/* @var $this \yii\web\View */
/* @var $languages LanguagesDb[] */
/* @var $defaultLanguageModel \Iliich246\YicmsCommon\Languages\DefaultLanguageForm */
/* @var $success bool */
$this->title = 'Languages list';

$js = <<<EOL
;(function(){
    var pjaxContainer = $('#pjax-language-config-container');

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

        console.log(textStatus);
    });
})();
EOL;

$this->registerJs($js);

?>
<div class="col-sm-9 content">
    <div class="row content-block content-header">
        <h1>List of languages</h1>

        <h2>List of all languages, existing in system</h2>
    </div>

    <div class="row content-block form-block">
        <div class="col-xs-12">
            <div class="content-block-title">
                <h3>Language settings</h3>
            </div>
            <?php Pjax::begin([
                'options' => [
                    'id' => 'pjax-language-config-container',
                ]
            ]); ?>
            <?php $form = ActiveForm::begin([
                'id' => 'set-language-config-form',
                'options' => [
                    'data-pjax' => true,
                ],
            ]); ?>

            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                    <strong>Success!</strong> Language configuration updated.
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($defaultLanguageModel, 'defaultLanguage')->dropDownList(
                        $defaultLanguageModel->getLanguagesList()
                    )
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($defaultLanguageModel, 'languageMethod')->dropDownList(
                        $defaultLanguageModel->getLanguagesMethodList()
                    )
                    ?>
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

    <div class="row content-block">
        <h3>List of languages</h3>
        <div class="col-xs-12">
            <div class="row control-buttons">
                <div class="col-xs-12">
                    <a href="<?= Url::toRoute(['create-language']) ?>" class="btn btn-primary">
                        Create new language
                    </a>
                </div>
            </div>

            <div class="list-block">
                <?php foreach ($languages as $language): ?>
                    <div class="row list-items">
                        <div class="col-xs-10 list-title">
                            <a href="<?= Url::toRoute(['update-language', 'id' => $language->id]) ?>">
                                <p>
                                    <?= $language->name ?>
                                </p>
                            </a>
                        </div>
                        <div class="col-xs-2 list-controls">
                            <?php if ($language->used): ?>
                                <span class="glyphicon glyphicon-eye-open"></span>
                            <?php else: ?>
                                <span class="glyphicon glyphicon-eye-close"></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
