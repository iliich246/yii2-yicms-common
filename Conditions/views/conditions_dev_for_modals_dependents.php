<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsCommon\Conditions\DevConditionsGroup;
use Iliich246\YicmsCommon\Conditions\ConditionTemplate;
use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;
use Iliich246\YicmsCommon\Validators\ValidatorsListWidget;

/** @var $this \yii\web\View */
/** @var $devConditionGroup DevConditionsGroup */
/** @var $returnBack boolean */
/** @var $pjaxName string */
/** @var $modalName string */

$js = <<<JS
;(function() {
    var conditionCreateUpdate = $('.condition-create-update-modal');

    var pjaxContainerName = '#' + $(conditionCreateUpdate).data('pjaxContainerOwner');
    var pjaxContainer     = $(pjaxContainerName);
    var modalName         = $(conditionCreateUpdate).data('modalOwner');

    var homeUrl   = $(conditionCreateUpdate).data('homeUrl');
    var returnUrl = $(pjaxContainer).data('returnUrlConditions');

    var deleteConditionUrl = homeUrl + '/common/dev-conditions/delete-condition-template-dependent?';

    var isReturn = $(conditionCreateUpdate).data('returnBack');

    var backButton = $('.conditions-modal-create-update-back');

    if (isReturn) goBack();

    $(backButton).on('click', goBack);

    function goBack() {
         $.pjax({
             url: returnUrl,
             container: pjaxContainerName,
             scrollTo: false,
             push: false,
             type: "POST",
             timeout: 2500,
         });
    }

    $('#condition-delete-modal').on('click', function() {
        var button = this;

        if (!$(button).is('[data-condition-template-id]')) return;

        var conditionTemplateId     = $(button).data('conditionTemplateId');
        var conditionHasConstraints = $(button).data('conditionHasConstraints');
        var pjaxContainer           = $('#update-conditions-list-container');

        if (!($(this).hasClass('condition-confirm-state'))) {
            $(this).before('<span>Are you sure? </span>');
            $(this).text('Yes, I`am sure!');
            $(this).addClass('condition-confirm-state');
        } else {
            if (!conditionHasConstraints) {
                $.pjax({
                    url: deleteConditionUrl
                         + 'conditionTemplateId=' + conditionTemplateId
                         + '&pjaxName='  + pjaxContainerName.substr(1)
                         + '&modalName=' + modalName,
                    container: pjaxContainerName,
                    scrollTo: false,
                    push: false,
                    type: "POST",
                    timeout: 2500
                });

                var deleteActive = true;

                $(pjaxContainer).on('pjax:success', function(event) {

                    if (!deleteActive) return false;

                    $(modalName).modal('hide');
                    deleteActive = false;
                });
            } else {
                var deleteButtonRow = $('.delete-button-row');

                var template = _.template($('#delete-with-pass-template').html());
                $(deleteButtonRow).empty();
                $(deleteButtonRow).append(template);

                var passwordInput = $('#condition-delete-password-input');
                var buttonDelete  = $('#button-delete-with-pass');

                $(buttonDelete).on('click', function() {

                $.pjax({
                    url: deleteConditionUrl
                         + 'conditionTemplateId=' + conditionTemplateId
                         + '&pjaxName='  + pjaxContainerName.substr(1)
                         + '&modalName=' + modalName,
                    container: pjaxContainerName,
                    scrollTo: false,
                    push: false,
                    type: "POST",
                    timeout: 2500
                });

                    var deleteActive = true;

                    $(pjaxContainer).on('pjax:success', function(event) {

                        if (!deleteActive) return false;

                        $(modalName).modal('hide');

                        deleteActive = false;
                    });

                    $(pjaxContainer).on('pjax:error', function(event) {

                        $(modalName).modal('hide');

                        bootbox.alert({
                            size: 'large',
                            title: "Wrong dev password",
                            message: "Condition template has not deleted",
                            className: 'bootbox-error'
                        });
                    });
                });

                $(modalName).on('hide.bs.modal', function() {
                    $(pjaxContainer).off('pjax:error');
                    $(pjaxContainer).off('pjax:success');
                    $(modalName).off('hide.bs.modal');
                });
            }
        }
    });
})();
JS;


$this->registerJs($js);

if (isset($returnBack) && $returnBack) $return = 'true';
else $return = 'false';

?>
<div class="modal-content condition-create-update-modal"
     data-home-url="<?= \yii\helpers\Url::base() ?>"
     data-return-back="<?= $return ?>"
     data-pjax-container-owner="<?= $pjaxName ?>"
     data-modal-owner="<?= $modalName ?>"
>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="myModalLabel">
            <?php if ($devConditionGroup->scenario == DevConditionsGroup::SCENARIO_CREATE): ?>
                Create new condition template
                <span class="glyphicon glyphicon-arrow-left conditions-modal-create-update-back"
                      aria-hidden="true"
                      style="float: right;margin-right: 20px">
                </span>
            <?php else: ?>
                Update existed condition template (<?= $devConditionGroup->conditionTemplate->program_name ?>)
                <span class="glyphicon glyphicon-arrow-left conditions-modal-create-update-back"
                      aria-hidden="true"
                      style="float: right;margin-right: 20px">

                </span>

            <?php endif; ?>
        </h3>
    </div>
    <?php $form = ActiveForm::begin([
        'id' => 'create-update-conditions-dependent',
        'options' => [
            'data-pjax' => true,
        ]
    ]);
    ?>

    <div class="modal-body">
        <div class="row">
            <div class="col-sm-4 col-xs-12">
                <?= $form->field($devConditionGroup->conditionTemplate, 'program_name') ?>
            </div>
            <div class="col-sm-4 col-xs-12">
                <?= $form->field($devConditionGroup->conditionTemplate, 'type')->dropDownList(
                    ConditionTemplate::getTypes())
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-xs-12 ">
                <?= $form->field($devConditionGroup->conditionTemplate, 'editable')->checkbox() ?>
            </div>
        </div>

        <?= SimpleTabsTranslatesWidget::widget([
            'form' => $form,
            'translateModels' => $devConditionGroup->conditionNameTranslates,
        ])
        ?>

        <?php if ($devConditionGroup->scenario == DevConditionsGroup::SCENARIO_UPDATE): ?>
            <div class="row delete-button-row">
                <div class="col-xs-12">

                    <br>

                    <p>IMPORTANT! Do not delete condition templates without serious reason!</p>
                    <button type="button"
                            class="btn btn-danger"
                            id="condition-delete-modal"
                            data-condition-template-reference="<?= $devConditionGroup->conditionTemplate->condition_template_reference ?>"
                            data-condition-template-id="<?= $devConditionGroup->conditionTemplate->id ?>"
                            data-condition-has-constraints="<?= (int)$devConditionGroup->conditionTemplate->isConstraints() ?>"
                    >
                        Delete condition template
                    </button>
                </div>
            </div>
            <script type="text/template" id="delete-with-pass-template">
                <div class="col-xs-12">
                    <br>
                    <label for="condition-delete-password-input">
                        Field has constraints. Enter dev password for delete field template
                    </label>
                    <input type="password"
                           id="condition-delete-password-input"
                           class="form-control" name=""
                           value=""
                           aria-required="true"
                           aria-invalid="false">
                    <br>
                    <button type="button"
                            class="btn btn-danger"
                            id="button-delete-with-pass"
                    >
                        Yes, i am absolutely seriously!!!
                    </button>
                </div>
            </script>

            <hr>

            <p class="btn btn-primary condition-data-list"
               data-condition-template-id="<?= $devConditionGroup->conditionTemplate->id ?>"
               data-return-url="<?= \yii\helpers\Url::toRoute([
                   '/common/dev-conditions/load-modal',
                   'conditionTemplateId' => $devConditionGroup->conditionTemplate->id,
               ]) ?>"
            >
                Config condition options
            </p>
        <?php endif; ?>
    </div>


    <div class="modal-footer">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::submitButton('Save and back',
            ['class' => 'btn btn-success',
                'value' => 'true', 'name' => '_saveAndBack']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
    <?php ActiveForm::end(); ?>
</div>
