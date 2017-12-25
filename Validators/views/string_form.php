<?php
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsCommon\Validators\StringValidatorForm;
use Iliich246\YicmsCommon\Languages\Language;

/** @var $this \yii\web\View */
/** @var $validatorForm StringValidatorForm */
?>
<?php $form = ActiveForm::begin([
    'id' => 'require-string-form',
    'options' => [
        'data-pjax' => true,
    ],
]);
?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="myModalLabel">
            String validator
        </h3>
    </div>
    <div class="modal-body">
        <?= $form->field($validatorForm, 'isActivate')->checkbox() ?>

        <p>In string you can place next placeholders: {attribute}, {value}</p>

        <?php foreach(Language::getInstance()->usedLanguages() as $language): ?>
            <?= $form->field($validatorForm, "message['$language->code']")
                ->label("Message for language $language->name") ?>
        <?php endforeach; ?>

        <?= $form->field($validatorForm, 'max') ?>

        <?php foreach(Language::getInstance()->usedLanguages() as $language): ?>
            <?= $form->field($validatorForm, "tooLong['$language->code']")
                ->label("TooLong message for language $language->name") ?>
        <?php endforeach; ?>

        <?= $form->field($validatorForm, 'min') ?>

        <?php foreach(Language::getInstance()->usedLanguages() as $language): ?>
            <?= $form->field($validatorForm, "tooShort['$language->code']")
                ->label("tooShort message for language $language->name") ?>
        <?php endforeach; ?>

        <?= $form->field($validatorForm, 'length') ?>

        <?php foreach(Language::getInstance()->usedLanguages() as $language): ?>
            <?= $form->field($validatorForm, "notEqual['$language->code']")
                ->label("Not equal message for language $language->name") ?>
        <?php endforeach; ?>

    </div>
    <div class="modal-footer">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</div>

<?php ActiveForm::end(); ?>
