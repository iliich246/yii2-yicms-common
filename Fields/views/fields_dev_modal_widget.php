<?php
//TODO: https://codepen.io/dimbslmh/full/mKfCc modal to center!!!

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\DevFieldsGroup;
use Iliich246\YicmsCommon\Fields\FieldsDevModalWidget;
use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;
use Iliich246\YicmsCommon\Validators\ValidatorsListWidget;

/** @var $this \yii\web\View */
/** @var $widget FieldsDevModalWidget */
/** @var \Iliich246\YicmsCommon\Assets\DeveloperAsset $bundle */

$bundle = \Iliich246\YicmsCommon\Assets\DeveloperAsset::register($this);

$modalName = FieldsDevModalWidget::getModalWindowName();
$deleteLink = $widget->deleteLink . '?fieldTemplateId=';

$js = <<<JS
;(function() {
    $(document).on('click', '#field-delete', function() {
        var button = ('#field-delete');

        if (!$(button).is('[data-field-template-id]')) return;

        var fieldTemplateId     = $(button).data('fieldTemplateId');
        var fieldHasConstraints = $(button).data('fieldHasConstraints');
        var pjaxContainer       = $('#update-fields-list-container');

        if (!($(this).hasClass('field-confirm-state'))) {
            $(this).before('<span>Are you sure? </span>');
            $(this).text('Yes, I`am sure!');
            $(this).addClass('field-confirm-state');
        } else {
            if (!fieldHasConstraints) {
                $.pjax({
                    url: '{$deleteLink}' + fieldTemplateId,
                    container: '#update-fields-list-container',
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

                var passwordInput = $('#field-delete-password-input');
                var buttonDelete  = $('#button-delete-with-pass');

                $(buttonDelete).on('click', function() {

                    $.pjax({
                        url: '{$deleteLink}' + fieldTemplateId + '&deletePass=' + $(passwordInput).val(),
                        container: '#update-fields-list-container',
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
                            message: "Field template has not deleted",
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
     id="<?= FieldsDevModalWidget::getModalWindowName() ?>"
     tabindex="-1"
     role="dialog"
     data-backdrop="static"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <?php Pjax::begin([
            'options' => [
                'id'                         => FieldsDevModalWidget::getPjaxContainerId(),
                'class'                      => 'pjax-container',
                'data-return-url'            => '0',
                'data-return-url-validators' => '0',
            ],
        ]); ?>
        <?php $form = ActiveForm::begin([
            'id' => FieldsDevModalWidget::getFormName(),
            'action'  => $widget->action,
            'options' => [
                'data-pjax'        => true,
                'data-yicms-saved' => $widget->dataSaved,
            ],
        ]);
        ?>

        <?php if ($widget->devFieldGroup->scenario == DevFieldsGroup::SCENARIO_UPDATE): ?>
            <?= Html::hiddenInput('_fieldTemplateId', $widget->devFieldGroup->fieldTemplate->id, [
                'id' => 'field-template-id-hidden'
            ]) ?>
        <?php endif; ?>

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="myModalLabel">
                    <?php if ($widget->devFieldGroup->scenario == DevFieldsGroup::SCENARIO_CREATE): ?>
                        Create new field
                    <?php else: ?>
                        Update existed field (<?= $widget->devFieldGroup->fieldTemplate->program_name ?>)
                    <?php endif; ?>
                </h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4 col-xs-12">
                        <?= $form->field($widget->devFieldGroup->fieldTemplate, 'program_name') ?>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <?= $form->field($widget->devFieldGroup->fieldTemplate, 'type')->dropDownList(
                            FieldTemplate::getTypes())
                        ?>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <?= $form->field($widget->devFieldGroup->fieldTemplate, 'language_type')->dropDownList(
                            FieldTemplate::getLanguageTypes())
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-xs-12 ">
                        <?= $form->field($widget->devFieldGroup->fieldTemplate, 'visible')->checkbox() ?>
                    </div>
                    <div class="col-sm-4 col-xs-12 ">
                        <?= $form->field($widget->devFieldGroup->fieldTemplate, 'editable')->checkbox() ?>
                    </div>
                    <div class="col-sm-4 col-xs-12 ">
                        <?= $form->field($widget->devFieldGroup->fieldTemplate, 'is_main')->checkbox() ?>
                    </div>
                </div>

                <?= SimpleTabsTranslatesWidget::widget([
                    'form' => $form,
                    'translateModels' => $widget->devFieldGroup->fieldNameTranslates,
                ])
                ?>

                <?php if ($widget->devFieldGroup->scenario == DevFieldsGroup::SCENARIO_UPDATE): ?>
                    <div class="row delete-button-row">
                        <div class="col-xs-12">
                            <br>

                            <p>IMPORTANT! Do not delete fields without serious reason!</p>
                            <button type="button"
                                    class="btn btn-danger"
                                    id="field-delete"
                                    data-field-template-reference="<?= $widget->devFieldGroup->fieldTemplate->field_template_reference ?>"
                                    data-field-template-id="<?= $widget->devFieldGroup->fieldTemplate->id ?>"
                                    data-field-has-constraints="<?= (int)$widget->devFieldGroup->fieldTemplate->isConstraints() ?>"
                                >
                                Delete field
                            </button>
                        </div>
                    </div>
                    <script type="text/template" id="delete-with-pass-template">
                        <div class="col-xs-12">
                            <br>
                            <label for="field-delete-password-input">
                                Field has constraints. Enter dev password for delete field template
                            </label>
                            <input type="password"
                                   id="field-delete-password-input"
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

                    <?= ValidatorsListWidget::widget([
                        'validatorReference'     => $widget->devFieldGroup->fieldTemplate,
                        'ownerPjaxContainerName' => FieldsDevModalWidget::getPjaxContainerId(),
                        'ownerModalId'           => FieldsDevModalWidget::getModalWindowName(),
                        'returnUrl'              => \yii\helpers\Url::toRoute([
                            '/common/dev-fields/load-modal',
                            'fieldTemplateId' => $widget->devFieldGroup->fieldTemplate->id,
                        ])
                    ]) ?>

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
