<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsCommon\Conditions\ConditionValues;
use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;

/** @var $this \yii\web\View */
/** @var $conditionTemplate \Iliich246\YicmsCommon\Conditions\ConditionTemplate */
/** @var $conditionValue \Iliich246\YicmsCommon\Conditions\ConditionValues */
/** @var $conditionValuesTranslates \Iliich246\YicmsCommon\Conditions\ConditionValueNamesForm[] */

$js = <<<JS
;(function() {

    var conditionValueModal = $('.condition-create-update-value-modal');
    var deleteButton        = $('#condition-value-delete');
    var backButton          = $('.condition-create-update-value-back');

    var pjaxContainer   = $(conditionValueModal).parent('.pjax-container');
    var pjaxContainerId = '#' + $(pjaxContainer).attr('id');

    var homeUrl           = $(conditionValueModal).data('homeUrl');
    var returnUrl         = $(conditionValueModal).data('returnUrl');
    var redirectUpdateUrl = $(conditionValueModal).data('redirectUpdateUrl');
    var deleteUrl         = homeUrl + '/common/dev-conditions/delete-condition-value';

    var isReturn         = $(conditionValueModal).data('returnBack');
    var isRedirectUpdate = $(conditionValueModal).data('redirectUpdate');

    if (isReturn) goBack();

    if (isRedirectUpdate) redirectUpdate();

    $(backButton).on('click', function(){
        goBack();
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

    function redirectUpdate() {
        $.pjax({
            url: redirectUpdateUrl,
            container: pjaxContainerId,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500,
        });
    }

    $('#condition-value-delete').on('click',  function() {

        var button = ('#condition-value-delete');

        if (!$(button).is('[data-condition-value-id]')) return;

        var conditionValueId        = $(button).data('conditionValueId');
        var conditionHasConstraints = $(button).data('conditionValueHasConstraints');

        if (!($(this).hasClass('condition-value-confirm-state'))) {
            $(this).before('<span>Are you sure? </span>');
            $(this).text('Yes, I`am sure!');
            $(this).addClass('condition-value-confirm-state');
        } else {
            if (!conditionHasConstraints) {
                $.pjax({
                    url: deleteUrl + '?conditionValueId=' + conditionValueId,
                    container: pjaxContainerId,
                    scrollTo: false,
                    push: false,
                    type: "POST",
                    timeout: 2500
                });

                var deleteActive = true;

                $(pjaxContainer).on('pjax:success', function(event) {

                    if (!deleteActive) return false;

                    //$('#{modalName}').modal('hide');
                    deleteActive = false;
                });
            } else {
                var deleteButtonRow = $('.delete-button-row');

                var template = _.template($('#delete-with-pass-template').html());
                $(deleteButtonRow).empty();
                $(deleteButtonRow).append(template);

                var passwordInput = $('#condition-value-delete-password-input');
                var buttonDelete  = $('#button-delete-with-pass');

                $(buttonDelete).on('click', function() {
                    $.pjax({
                        url: deleteUrl + '?conditionValueId=' + conditionValueId +
                                         '&deletePass=' + $(passwordInput).val(),
                        container: pjaxContainerId,
                        scrollTo: false,
                        push: false,
                        type: "POST",
                        timeout: 2500
                    });

                    var deleteActive = true;

                    //$(pjaxContainer).on('pjax:success', function(event) {
                    //
                    //    if (!deleteActive) return false;
                    //
                    //    $('#conditionsDevModal').modal('hide');
                    //    deleteActive = false;
                    //});

                    $(pjaxContainer).on('pjax:error', function(event) {

                        $('#conditionsDevModal').modal('hide');

                        bootbox.alert({
                            size: 'large',
                            title: "Wrong dev password",
                            message: "Condition value has not deleted",
                            className: 'bootbox-error'
                        });
                    });
                });

//                $('#{modalName}').on('hide.bs.modal', function() {
//                    $(pjaxContainer).off('pjax:error');
//                    $(pjaxContainer).off('pjax:success');
//                    $('#{modalName}').off('hide.bs.modal');
//                });
            }
        }
    });

})();
JS;

$this->registerJs($js);

if (isset($returnBack) && $returnBack) $return = 'true';
else $return = 'false';

if (isset($redirectUpdate)) $redirect = 'true';
else $redirect = 'false';

$conditionValue->isNewRecord ? $conditionValueId = '0' : $conditionValueId = $conditionValue->id;

?>

<div class="modal-content condition-create-update-value-modal"
     data-home-url="<?= \yii\helpers\Url::base() ?>"
     data-return-back="<?= $return ?>"
     data-redirect-update="<?= $redirect ?>"
     data-return-url="<?= \yii\helpers\Url::toRoute([
         '/common/dev-conditions/condition-values-list',
         'conditionTemplateId' => $conditionTemplate->id,
     ]) ?>"
     data-redirect-update-url="<?= \yii\helpers\Url::toRoute([
         '/common/dev-conditions/update-condition-value',
         'conditionValueId' => $conditionValueId,
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
            <span class="glyphicon glyphicon-arrow-left condition-create-update-value-back"
                  style="float: right;margin-right: 20px"></span>
        </h3>
    </div>
    <?php $form = ActiveForm::begin([
        'id' => 'condition-create-update-value-form',
        'options' => [
            'data-pjax' => true,
            'data-return-back' => $return
        ],
    ]);
    ?>
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-6 col-xs-12">
                <?= $form->field($conditionValue, 'value_name') ?>
            </div>
            <div class="col-sm-6 col-xs-12">
                <br>
                <?= $form->field($conditionValue, 'is_default')->checkbox() ?>
            </div>
        </div>

        <?= SimpleTabsTranslatesWidget::widget([
            'form' => $form,
            'translateModels' => $conditionValuesTranslates,
        ])
        ?>

        <?php if ($conditionValue->scenario == ConditionValues::SCENARIO_UPDATE): ?>
            <div class="row delete-button-row">
                <div class="col-xs-12">
                    <br>
                    <button type="button"
                            class="btn btn-danger"
                            data-condition-value-id="<?= $conditionValue->id ?>"
                            data-condition-value-has-constraints="<?= (int)$conditionValue->isConstraints() ?>"
                            id="condition-value-delete">
                        Delete condition value
                    </button>
                </div>
            </div>
            <script type="text/template" id="delete-with-pass-template">
                <div class="col-xs-12">
                    <br>
                    <label for="condition-value-delete-password-input">
                        Condition value has constraints. Enter dev password for delete condition value
                    </label>
                    <input type="password"
                           id="condition-value-delete-password-input"
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
