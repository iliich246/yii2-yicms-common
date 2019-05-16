<?php //template

/** @var $this yii\web\View */
/** @var $imagesBlock \Iliich246\YicmsCommon\Images\ImagesBlock */
/** @var $imageReference string */
/** @var $imagesList \Iliich246\YicmsCommon\Images\Image[] */
/** @var $image \Iliich246\YicmsCommon\Images\Image */

$js = <<<JS
;(function() {
    var addImage       = $('.images-modal-body');
    var addImageButton = $('.add-image-button');

    var homeUrl            = $(addImage).data('homeUrl');
    var pjaxContainerName  = '#' + $(addImage).data('pjaxContainerName');
    var pjaxFilesModalName = '#' + $(addImage).data('filesModalName');
    var imagesBlockId      = $(addImage).data('imagesBlockId');
    var imageReference     = $(addImage).data('imageReference');

    var loadNewImageUrl = homeUrl + '/common/admin-images/load-new-image';
    var updateImageUrl  = homeUrl + '/common/admin-images/update-image';
    var upImageOrder    = homeUrl + '/common/admin-images/up-image-order';
    var downImageOrder  = homeUrl + '/common/admin-images/down-image-order';

    var returnUrl = $(addImage).data('returnUrl');

    $(pjaxContainerName).data('returnUrl', returnUrl);

    $(addImageButton).on('click', function() {
        $.pjax({
            url: loadNewImageUrl + '?imagesBlockId=' + imagesBlockId + '&imageReference=' + imageReference,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.image-list-item', function() {
        killEventListeners();
        //$('#images-pjax-container').data('returnUrl', 0);

        $.pjax({
            url: updateImageUrl + '?imageId=' + $(this).data('imageId'),
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.image-up-order', function() {
        killEventListeners();
        $.pjax({
            url: upImageOrder + '?imageId=' + $(this).data('imageId'),
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.image-down-order', function() {
        killEventListeners();
        $.pjax({
            url: downImageOrder + '?imageId=' + $(this).data('imageId'),
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(pjaxFilesModalName).on('hide.bs.modal', function() {
        killEventListeners();
        $(pjaxContainerName).data('returnUrl', '0');
        $(pjaxFilesModalName).off('hide.bs.modal');
        $(pjaxContainerName).empty();
    });

    function killEventListeners() {
        $(addImageButton).off('click');
        $(document).off('click', '.image-list-item');
        $(document).off('click', '.image-up-order');
        $(document).off('click', '.image-down-order');
    }
})();
JS;

$this->registerJs($js);

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="myModalLabel">
            <?= $imagesBlock->getName() ?>
        </h3>
        <h4>
            <?= $imagesBlock->getDescription() ?>
        </h4>
    </div>
    <div class="modal-body images-modal-body"
         data-home-url="<?= \yii\helpers\Url::base() ?>"
         data-pjax-container-name="images-pjax-container"
         data-images-modal-name="images-modal"
         data-images-block-id="<?= $imagesBlock->id ?>"
         data-image-reference="<?= $imageReference ?>"
         data-return-url="<?= \yii\helpers\Url::toRoute(
             [
                 'images-list',
                 'imagesBlockId'  => $imagesBlock->id,
                 'imageReference' => $imageReference,
             ]) ?>"
        >
        <button class="btn btn-primary add-image-button">
            Add new image
        </button>
        <hr>

        <?php foreach($imagesList as $image): ?>
            <div class="row list-items">
                <div class="col-xs-10 list-title">
                    <p data-images-block-id="<?= $imagesBlock->id ?>"
                       data-image-id="<?= $image->id ?>"
                       class="image-list-item">
                        <?= $image->listName() ?>
                    </p>
                </div>
                <div class="col-xs-2 list-controls">
                    <span class="glyphicon glyphicon-eye-open"></span>

                    <?php if ($image->canUpOrder()): ?>
                        <span class="glyphicon image-up-order glyphicon-arrow-up"
                              data-image-id="<?= $image->id ?>"></span>
                    <?php endif; ?>
                    <?php if ($image->canDownOrder()): ?>
                        <span class="glyphicon image-down-order glyphicon-arrow-down"
                              data-image-id="<?= $image->id ?>"></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>
