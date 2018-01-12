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
