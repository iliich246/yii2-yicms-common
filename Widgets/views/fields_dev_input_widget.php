<?php
//TODO: https://codepen.io/dimbslmh/full/mKfCc modal to center!!!

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Fields\FieldNamesTranslatesForm;
use Iliich246\YicmsCommon\Fields\DevFieldsGroup;
use Iliich246\YicmsCommon\Widgets\FieldsDevInputWidget;
use Iliich246\YicmsCommon\Fields\Field;
use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;
use Iliich246\YicmsCommon\Fields\FieldTemplate;

/** @var $widget FieldsDevInputWidget */
/** @var \Iliich246\YicmsCommon\Assets\DeveloperAsset $bundle */
$bundle = \Iliich246\YicmsCommon\Assets\DeveloperAsset::register($this);


$js = <<<EOT

(function() {
    var firstOpen = true;

    $(document).on('click', '#field-delete', function() {

        if (!($(this).hasClass('field-confirm-state'))) {
            $(this).before('<span>Are you sure? </span>');
            $(this).text('Yes, I`am sure!');
            $(this).addClass('field-confirm-state');
        } else {

            alert(1);
        //    $.pjax({
        //        url: '{3$3urlFieldOrderDown}&fieldTemplateId=' + $(this).data('fieldTemplateId'),
        //        container: '#update-fields-list-container',
        //        scrollTo: false,
        //        push: false,
        //        type: "POST",
        //        timeout: 2500
        //    });
        }
    });
})();
EOT;

$this->registerJs($js, $this::POS_READY);
?>

<div class="modal fade"
     id="<?= FieldsDevInputWidget::getModalWindowName() ?>"
     tabindex="-1"
     role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <?php Pjax::begin([
            'options' => [
                'id' => FieldsDevInputWidget::getPjaxContainerId(),
            ],
        ]); ?>
        <?php $form = ActiveForm::begin([
            'id' => FieldsDevInputWidget::getFormName(),
            'options' => [
                'data-pjax' => true,
                'data-yicms-saved' => $widget->dataSaved,
            ],
        ]);
        ?>
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
                    <p>IMPORTANT! Do not delete fields without serious reason!</p>
                    <button type="button" class="btn btn-danger" id="field-delete">Delete field</button>
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