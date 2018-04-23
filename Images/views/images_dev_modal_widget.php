<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Images\ImagesDevModalWidget;
use Iliich246\YicmsCommon\Images\DevImagesGroup;
use Iliich246\YicmsCommon\Images\ImagesBlock;
use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;
use Iliich246\YicmsCommon\Validators\ValidatorsListWidget;

/** @var $this \yii\web\View */
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

        var imageTemplateId          = $(button).data('imageTemplateId');
        var imageBlockHasConstraints = $(button).data('imageBlockHasConstraints');
        var pjaxContainer            = $('#update-images-list-container');

        if (!($(this).hasClass('image-confirm-state'))) {
            $(this).before('<span>Are you sure? </span>');
            $(this).text('Yes, I`am sure!');
            $(this).addClass('image-confirm-state');
        } else {
            if (!imageBlockHasConstraints) {
                $.pjax({
                    url: '{$deleteLink}' + imageTemplateId,
                    container: '#update-images-list-container',
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

                var passwordInput = $('#images-block-delete-password-input');
                var buttonDelete  = $('#button-delete-with-pass');

                $(buttonDelete).on('click', function() {
                    $.pjax({
                        url: '{$deleteLink}' + imageTemplateId + '&deletePass=' + $(passwordInput).val(),
                        container: '#update-images-list-container',
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
                             message: "Images block template has not deleted",
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

if ($widget->devImagesGroup->scenario == DevImagesGroup::SCENARIO_CREATE &&
    $widget->devImagesGroup->justSaved)
    $redirectToUpdate = 'true';
else
    $redirectToUpdate = 'false';

if ($redirectToUpdate == 'true')
    $imageBlockIdForRedirect = $widget->devImagesGroup->imagesBlock->id;
else
    $imageBlockIdForRedirect = '0';

?>

<div class="modal fade"
     id="<?= ImagesDevModalWidget::getModalWindowName() ?>"
     tabindex="-1"
     role="dialog"
     data-backdrop="static"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <?php Pjax::begin([
            'options' => [
                'id'                         => ImagesDevModalWidget::getPjaxContainerId(),
                'class'                      => 'pjax-container',
                'data-return-url'            => '0',
                'data-return-url-fields'     => '0',
                'data-return-url-validators' => '0',
            ],
        ]); ?>
        <?php $form = ActiveForm::begin([
            'id'      => ImagesDevModalWidget::getFormName(),
            'action'  => $widget->action,
            'options' => [
                'data-pjax'                     => true,
                'data-yicms-saved'              => $widget->dataSaved,
                'data-save-and-exit'            => $widget->saveAndExit,
                'data-redirect-to-update-image' => $redirectToUpdate,
                'data-image-block-id-redirect'  => $imageBlockIdForRedirect
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
                    <div class="col-sm-4 col-xs-12">
                        <br>
                        <p>zero value - infinite count of images in block</p>
                    </div>
                    <?php /* ?>
                    <div class="col-sm-4 col-xs-12">
                        <br>
                        <?= $form->field($widget->devImagesGroup->imagesBlock, 'fill_color') ?>
                    </div>
                    */ ?>
                </div>

                <div class="row">
                    <div class="col-sm-4 col-xs-12 ">
                        <?= $form->field($widget->devImagesGroup->imagesBlock, 'crop_type')->dropDownList(
                            ImagesBlock::getCropTypes()
                        ) ?>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <?= $form->field($widget->devImagesGroup->imagesBlock, 'crop_width') ?>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <?= $form->field($widget->devImagesGroup->imagesBlock, 'crop_height') ?>
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
                    <div class="row delete-button-row">
                        <div class="col-xs-12">

                            <br>

                            <p>IMPORTANT! Do not delete images blocks without serious reason!</p>
                            <button type="button"
                                    class="btn btn-danger"
                                    id="image-delete"
                                    data-image-template-reference="<?= $widget->devImagesGroup->imagesBlock->image_template_reference ?>"
                                    data-image-template-id="<?= $widget->devImagesGroup->imagesBlock->id ?>"
                                    data-image-block-has-constraints="<?= $widget->devImagesGroup->imagesBlock->isConstraints() ?>"
                                >
                                Delete image block template
                            </button>
                        </div>
                    </div>
                    <script type="text/template" id="delete-with-pass-template">
                        <div class="col-xs-12">
                            <br>
                            <label for="images-block-delete-password-input">
                                Images block has constraints. Enter dev password for delete images block template
                            </label>
                            <input type="password"
                                   id="images-block-delete-password-input"
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

                    <p class="btn btn-primary view-images-block-fields"
                       data-field-template-id="<?= $widget->devImagesGroup->imagesBlock->getFieldTemplateReference()  ?>"
                       data-return-url="<?= \yii\helpers\Url::toRoute([
                           '/common/dev-images/load-modal',
                           'imageTemplateId' => $widget->devImagesGroup->imagesBlock->id,
                       ]) ?>"
                        >
                        View image block fields
                    </p>

                    <span class="btn btn-primary view-images-block-conditions"
                          data-condition-template-id="<?= $widget->devImagesGroup->imagesBlock->getConditionTemplateReference() ?>"
                          data-return-url="<?= \yii\helpers\Url::toRoute([
                              '/common/dev-images/load-modal',
                              'imageTemplateId' => $widget->devImagesGroup->imagesBlock->id,
                          ]) ?>"
                    >
                        View image block conditions
                    </span>

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
                <?= Html::submitButton('Save and exit', ['class' => 'btn btn-success',
                    'value' => 'true', 'name' => '_saveAndExit']) ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        <?php Pjax::end() ?>
    </div>
</div>
