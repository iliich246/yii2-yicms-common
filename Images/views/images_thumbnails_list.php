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

    var addNewThumbnailConfiguratorUrl = homeUrl + '/common/dev-images/add-new-thumbnail-configurator';
    var updateThubnailConfiguratorUrl  = homeUrl + '/common/dev-images/update-thumbnail-configurator';




})();
JS;

$this->registerJs($js);

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="myModalLabel">
            Thumbnails

            <span class="glyphicon glyphicon-arrow-left validator-form-back" aria-hidden="true" style="float: right;margin-right: 20px"></span>
        </h3>

    </div>
    <div class="modal-body thumbnails-modal-body"
         data-home-url="<?= \yii\helpers\Url::base() ?>"
         data-file-block-id="<?= 1?>"
         data-file-reference="<?= 1 ?>"
         data-return-url="<?= \yii\helpers\Url::toRoute(
             [
                 'files-list',
                 'fileBlockId' =>1,
                 'fileReference' => 1,
             ]) ?>"
    >
        <button class="btn btn-primary add-file-button">
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
