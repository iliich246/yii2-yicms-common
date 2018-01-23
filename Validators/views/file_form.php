<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsCommon\Validators\FileValidatorForm;
use Iliich246\YicmsCommon\Languages\Language;

/** @var $this \yii\web\View */
/** @var $validatorForm FileValidatorForm */
/** @var $pjaxContainer string */
/** @var $returnBack boolean */

if (isset($returnBack)) $return = 'true';
else $return = 'false';

$js = <<<JS
    ;(function() {
        var validatorForm = $('.validator-form');
        var deleteButton = $('#validator-delete');

        var pjaxContainer = $(validatorForm).parent('.pjax-container');
        var pjaxContainerId = '#' + $(pjaxContainer).attr('id');
        var validatorId = $(validatorForm).data('validatorId');

        var homeUrl = $(validatorForm).data('homeUrl');
        var returnUrl = $(pjaxContainer).data('returnUrl');
        var deleteUrl = homeUrl + '/common/dev-validators/delete-validator?';

        var isReturn = $(validatorForm).data('returnBack');

        var backButton = $('.validator-form-back');

        if (isReturn) goBack();

        $(backButton).on('click', goBack);

        $(deleteButton).on('click', function() {
            if (!($(this).hasClass('validator-confirm-state'))) {
                $(this).before('<span>Are you sure? </span>');
                $(this).text('Yes, I`am sure!');
                $(this).addClass('validator-confirm-state');
            } else {
                $.ajax({
                    type: 'POST',
                    url: deleteUrl + 'id=' + validatorId,
                    success: goBack,
                    error: function(err) {
                        bootbox.alert({
                            size: 'large',
                            title: "Ajax error",
                            message: 'There are some error on ajax request!',
                            className: 'bootbox-error'
                        });
                    }
                })
            }
        });

        function goBack() {
            $.pjax({
                url: returnUrl,
                container: pjaxContainerId,
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500,
            });
        }
    })();
JS;

$this->registerJs($js);

?>

<?php $form = ActiveForm::begin([
    'id' => 'number-validator-form',
    'options' => [
        'class' => 'validator-form',
        'data-pjax' => true,
        'data-home-url' => \yii\helpers\Url::base(),
        'data-return-back' => $return,
        'data-validator-id' => $validatorForm->getValidatorDb()->id,
    ],
]);
?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title">
            File validator

            <span class="glyphicon glyphicon-arrow-left validator-form-back" aria-hidden="true" style="float: right;margin-right: 20px"></span>

        </h3>
    </div>

    <div class="modal-body">

        <?= $form->field($validatorForm, 'isActivate')->checkbox() ?>

        <?= $form->field($validatorForm, 'maxSize') ?>

        <?= $form->field($validatorForm, 'minSize') ?>

        <?= $form->field($validatorForm, 'mimeTypes') ?>

        <?= $form->field($validatorForm, 'extensions') ?>

        <?php foreach(Language::getInstance()->usedLanguages() as $language): ?>
            <?= $form->field($validatorForm, "message['$language->code']")
                ->label("Message for language $language->name") ?>
        <?php endforeach; ?>

        <?php foreach(Language::getInstance()->usedLanguages() as $language): ?>
            <?= $form->field($validatorForm, "tooBig['$language->code']")
                ->label("Too big error message for language $language->name") ?>
        <?php endforeach; ?>

        <?php foreach(Language::getInstance()->usedLanguages() as $language): ?>
            <?= $form->field($validatorForm, "tooSmall['$language->code']")
                ->label("Too small error message for language $language->name") ?>
        <?php endforeach; ?>

        <?php foreach(Language::getInstance()->usedLanguages() as $language): ?>
            <?= $form->field($validatorForm, "wrongExtension['$language->code']")
                ->label("Wrong extension error message for language $language->name") ?>
        <?php endforeach; ?>

        <?php foreach(Language::getInstance()->usedLanguages() as $language): ?>
            <?= $form->field($validatorForm, "wrongMimeType['$language->code']")
                ->label("Wrong mime type error message for language $language->name") ?>
        <?php endforeach; ?>


        <button type="button"
                class="btn btn-danger"
                id="validator-delete">
            Delete validator
        </button>

    </div>

    <div class="modal-footer">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::submitButton('Save and back',
            ['class' => 'btn btn-success',
                'value' => 'true', 'name' => '_saveAndBack']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</div>
<?php ActiveForm::end(); ?>
