<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;

/** @var $this \yii\web\View */
/** @var  $js */

$js = <<<JS
;(function() {

})();
JS;

$this->registerJs($js);

?>

<?php $form = ActiveForm::begin([
    'id' => 'create-update-thumbnail-form',
    'options' => [
        'class'            => 'thumbnail-form',
        'data-pjax'        => true,
        'data-home-url'    => \yii\helpers\Url::base(),
        'data-return-back' => $return,

    ],
]);
?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title">
            Thumbnail configurator

            <span class="glyphicon glyphicon-arrow-left validator-form-back" aria-hidden="true" style="float: right;margin-right: 20px"></span>

        </h3>
    </div>
    <div class="modal-body">

    </div>
    <div class="modal-footer">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::submitButton('Save and back',
            ['class' => 'btn btn-success',
                'value' => 'true', 'name' => '_saveAndBack']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
<?php ActiveForm::end(); ?>
?>
