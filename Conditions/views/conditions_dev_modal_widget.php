<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Conditions\ConditionsDevModalWidget;
use Iliich246\YicmsCommon\Conditions\DevConditionsGroup;
use Iliich246\YicmsCommon\Conditions\ConditionTemplate;
use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;
use Iliich246\YicmsCommon\Validators\ValidatorsListWidget;

/** @var $widget \Iliich246\YicmsCommon\Conditions\ConditionsDevModalWidget */
/** @var \Iliich246\YicmsCommon\Assets\DeveloperAsset $bundle */

$bundle = \Iliich246\YicmsCommon\Assets\DeveloperAsset::register($this);

$modalName = ConditionsDevModalWidget::getModalWindowName();
$deleteLink = $widget->deleteLink . '?conditionTemplateId=';

$js = <<<JS
;(function() {
    $(document).on('click', '#condition-delete', function() {
        var button = ('#condition-delete');

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
                    url: '{$deleteLink}' + conditionTemplateId,
                    container: '#update-conditions-list-container',
                    scrollTo: false,
                    push: false,
                    type: "POST",
                    timeout: 2500
                });

                var deleteActive = true;

                $(pjaxContainer).on('pjax:success', function(event) {

                    if (!deleteActive) return false;

                    $('#{$modalName}').modal('hide');
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
                        url: '{$deleteLink}' + conditionTemplateId + '&deletePass=' + $(passwordInput).val(),
                        container: '#update-conditions-list-container',
                        scrollTo: false,
                        push: false,
                        type: "POST",
                        timeout: 2500
                    });

                    var deleteActive = true;

                    $(pjaxContainer).on('pjax:success', function(event) {

                        if (!deleteActive) return false;

                        $('#{$modalName}').modal('hide');

                        deleteActive = false;
                    });

                    $(pjaxContainer).on('pjax:error', function(event) {

                        $('#{$modalName}').modal('hide');

                        bootbox.alert({
                            size: 'large',
                            title: "Wrong dev password",
                            message: "Condition template has not deleted",
                            className: 'bootbox-error'
                        });
                    });
                });

                $('#{$modalName}').on('hide.bs.modal', function() {
                    $(pjaxContainer).off('pjax:error');
                    $(pjaxContainer).off('pjax:success');
                    $('#{$modalName}').off('hide.bs.modal');
                });
            }
        }
    });
})();
JS;

$this->registerJs($js, $this::POS_READY);

$this->registerAssetBundle(\Iliich246\YicmsCommon\Assets\LodashAsset::className());

?>

<div class="modal fade"
     id="<?= ConditionsDevModalWidget::getModalWindowName() ?>"
     tabindex="-1"
     role="dialog"
     data-backdrop="static"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <?php Pjax::begin([
            'options' => [
                'id' => ConditionsDevModalWidget::getPjaxContainerId(),
                'class' => 'pjax-container',
                'data-return-url' => '0',
            ],
        ]); ?>
        <?php $form = ActiveForm::begin([
            'id' => ConditionsDevModalWidget::getFormName(),
            'action' => $widget->action,
            'options' => [
                'data-pjax' => true,
                'data-yicms-saved' => $widget->dataSaved,
            ],
        ]);
        ?>

        <?php if ($widget->devConditionsGroup->scenario == DevConditionsGroup::SCENARIO_UPDATE): ?>
            <?= Html::hiddenInput('_conditionTemplateId', $widget->devConditionsGroup->conditionTemplate->id, [
                'id' => 'condition-template-id-hidden'
            ]) ?>
        <?php endif; ?>

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="myModalLabel">
                    <?php if ($widget->devConditionsGroup->scenario == DevConditionsGroup::SCENARIO_CREATE): ?>
                        Create new condition template
                    <?php else: ?>
                        Update existed condition template (<?= $widget->devConditionsGroup->conditionTemplate->program_name ?>)
                    <?php endif; ?>
                </h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4 col-xs-12">
                        <?= $form->field($widget->devConditionsGroup->conditionTemplate, 'program_name') ?>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <?= $form->field($widget->devConditionsGroup->conditionTemplate, 'type')->dropDownList(
                            ConditionTemplate::getTypes())
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-xs-12 ">
                        <?= $form->field($widget->devConditionsGroup->conditionTemplate, 'editable')->checkbox() ?>
                    </div>
                </div>

                <?= SimpleTabsTranslatesWidget::widget([
                    'form' => $form,
                    'translateModels' => $widget->devConditionsGroup->conditionNameTranslates,
                ])
                ?>

                <?php if ($widget->devConditionsGroup->scenario == DevConditionsGroup::SCENARIO_UPDATE): ?>
                    <div class="row delete-button-row">
                        <div class="col-xs-12">

                            <br>

                            <p>IMPORTANT! Do not delete condition templates without serious reason!</p>
                            <button type="button"
                                    class="btn btn-danger"
                                    id="condition-delete"
                                    data-condition-template-reference="<?= $widget->devConditionsGroup->conditionTemplate->condition_template_reference ?>"
                                    data-condition-template-id="<?= $widget->devConditionsGroup->conditionTemplate->id ?>"
                                    data-condition-has-constraints="<?= (int)$widget->devConditionsGroup->conditionTemplate->isConstraints() ?>"
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
                       data-condition-template-id="<?= $widget->devConditionsGroup->conditionTemplate->id ?>"
                       data-return-url="<?= \yii\helpers\Url::toRoute([
                           '/common/dev-conditions/load-modal',
                           'conditionTemplateId' => $widget->devConditionsGroup->conditionTemplate->id,
                       ]) ?>"
                        >
                        Config condition options
                    </p>
                <?php endif; ?>
            </div>

            <div class="modal-footer">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        <?php Pjax::end() ?>
    </div>
</div>
