<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;


/** @var $this \yii\web\View */
/** @var $thumbnails Iliich246\YicmsCommon\Images\ImagesThumbnails[] */
/** @var $imagesBlock Iliich246\YicmsCommon\Images\ImagesBlock */

$js = <<<JS
;(function() {
    var thumbnailForm = $('.thumbnails-modal-body');

    var homeUrl = $(thumbnailForm).data('homeUrl');

    var pjaxContainer   = $(thumbnailForm).closest('.pjax-container');
    var pjaxContainerId = '#' + $(pjaxContainer).attr('id');
    var returnUrl       = $(thumbnailForm).data('returnUrl');

    var addNewThumbnailConfiguratorUrl = homeUrl + '/common/dev-images/add-new-thumbnail-configurator';
    var updateThumbnailConfiguratorUrl = homeUrl + '/common/dev-images/update-thumbnail-configurator';

    var imageTemplateId = $(thumbnailForm).data('imageTemplateId');

    $('.thumbnail-list-form-back').on('click', function(){
        $(pjaxContainerId).data('returnUrl', returnUrl);

         $.pjax({
             url: returnUrl,
             container: pjaxContainerId,
             scrollTo: false,
             push: false,
             type: "POST",
             timeout: 2500,
         });
    });

    $('.add-thumbnail-button').on('click', function() {
         $.pjax({
             url: addNewThumbnailConfiguratorUrl + '?imageTemplateId=' + imageTemplateId,
             container: pjaxContainerId,
             scrollTo: false,
             push: false,
             type: "POST",
             timeout: 2500,
         });
    });

    $('.thumbnail-block-item').on('click', function() {
         $.pjax({
             url: updateThumbnailConfiguratorUrl + '?thumbnailId=' + $(this).data('thumbnailId'),
             container: pjaxContainerId,
             scrollTo: false,
             push: false,
             type: "POST",
             timeout: 2500,
         });
    });
})();
JS;

$this->registerJs($js);

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="myModalLabel">
            Thumbnails

            <span class="glyphicon glyphicon-arrow-left thumbnail-list-form-back" aria-hidden="true" style="float: right;margin-right: 20px"></span>
        </h3>

    </div>
    <div class="modal-body thumbnails-modal-body"
         data-home-url="<?= \yii\helpers\Url::base() ?>"
         data-image-template-id="<?= $imagesBlock->id ?>"
         data-return-url="<?= \yii\helpers\Url::toRoute(
             [
                 '/common/dev-images/load-modal',
                 'imageTemplateId' => $imagesBlock->id,
             ]) ?>"
    >
        <button class="btn btn-primary add-thumbnail-button">
            Add new thumbnail configurator
        </button>
        <hr>
        <?php foreach($thumbnails as $thumbnail): ?>
            <div class="row list-items">
                <div class="col-xs-10 list-title">
                    <p data-thumbnail-id="<?= $thumbnail->id ?>"
                       class="thumbnail-block-item">
                        <?= $thumbnail->program_name ?>
                    </p>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>
