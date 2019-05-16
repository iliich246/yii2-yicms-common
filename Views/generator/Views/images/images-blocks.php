<?php //template

use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Images\ImagesBlock;

/** @var $this yii\web\View */
/** @var $imagesBlocks ImagesBlock[] */
/** @var $imageReference string */

$js = <<<JS
;(function() {
    var imagesListBlock = $('.images-list-block');

    var imageReference = $(imagesListBlock).data('imageReference');

    var homeUrl         = $(imagesListBlock).data('homeUrl');
    var imagesListUrl   = homeUrl + '/common/admin-images/images-list';
    var loadNewImageUrl = homeUrl + '/common/admin-images/load-new-image';
    var updateImageUrl  = homeUrl + '/common/admin-images/update-image';

    var pjaxContainerName  = '#' + $(imagesListBlock).data('pjaxContainerName');
    var pjaxFilesModalName = '#' + $(imagesListBlock).data('imagesModalName');
    var imageLoaderScr     = $(imagesListBlock).data('loaderImageSrc');

    var sendTimeout;

    $(pjaxContainerName).on('pjax:send', function() {
        sendTimeout = setTimeout(function() {
            $(pjaxFilesModalName)
                .find('.modal-content')
                .empty()
                .append('<img src="' + imageLoaderScr + '" style="text-align:center">');
        }, 50);
    });

    $(document).on('click', '.image-block-item', function() {
        var _this         = this;
        var imagesBlockId = $(this).data('imagesBlockId');

        if (!$(this).data('type'))
            handleManyImages();
        else
            handleSingleImage();

        function handleManyImages() {

            $.pjax({
                url: imagesListUrl + '?imagesBlockId=' + imagesBlockId + '&imageReference=' + imageReference,
                container: pjaxContainerName,
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500
            });

            $(pjaxFilesModalName).modal('show');
        }

        function handleSingleImage() {

            var imageBlockId   = $(_this).data('imagesBlockId');
            var imageReference = $(_this).data('imageReference');
            var imageId        = $(_this).data('imageId');

            $(pjaxContainerName).data('returnUrl', null);

            if ($(_this).data('isImageInBlock')) {
                $.pjax({
                    url: updateImageUrl + '?imageId=' + imageId,
                    container: pjaxContainerName,
                    scrollTo: false,
                    push: false,
                    type: "POST",
                    timeout: 2500
                });
            } else {
                $.pjax({
                    url: loadNewImageUrl + '?imagesBlockId=' + imageBlockId + '&imageReference=' + imageReference,
                    container: pjaxContainerName,
                    scrollTo: false,
                    push: false,
                    type: "POST",
                    timeout: 2500
                });
            }

            $(pjaxFilesModalName).modal('show');
        }
    });

    $(pjaxFilesModalName).on('hidden.bs.modal', function() {
        $(pjaxContainerName).empty();
    });
})();
JS;

$this->registerJs($js);

$bundle = \Iliich246\YicmsCommon\Assets\DeveloperAsset::register($this);
$src = $bundle->baseUrl . '/loader.svg';

?>
<?php Pjax::begin([
    'options' => [
        'id' => 'images-list-pjax-container'
    ],
    'enablePushState'    => false,
    'enableReplaceState' => false
]) ?>
<div class="list-block images-list-block"
     data-home-url="<?= \yii\helpers\Url::base() ?>"
     data-pjax-container-name="images-pjax-container"
     data-images-modal-name="images-modal"
     data-loader-image-src="<?= $src ?>"
     data-image-reference="<?= $imageReference ?>"
>
    <?php foreach ($imagesBlocks as $imagesBlock): ?>
    <div class="row list-items">
        <div class="col-xs-10 list-title">
            <p class="image-block-item"
               data-images-block-id="<?= $imagesBlock->id ?>"
               data-type="<?= $imagesBlock->type ?>"
               data-is-image-in-block="<?= (int)$imagesBlock->isEntities() ?>"
               data-image-reference="<?= $imageReference ?>"
               <?php if ($imagesBlock->isEntities()): ?>
               data-image-id="<?= $imagesBlock->getImage()->id ?>"
               <?php else: ?>
               data-image-id="0"
               <?php endif; ?>
               <?php if ($imagesBlock->getDescription()): ?>
               data-toggle="tooltip"
               data-placement="top"
               title="<?= $imagesBlock->getDescription() ?>"
               <?php endif; ?>
            >
                <?= $imagesBlock->getName() ?>
            </p>

        </div>
        <div class="col-xs-2 list-controls">
            <span class="glyphicon glyphicon-eye-open"></span>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php Pjax::end() ?>
