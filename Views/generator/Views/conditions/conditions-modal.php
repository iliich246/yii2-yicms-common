<?php

use yii\bootstrap\ActiveForm;

/** @var $conditionsGroup Iliich246\YicmsCommon\Conditions\ConditionsGroup */
/** @var $conditionTemplateReference string */
/** @var $form ActiveForm */
/** @var $refreshUrl string */

$js = <<<JS

;(function(){

})();
JS;

$this->registerJs($js);

?>

<div class="modal-conditions-info"
     data-home-url="<?= \yii\helpers\Url::base() ?>"
     data-condition-template-reference="<?= $conditionsGroup->getCurrentConditionTemplateReference() ?>"
     data-refresh-url="<?= $refreshUrl ?>"
    ></div>

<?php if (isset($success) && $success): ?>
    <div class="alert alert-success alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                aria-hidden="true">?</span></button>
        <strong>Success!</strong> Data on page updated.
    </div>
<?php endif; ?>

<?= $conditionsGroup->render($form, true) ?>
