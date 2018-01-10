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

        var conditionTemplateId = $(button).data('conditionTemplateId');

        if (!($(this).hasClass('condition-confirm-state'))) {
            $(this).before('<span>Are you sure? </span>');
            $(this).text('Yes, I`am sure!');
            $(this).addClass('condition-confirm-state');
        } else {
            $.pjax({
                url: '{$deleteLink}' + conditionTemplateId,
                container: '#update-conditions-list-container',
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500
            });

            var deleteActive = true;

            $('#update-condition-list-container').on('pjax:success', function(event) {

                if (!deleteActive) return false;

                $('#{$modalName}').modal('hide');
                deleteActive = false;
            });
        }
    });
})();
JS;

$this->registerJs($js, $this::POS_READY);

?>

<div class="modal fade"
     id="<?= ConditionsDevModalWidget::getModalWindowName() ?>"
     tabindex="-1"
     role="dialog"
     aria-labelledby="myModalLabel"
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
                    <div class="row">
                        <div class="col-xs-12">

                            <br>

                            <p>IMPORTANT! Do not delete condition templates without serious reason!</p>
                            <button type="button"
                                    class="btn btn-danger"
                                    id="file-delete"
                                    data-condition-template-reference="<?= $widget->devConditionsGroup->conditionTemplate->condition_template_reference ?>"
                                    data-condition-template-id="<?= $widget->devConditionsGroup->conditionTemplate->id ?>">
                                Delete condition template
                            </button>
                        </div>
                    </div>
                    <hr>


                    <?= ValidatorsListWidget::widget([
                        'validatorReference' => $widget->devConditionsGroup->conditionTemplate,
                        'ownerPjaxContainerName' => ConditionsDevModalWidget::getPjaxContainerId(),
                        'ownerModalId' => ConditionsDevModalWidget::getModalWindowName(),
                        'returnUrl' => \yii\helpers\Url::toRoute([
                            '/common/dev-condition/load-modal',
                            'conditionTemplateId' => $widget->devConditionsGroup->conditionTemplate->id,
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
