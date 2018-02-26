<?php

/** @var $this \yii\web\View */
/** @var $conditionTemplate \Iliich246\YicmsCommon\Conditions\ConditionTemplate */

?>

<div class="modal-content conditon-data-list-modal"
     data-home-url="<?= \yii\helpers\Url::base() ?>"
     data-condition-template-reference="<?= $conditionTemplate->condition_template_reference ?>"
     data-return-url-fields="<?= \yii\helpers\Url::toRoute([
         '/common/dev-fields/update-fields-list-container-dependent',
         'conditionTemplateReference' => $conditionTemplate->condition_template_reference,
     ]) ?>"
    >

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</div>
