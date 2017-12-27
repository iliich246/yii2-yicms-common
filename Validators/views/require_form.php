<?php
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsCommon\Validators\RequiredValidatorForm;
use Iliich246\YicmsCommon\Languages\Language;

/** @var $this \yii\web\View */
/** @var $validatorForm RequiredValidatorForm */
/** @var $pjaxContainer string */
/** @var $returnBack boolean */

$js = <<<JS
    (function() {
        var validatorForm = $('.validator-form');

        var pjaxContainer = $(validatorForm).parent('.pjax-container');
        var pjaxContainerId = '#' + $(pjaxContainer).attr('id');
        var returnUrl = $(pjaxContainer).data('returnUrl');
        var isReturn = $(validatorForm).data('returnBack');

        var backButton = $('.validator-form-back');

        if (isReturn) goBack();

        $(backButton).on('click', goBack);

        function goBack() {
            $.pjax({
                url: returnUrl,
                container: pjaxContainerId,
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500
            });
        }
    })();
JS;

$this->registerJs($js);

if (isset($returnBack)) $return = 'true';
else $return = 'false';
?>

<?php $form = ActiveForm::begin([
    'id' => 'require-validator-form',
    'options' => [
        'class' => 'validator-form',
        'data-pjax' => true,
        'data-return-back' => $return,
    ],
]);
?>
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="myModalLabel">
            Require validator

            <span class="glyphicon glyphicon-arrow-left validator-form-back" aria-hidden="true" style="float: right;margin-right: 20px"></span>
        </h3>
    </div>
    <div class="modal-body">
        <?= $form->field($validatorForm, 'isActivate')->checkbox() ?>

        <p>In string you can place next placeholders: {attribute}, {value}</p>
        <?php foreach (Language::getInstance()->usedLanguages() as $language): ?>
            <?= $form->field($validatorForm, "message['$language->code']")
                ->label("Message for language $language->name") ?>
        <?php endforeach; ?>
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
