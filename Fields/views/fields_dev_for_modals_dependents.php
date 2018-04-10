
<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsCommon\Fields\DevFieldsGroup;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;
use Iliich246\YicmsCommon\Validators\ValidatorsListWidget;

/** @var $this \yii\web\View */
/** @var $devFieldGroup \Iliich246\YicmsCommon\Fields\DevFieldsGroup */
/** @var $returnBack boolean */
/** @var $pjaxName string */
/** @var $modalName string */

$js = <<<JS
;(function() {
    var fieldCreateUpdate = $('.field-create-update-modal');

    var pjaxContainerName = '#' + $(fieldCreateUpdate).data('pjaxContainerOwner');
    var pjaxContainer     = $(pjaxContainerName);
    var modalName         = $(fieldCreateUpdate).data('modalOwner');

    var homeUrl   = $(fieldCreateUpdate).data('homeUrl');
    var returnUrl = $(pjaxContainer).data('returnUrlFields');

    var deleteFieldUrl = homeUrl + '/common/dev-fields/delete-field-template-dependent?';

    var isReturn = $(fieldCreateUpdate).data('returnBack');

    var backButton = $('.fields-modal-create-update-back');

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

    $('#field-delete-modal').on('click', function() {
        var button = this;

        var fieldTemplateId     = $(button).data('fieldTemplateId');
        var fieldHasConstraints = $(button).data('fieldHasConstraints');

        if (!($(this).hasClass('field-confirm-state'))) {

            $(this).before('<span>Are you sure? </span>');
            $(this).text('Yes, I`am sure!');
            $(this).addClass('field-confirm-state');
        } else {
            if (!fieldHasConstraints) {
                $.pjax({
                    url: deleteFieldUrl
                         + 'fieldTemplateId=' + fieldTemplateId
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

                var passwordInput = $('#field-delete-password-input');
                var buttonDelete  = $('#button-delete-with-pass');

                $(buttonDelete).on('click', function() {

                    $.pjax({
                        url: deleteFieldUrl
                            + '&pjaxName='  + pjaxContainerName.substr(1)
                            + '&modalName=' + modalName
                            + fieldTemplateId + '&deletePass=' + $(passwordInput).val(),
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
                            message: "Field template has not deleted",
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

<div class="modal-content field-create-update-modal"
     data-home-url="<?= \yii\helpers\Url::base() ?>"
     data-return-back="<?= $return ?>"
     data-pjax-container-owner="<?= $pjaxName ?>"
     data-modal-owner="<?= $modalName ?>"
    >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="myModalLabel">
            <?php if ($devFieldGroup->scenario == DevFieldsGroup::SCENARIO_CREATE): ?>
                Create new field
                <span class="glyphicon glyphicon-arrow-left fields-modal-create-update-back" aria-hidden="true" style="float: right;margin-right: 20px"></span>

            <?php else: ?>
                Update existed field (<?= $devFieldGroup->fieldTemplate->program_name ?>)
                <span class="glyphicon glyphicon-arrow-left fields-modal-create-update-back" aria-hidden="true" style="float: right;margin-right: 20px"></span>

            <?php endif; ?>
        </h3>
    </div>
    <?php $form = ActiveForm::begin([
        'id' => 'create-update-fields-dependent',
        'options' => [
            'data-pjax' => true,
        ]
    ]);
    ?>
    <div class="modal-body">

        <?php if ($devFieldGroup->scenario == DevFieldsGroup::SCENARIO_UPDATE): ?>
            <?= Html::hiddenInput('_fieldTemplateId', $devFieldGroup->fieldTemplate->id, [
                'id' => 'field-template-id-hidden'
            ]) ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-sm-4 col-xs-12">
                <?= $form->field($devFieldGroup->fieldTemplate, 'program_name') ?>
            </div>
            <div class="col-sm-4 col-xs-12">
                <?= $form->field($devFieldGroup->fieldTemplate, 'type')->dropDownList(
                    FieldTemplate::getTypes())
                ?>
            </div>
            <div class="col-sm-4 col-xs-12">
                <?= $form->field($devFieldGroup->fieldTemplate, 'language_type')->dropDownList(
                    FieldTemplate::getLanguageTypes())
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-xs-12 ">
                <?= $form->field($devFieldGroup->fieldTemplate, 'visible')->checkbox() ?>
            </div>
            <div class="col-sm-4 col-xs-12 ">
                <?= $form->field($devFieldGroup->fieldTemplate, 'editable')->checkbox() ?>
            </div>
        </div>

        <?= SimpleTabsTranslatesWidget::widget([
            'form'            => $form,
            'translateModels' => $devFieldGroup->fieldNameTranslates,
            'tabModification' => '_modal'
        ])
        ?>

        <?php if ($devFieldGroup->scenario == DevFieldsGroup::SCENARIO_UPDATE): ?>
            <div class="row delete-button-row-modal">
                <div class="col-xs-12">
                    <br>

                    <p>IMPORTANT! Do not delete fields without serious reason!</p>
                    <button type="button"
                            class="btn btn-danger"
                            id="field-delete-modal"
                            data-field-template-reference="<?= $devFieldGroup->fieldTemplate->field_template_reference ?>"
                            data-field-template-id="<?= $devFieldGroup->fieldTemplate->id ?>"
                            data-field-has-constraints="<?= (int)$devFieldGroup->fieldTemplate->isConstraints() ?>"
                        >
                        Delete field
                    </button>
                </div>
            </div>
            <script type="text/template" id="delete-with-pass-template-modal">
                <div class="col-xs-12">
                    <br>
                    <label for="field-delete-password-input-modal">
                        Field has constraints. Enter dev password for delete field template
                    </label>
                    <input type="password"
                           id="field-delete-password-input-modal"
                           class="form-control" name=""
                           value=""
                           aria-required="true"
                           aria-invalid="false">
                    <br>
                    <button type="button"
                            class="btn btn-danger"
                            id="button-delete-with-pass-modal"
                        >
                        Yes, i am absolutely seriously!!!
                    </button>
                </div>
            </script>

            <hr>

            <?= ValidatorsListWidget::widget([
                'validatorReference'     => $devFieldGroup->fieldTemplate,
                'ownerPjaxContainerName' => $pjaxName,
                'ownerModalId'           => $modalName,
                'returnUrl' => \yii\helpers\Url::toRoute([
                    '/common/dev-fields/load-modal-dependent',
                    'fieldTemplateReference' => $devFieldGroup->fieldTemplate->field_template_reference,
                    'fieldTemplateId'        => $devFieldGroup->fieldTemplate->id,
                    'pjaxName'               => $pjaxName,
                    'modalName'              => $modalName,
                ])
            ]) ?>

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
