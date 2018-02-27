<?php

use Iliich246\YicmsCommon\Conditions\ConditionValues;

/** @var $this \yii\web\View */
/** @var $conditionTemplate \Iliich246\YicmsCommon\Conditions\ConditionTemplate */
/** @var $conditionValue \Iliich246\YicmsCommon\Conditions\ConditionValues */
/** @var $conditionValuesTranslates \Iliich246\YicmsCommon\Conditions\ConditionValueNamesForm[] */
?>

<div class="modal-content condition-data-list-modal"
     data-home-url="<?= \yii\helpers\Url::base() ?>"
     data-condition-template-reference="<?= 1 ?>"
     data-return-url-fields="<?= \yii\helpers\Url::toRoute([
         '/common/dev-fields/update-fields-list-container-dependent',
         'conditionTemplateReference' => $conditionTemplate->condition_template_reference,
     ]) ?>"
    >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title">
            <?php if ($conditionValue->scenario == ConditionValues::SCENARIO_CREATE): ?>
                Create condition value
            <?php else: ?>
                Update condition value
            <?php endif; ?>
            <span class="glyphicon glyphicon-arrow-left condition-data-list-back"
                  style="float: right;margin-right: 20px"></span>
        </h3>
    </div>
    <div class="modal-body">

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</div>
