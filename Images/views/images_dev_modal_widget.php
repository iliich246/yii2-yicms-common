<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Images\ImagesDevModalWidget;
use Iliich246\YicmsCommon\Images\DevImagesGroup;
use Iliich246\YicmsCommon\Images\ImagesBlock;
use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;
use Iliich246\YicmsCommon\Validators\ValidatorsListWidget;

/** @var $widget ImagesDevModalWidget */
/** @var \Iliich246\YicmsCommon\Assets\DeveloperAsset $bundle */

$bundle = \Iliich246\YicmsCommon\Assets\DeveloperAsset::register($this);

$modalName = ImagesDevModalWidget::getModalWindowName();
$deleteLink = $widget->deleteLink . '?imageTemplateId=';

$js = <<<JS
;(function() {
    $(document).on('click', '#image-delete', function() {
        var button = ('#image-delete');

        if (!$(button).is('[data-image-template-id]')) return;

        var fileTemplateId = $(button).data('imageTemplateId');

        if (!($(this).hasClass('image-confirm-state'))) {
            $(this).before('<span>Are you sure? </span>');
            $(this).text('Yes, I`am sure!');
            $(this).addClass('image-confirm-state');
        } else {
            $.pjax({
                url: '{$deleteLink}' + fileTemplateId,
                container: '#update-images-list-container',
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500
            });

            var deleteActived = true;

            $('#update-images-list-container').on('pjax:success', function(event) {

                if (!deleteActived) return false;

                $('#{$modalName}').modal('hide');
                deleteActived = false;
            });
        }
    });

    $(document).on('click', '.config-thumbnails-button', function() {

        var imageTemplateId = $(this).data('imageTemplateId');



    });
})();
JS;

$this->registerJs($js, $this::POS_READY);

?>

<div class="modal fade"
     id="<?= ImagesDevModalWidget::getModalWindowName() ?>"
     tabindex="-1"
     role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <?php Pjax::begin([
            'options' => [
                'id' => ImagesDevModalWidget::getPjaxContainerId(),
                'class' => 'pjax-container',
                'data-return-url' => '0',
            ],
        ]); ?>
        <?php $form = ActiveForm::begin([
            'id' => ImagesDevModalWidget::getFormName(),
            'action' => $widget->action,
            'options' => [
                'data-pjax' => true,
                'data-yicms-saved' => $widget->dataSaved,
            ],
        ]);
        ?>

        <?php if ($widget->devImagesGroup->scenario == DevImagesGroup::SCENARIO_UPDATE): ?>
            <?= Html::hiddenInput('_imageTemplateId', $widget->devImagesGroup->imagesBlock->id, [
                'id' => 'image-template-id-hidden'
            ]) ?>
        <?php endif; ?>

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="myModalLabel">
                    <?php if ($widget->devImagesGroup->scenario == DevImagesGroup::SCENARIO_CREATE): ?>
                        Create new images block template
                    <?php else: ?>
                        Update existed image block template (<?= $widget->devImagesGroup->imagesBlock->program_name ?>)
                    <?php endif; ?>
                </h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4 col-xs-12">
                        <?= $form->field($widget->devImagesGroup->imagesBlock, 'program_name') ?>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <?= $form->field($widget->devImagesGroup->imagesBlock, 'type')->dropDownList(
                            ImagesBlock::getTypes())
                        ?>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <?= $form->field($widget->devImagesGroup->imagesBlock, 'language_type')->dropDownList(
                            ImagesBlock::getLanguageTypes())
                        ?>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <?= $form->field($widget->devImagesGroup->imagesBlock, 'max_images') ?>
                    </div>
                    <div class="col-sm-8 col-xs-12">
                        <br>
                        <p>zero value - infinite count of images in block</p>
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-4 col-xs-12 ">
                        <?= $form->field($widget->devImagesGroup->imagesBlock, 'visible')->checkbox() ?>
                    </div>
                    <div class="col-sm-4 col-xs-12 ">
                        <?= $form->field($widget->devImagesGroup->imagesBlock, 'editable')->checkbox() ?>
                    </div>
                    <?php if ($widget->devImagesGroup->scenario == DevImagesGroup::SCENARIO_CREATE): ?>
                        <div class="col-sm-4 col-xs-12 ">
                            <?= $form->field($widget->devImagesGroup->imagesBlock, 'createStandardFields')->checkbox() ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?= SimpleTabsTranslatesWidget::widget([
                    'form' => $form,
                    'translateModels' => $widget->devImagesGroup->imagesNameTranslates,
                ])
                ?>

                <?php if ($widget->devImagesGroup->scenario == DevImagesGroup::SCENARIO_UPDATE): ?>
                    <div class="row">
                        <div class="col-xs-12">

                            <br>

                            <p>IMPORTANT! Do not delete images blocks without serious reason!</p>
                            <button type="button"
                                    class="btn btn-danger"
                                    id="image-delete"
                                    data-image-template-reference="<?= $widget->devImagesGroup->imagesBlock->image_template_reference ?>"
                                    data-image-template-id="<?= $widget->devImagesGroup->imagesBlock->id ?>">
                                Delete image block template
                            </button>
                        </div>
                    </div>
                    <hr>

                    <a href="<?= \yii\helpers\Url::toRoute([
                        '/common/dev-images/show-image-block-fields',
                        'imageTemplateId' => $widget->devImagesGroup->imagesBlock->id
                    ]) ?>"
                       class="btn btn-primary">
                        View image block fields
                    </a>

                    <p class="btn btn-primary config-thumbnails-button"
                       data-image-template-id="<?= $widget->devImagesGroup->imagesBlock->id ?>">
                        Config images thumbnails
                    </p>

                    <hr>

                    <?= ValidatorsListWidget::widget([
                        'validatorReference' => $widget->devImagesGroup->imagesBlock,
                        'ownerPjaxContainerName' => ImagesDevModalWidget::getPjaxContainerId(),
                        'ownerModalId' => ImagesDevModalWidget::getModalWindowName(),
                        'returnUrl' => \yii\helpers\Url::toRoute([
                            '/common/dev-images/load-modal',
                            'imageTemplateId' => $widget->devImagesGroup->imagesBlock->id,
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
