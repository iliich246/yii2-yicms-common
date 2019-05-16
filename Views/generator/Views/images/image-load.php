<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Images\ImagesGroup;
use Iliich246\YicmsCommon\Images\ImagesBlock;

/** @var $this yii\web\View */
/** @var $imagesBlock \Iliich246\YicmsCommon\Images\ImagesBlock */
/** @var $imagesGroup \Iliich246\YicmsCommon\Images\ImagesGroup*/
/** @var $fieldsGroup \Iliich246\YicmsCommon\Fields\FieldsGroup */
/** @var $filesGroup \Iliich246\YicmsCommon\Files\FilesGroup */
/** @var $conditionsGroup \Iliich246\YicmsCommon\Conditions\ConditionsGroup */

$js = <<<JS
;(function() {
    var imageLoadForm = $('#image-load-form');
    var imageLoadBack = $('.image-load-back');

    var homeUrl = $(imageLoadForm).data('homeUrl');

    var pjaxContainer   = $(imageLoadForm).closest('.pjax-container');
    var pjaxContainerId = '#' + $(pjaxContainer).attr('id');

    var imageBlockId = $(imageLoadForm).data('fileBlockId');

    var isReturn  = $(imageLoadForm).data('returnBack');
    var returnUrl = $(pjaxContainer).data('returnUrl');

    var updateImageUrl = '/common/admin-images/update-image';
    var updateRedirect = $(imageLoadForm).data('updateRedirect');
    var imageId        = $(imageLoadForm).data('imageId');
    var cropMode       = $(imageLoadForm).data('cropMode');
    var cropHeight     = $(imageLoadForm).data('cropHeight');
    var cropWidth      = $(imageLoadForm).data('cropWidth');

    if (isReturn) return goBack();

    if (updateRedirect) return redirectToUpdateImage();

    if (returnUrl) {
        $(imageLoadBack).css('display', 'block');

        $(imageLoadBack).on('click', function() {
            $.pjax({
                url: returnUrl,
                container: '#images-pjax-container',
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500,
            });
        });
    }

    var modalImageLoader = $('.modal-of-image-loader');
    var modalCrop        = $('.modal-of-crop');

    $('.test-c').on('click', function() {
        $('.modal1').hide();
        $('.modal2').show();
    });

    $('.cropper-back').on('click', function() {
        $(modalCrop).hide();
        $(modalImageLoader).show();
    });

    if (cropMode == -1) noCropImageHandler();
    else cropImageHandler();

    function goBack() {
        var imagesPjaxContainer = $('#images-pjax-container');
        
        $(imagesPjaxContainer).data('needToGoBack', 1);
        $(imagesPjaxContainer).data('backUrlData', returnUrl);
    }

    function redirectToUpdateImage() {
        var imagesPjaxContainer = $('#images-pjax-container');

        $(imagesPjaxContainer).data('needRedirectFromCreateToUpdate', 1);
        $(imagesPjaxContainer).data('needToUpdateImageId', imageId);
    }

    function noCropImageHandler() {
        var imageLoaders = $('.image-loader-button');

        $(imageLoaders).each(function(index, imageLoader) {

            var preview = $(imageLoader).parent().next().children();

            $(imageLoader).change(function(event) {
                var fileReader = new FileReader();
                fileReader.readAsDataURL(this.files[0]);

                fileReader.onloadend = function() {
                    var image = $(preview).find('img');
                    if (image) $(image).remove();

                    $(preview).append("<img src='" +fileReader.result + "' style=\"margin-bottom: 10px;width: 100%\">");
                }
            });
        });
    }

    function cropImageHandler() {

        var imageLoaders = $('.image-loader-button');

        var cropImageContainer = $('.crop-image-container');
        var cropButton         = $('.crop-button');

        $(imageLoaders).each(function(index, imageLoader) {
            $(imageLoader).change(function(event) {
                var fileReader = new FileReader();
                fileReader.readAsDataURL(this.files[0]);

                console.log('yes crop');

                var cropInfo = $(imageLoader).parent().next().next().children().first();
                var preview  = $(imageLoader).parent().next().children();

                console.log(cropInfo);

                fileReader.onloadend = function() {

                    $(cropImageContainer).cropper('destroy');

                    $(cropImageContainer).attr('src', fileReader.result);

                    $(cropImageContainer).cropper({
                        aspectRatio: cropWidth / cropHeight,
                        autoCrop: true,
                        autoCropArea: 0.5,
                        viewMode: cropMode,
                        responsive: true,
                        dragMode: 'move'
                    });
                };

                $(cropButton).on('click', function() {

                    $(cropInfo).val(JSON.stringify($(cropImageContainer).cropper('getData')));
                    $(preview).empty();
                    $(preview).append($(cropImageContainer).cropper('getCroppedCanvas'));
                    $(this).off('click');
                    $(modalImageLoader).show();
                    $(modalCrop).hide();
                });

                $(modalImageLoader).hide();
                $(modalCrop).show();
            });
        });
    }

    $('.image-save-button').click(function() {
        $(pjaxContainer).data('needToRefreshImagesList', 1);
    });

    $('#image-delete').on('click', function() {
        var button = ('#image-delete');

        if (!$(button).is('[data-image-id]')) return;

        if (!($(this).hasClass('image-confirm-state'))) {
            $(this).before('<span>Are you sure? This action will delete image</span>');
            $(this).text('Yes, I`am sure!');
            $(this).addClass('image-confirm-state');
        } else {
            $.pjax({
                url: $(this).data('deleteUrl'),
                container: '#images-pjax-container',
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500
            });
        }
    });
})();
JS;

$this->registerJs($js);

if (isset($returnBack)) $return = 'true';
else $return = 'false';

if (isset($updateRedirect)) $toUpdate = 'true';
else $toUpdate = 'false';

if (isset($imageIdForRedirect)) $imageId = $imageIdForRedirect;
else $imageId = '0';

if ($imagesBlock->crop_type != ImagesBlock::NO_CROP)
    $this->registerAssetBundle(\Iliich246\YicmsCommon\Assets\CropperAsset::className());

?>

<div class="modal-content">
    <div class="modal-of-image-loader">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <?php if ($imagesGroup->scenario == ImagesGroup::SCENARIO_CREATE): ?>
            <h3>Create new image
        <?php else: ?>
            <h3>Update image
        <?php endif; ?>

        <span class="glyphicon glyphicon-arrow-left image-load-back" aria-hidden="true"
              style="float: right;margin-right: 20px;display: none"></span>

        </h3>
    </div>

    <?php $form = ActiveForm::begin([
        'id'      => 'image-load-form',
        'options' => [
            'class'                => 'image-load-form',
            'data-pjax'            => true,
            'data-home-url'        => \yii\helpers\Url::base(),
            'data-images-block-id' => $imagesBlock->id,
            'data-return-back'     => $return,
            'data-update-redirect' => $toUpdate,
            'data-image-id'        => $imageId,
            'data-crop-mode'       => $imagesBlock->crop_type - 1,
            'data-crop-height'     => $imagesBlock->crop_height,
            'data-crop-width'      => $imagesBlock->crop_width,
        ],
    ]);
    ?>
    <div class="modal-body">

        <?= $imagesGroup->render($form) ?>

        <div class="row">
            <div class="col-sm-4 col-xs-12 ">
                <?= $form->field($imagesGroup->imageEntity, 'visible')->checkbox() ?>
            </div>
            <?php if (CommonModule::isUnderDev()): ?>
                <div class="col-sm-4 col-xs-12 ">
                    <?= $form->field($imagesGroup->imageEntity, 'editable')->checkbox() ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($imagesGroup->scenario == ImagesGroup::SCENARIO_UPDATE && $imagesGroup->imagesBlock->hasFields()): ?>

            <br>

            <hr>

            <h3>Fields of image:</h3>

            <?= $this->render(CommonModule::getInstance()->yicmsLocation  . '/Common/Views/pjax/fields-modal', [
                'fieldTemplateReference' => $imagesBlock->getFieldTemplateReference(),
                'fieldsGroup'            => $fieldsGroup,
                'form'                   => $form,
                'refreshUrl'             => \yii\helpers\Url::toRoute(
                    [
                        '/common/admin-images/update-image',
                        'imageId' => $imagesGroup->imageEntity->id,
                    ]),

            ]) ?>

        <?php endif; ?>

        <?php if ($imagesGroup->scenario == ImagesGroup::SCENARIO_UPDATE && $imagesGroup->imagesBlock->hasConditions()): ?>

            <br>

            <hr>

            <h3>Conditions of image:</h3>

            <?= $this->render(CommonModule::getInstance()->yicmsLocation  . '/Common/Views/conditions/conditions-modal', [
                'conditionsTemplateReference' => $imagesBlock->getConditionTemplateReference(),
                'conditionsGroup'             => $conditionsGroup,
                'form'                        => $form,
                'refreshUrl'                  => \yii\helpers\Url::toRoute(
                    [
                        '/common/admin-images/update-image',
                        'imageId' => $imagesGroup->imageEntity->id,
                    ]),
            ]) ?>


        <?php endif; ?>

        <?php if ($imagesBlock->type != ImagesBlock::TYPE_ONE_IMAGE): ?>
            <div class="row delete-button-row-images">
                <div class="col-xs-12">
                    <button type="button"
                            class="btn btn-danger"
                            id="image-delete"
                            data-delete-url="<?= \yii\helpers\Url::toRoute([
                                '/common/admin-images/delete-image',
                                'imageId' => $imagesGroup->imageEntity->id
                            ]) ?>"
                            data-image-id="<?= $imagesGroup->imageEntity->id ?>"
                        >
                        Delete image
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="modal-footer">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success image-save-button']) ?>

        <?php if ($imagesBlock->type != ImagesBlock::TYPE_ONE_IMAGE): ?>
            <?= Html::submitButton('Save and back',
                ['class' => 'btn btn-success image-save-button',
                    'value' => 'true', 'name' => '_saveAndBack']) ?>
        <?php endif; ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
    <?php ActiveForm::end(); ?>
    </div>
    <div class="modal-of-crop" style="display: none">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                <h3>CROPPER


                        <span class="glyphicon glyphicon-arrow-left cropper-back" aria-hidden="true"
                              style="float: right;margin-right: 20px"></span>

                </h3>
            </div>
            <div class="modal-body">
                <img src="" alt="" class="crop-image-container" style="max-width: 100%;">
            </div>
            <div class="modal-footer">
                <?= Html::submitButton('Crop', ['class' => 'btn btn-success crop-button']) ?>


                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
    </div>
</div>
