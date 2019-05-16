<?php //template

use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Files\FilesBlock;

/** @var $this yii\web\View */
/** @var $filesBlocks Iliich246\YicmsCommon\Files\FilesBlock[] */
/** @var $fileReference string */

$js = <<<JS
;(function() {
    var filesListBlock = $('.files-list-block');

    var fileReference = $(filesListBlock).data('fileReference');

    var homeUrl = $(filesListBlock).data('homeUrl');
    var filesListUrl = homeUrl + '/common/admin-files/files-list';
    var loadNewFileUrl = homeUrl + '/common/admin-files/load-new-file';
    var updateFileUrl = homeUrl + '/common/admin-files/update-file';

    var pjaxContainerName = '#' + $(filesListBlock).data('pjaxContainerName');
    var pjaxFilesModalName = '#' + $(filesListBlock).data('filesModalName');
    var imageLoaderScr = $(filesListBlock).data('loaderImageSrc');

    var sendTimeout;

    $(pjaxContainerName).on('pjax:send', function() {
        sendTimeout = setTimeout(function() {
            $(pjaxFilesModalName)
                .find('.modal-content')
                .empty()
                .append('<img src="' + imageLoaderScr + '" style="text-align:center">');
        }, 50);
    });

    $(pjaxContainerName).on('pjax:complete', function() {
        //clearTimeout(sendTimeout);
    });

    $(document).on('click', '.file-block-item-p', function() {
        var _this = this;

        if (!$(this).data('type'))
            handleManyFiles();
        else
            handleSingleFile();

        function handleManyFiles() {
            var fileBlockId = $(_this).data('fileBlockId');

            $.pjax({
                url: filesListUrl + '?fileBlockId=' + fileBlockId + '&fileReference=' + fileReference,
                container: pjaxContainerName,
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500
            });

            $(pjaxFilesModalName).modal('show');
        }

        function handleSingleFile() {

            var fileBlockId   = $(_this).data('fileBlockId');
            var fileReference = $(_this).data('fileReference');
            var fileId        = $(_this).data('fileId');

            $(pjaxContainerName).data('returnUrl', 0);

            if ($(_this).data('isFileInBlock')) {
                $.pjax({
                    url: updateFileUrl + '?fileId=' + fileId,
                    container: pjaxContainerName,
                    scrollTo: false,
                    push: false,
                    type: "POST",
                    timeout: 2500
                });
            } else {
                $.pjax({
                    url: loadNewFileUrl + '?fileBlockId=' + fileBlockId + '&fileReference=' + fileReference,
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

$js2 = <<<JS
;(function(){
    $('[data-toggle="tooltip"]').tooltip();
})();
JS;

?>

<?php Pjax::begin([
    'options' => [
        'id' => 'files-list-pjax-container'
    ],
    'enablePushState'    => false,
    'enableReplaceState' => false
]) ?>

<?php $this->registerJs($js2); ?>

<div class="list-block files-list-block"
     data-home-url="<?= \yii\helpers\Url::base() ?>"
     data-pjax-container-name="files-pjax-container"
     data-files-modal-name="files-modal"
     data-loader-image-src="<?= $src ?>"
     data-file-reference="<?= $fileReference ?>"
>
    <?php foreach($filesBlocks as $fileBlock): ?>
    <div class="row list-items">
        <div class="col-xs-10 list-title">
            <p class="file-block-item-p"
               data-file-block-id="<?= $fileBlock->id ?>"
               data-type="<?= $fileBlock->type ?>"
               data-is-file-in-block="<?= (int)$fileBlock->isEntities() ?>"
               data-file-reference="<?= $fileReference ?>"
               <?php if ($fileBlock->isEntities()): ?>
               data-file-id="<?= $fileBlock->getFile()->id ?>"
               <?php else: ?>
               data-file-id="0"
               <?php endif; ?>
               <?php if ($fileBlock->getDescription()): ?>
               data-toggle="tooltip"
               data-placement="top"
               title="<?= $fileBlock->getDescription() ?>"
               <?php endif; ?>
            >
                <?= $fileBlock->getName() ?>
            </p>

            <?php if ($fileBlock->type == FilesBlock::TYPE_ONE_FILE): ?>
                <?php if ($fileBlock->getFile()): ?>
                <a href="<?= $fileBlock->getFile()->uploadUrl() ?>" data-pjax="0">
                    Upload file "<?= $fileBlock->getFile()->getFileName() ?>"
                </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="col-xs-2 list-controls">
            <?php if ($fileBlock->visible): ?>
            <span class="glyphicon glyphicon-eye-open"></span>
            <?php else: ?>
            <span class="glyphicon glyphicon-eye-close"></span>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php Pjax::end() ?>
