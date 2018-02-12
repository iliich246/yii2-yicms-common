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

    var pjaxContainer   = $(fieldCreateUpdate).parent('.pjax-container');
    var pjaxContainerId = '#' + $(pjaxContainer).attr('id');

    var homeUrl   = $(fieldCreateUpdate).data('homeUrl');
    var returnUrl = $(pjaxContainer).data('returnUrlFields');

    var backButton = $('.fields-modal-create-update-back');

    $(backButton).on('click', goBack);

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
})();
JS;

$this->registerJs($js);

if (isset($returnBack) || $returnBack) $return = 'true';
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
    ]);
    ?>
    <div class="modal-body">
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
            <div class="col-sm-4 col-xs-12 ">
                <?= $form->field($devFieldGroup->fieldTemplate, 'is_main')->checkbox() ?>
            </div>
        </div>

        <?= SimpleTabsTranslatesWidget::widget([
            'form'            => $form,
            'translateModels' => $devFieldGroup->fieldNameTranslates,
            'tabModification' => '_modal'
        ])
        ?>

        <?php if ($devFieldGroup->scenario == DevFieldsGroup::SCENARIO_UPDATE): ?>
            <div class="row delete-button-row">
                <div class="col-xs-12">
                    <br>

                    <p>IMPORTANT! Do not delete fields without serious reason!</p>
                    <button type="button"
                            class="btn btn-danger"
                            id="field-delete"
                            data-field-template-reference="<?= $devFieldGroup->fieldTemplate->field_template_reference ?>"
                            data-field-template-id="<?= $devFieldGroup->fieldTemplate->id ?>"
                            data-field-has-constraints="<?= (int)$devFieldGroup->fieldTemplate->isConstraints() ?>"
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
                'validatorReference'     => $devFieldGroup->fieldTemplate,
                'ownerPjaxContainerName' => FieldsDevModalWidget::getPjaxContainerId(),
                'ownerModalId'           => FieldsDevModalWidget::getModalWindowName(),
                'returnUrl' => \yii\helpers\Url::toRoute([
                    '/common/dev-fields/load-modal-dependent',
                    'fieldTemplateId' => $devFieldGroup->fieldTemplate->id,
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
