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

?>

<div class="modal fade" id="<?= FieldsDevInputWidget::getModalWindowName() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <?php Pjax::begin([
            'options' => [
                'id' => FieldsDevInputWidget::getPjaxContainerId(),
            ]
        ]); ?>
        <?php $form = ActiveForm::begin([
            'id' => FieldsDevInputWidget::getFormName(),
            'options' => [
                'data-pjax' => true,
            ],
        ]);
        ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Название модали</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-sm-6 col-xs-12">
                        <?= $form->field($widget->devFieldGroup->fieldTemplate, 'program_name') ?>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <?= $form->field($widget->devFieldGroup->fieldTemplate, 'type')->dropDownList(
                            FieldTemplate::getTypes())
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
            </div>
            <div class="modal-footer">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
        <pre>
            <?php print_r($widget->devFieldGroup->fieldTemplate)?>
        </pre>
        <?php ActiveForm::end(); ?>
        <?php Pjax::end() ?>
    </div>
</div>