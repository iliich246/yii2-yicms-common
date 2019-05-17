<?php //template

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>

<div class="jumbotron" style="text-align: center; height: 100vh; margin-right: auto;margin-left: auto">
    <br>
    <br>
    <br>
    <h1 style="font-size: 20px;">Admin panel</h1>
    <br>
    <br>
<?php $form = ActiveForm::begin([
    'id' => 'admin-login-form',
]);?>
    <?= $form->field($model, 'userName', [
        'inputOptions' => [
            'style' => 'width: 300px;margin-right: auto;margin-left: auto'
        ]
    ]) ?>
    <?= $form->field($model, 'password',[
        'inputOptions' => [
            'style' => 'width: 300px;margin-right: auto;margin-left: auto'
        ]
    ]) ?>
    <?= Html::submitButton('Enter', ['class' => 'btn btn-success']) ?>
    <?php ActiveForm::end(); ?>
</div>
