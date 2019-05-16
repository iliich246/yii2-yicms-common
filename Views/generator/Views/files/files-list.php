<?php //template

/** @var $this yii\web\View */
/** @var $fileBlock \Iliich246\YicmsCommon\Files\FilesBlock */
/** @var $fileReference string */
/** @var $filesList \Iliich246\YicmsCommon\Files\File[] */
/** @var $file \Iliich246\YicmsCommon\Files\File */

$js = <<<JS
;(function() {
    var addFile       = $('.files-modal-body');
    var addFileButton = $('.add-file-button');

    var homeUrl            = $(addFile).data('homeUrl');
    var pjaxContainerName  = '#' + $(addFile).data('pjaxContainerName');
    var pjaxFilesModalName = '#' + $(addFile).data('filesModalName');
    var fileBlockId        = $(addFile).data('fileBlockId');
    var fileReference      = $(addFile).data('fileReference');

    var loadNewFileUrl = homeUrl + '/common/admin-files/load-new-file';
    var updateFileUrl  = homeUrl + '/common/admin-files/update-file';
    var upFileOrder    = homeUrl + '/common/admin-files/up-file-order';
    var downFileOrder  = homeUrl + '/common/admin-files/down-file-order';

    var returnUrl = $(addFile).data('returnUrl');

    $(pjaxContainerName).data('returnUrl', returnUrl);

    $(addFileButton).on('click', function() {
        $.pjax({
            url: loadNewFileUrl + '?fileBlockId=' + fileBlockId + '&fileReference=' + fileReference,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.file-block-item', function() {
        killEventListeners();
        $.pjax({
            url: updateFileUrl + '?fileId=' + $(this).data('fileId'),
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.file-up-order', function() {
        killEventListeners();
        $.pjax({
            url: upFileOrder + '?fileId=' + $(this).data('fileId'),
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.file-down-order', function() {
        killEventListeners();
        $.pjax({
            url: downFileOrder + '?fileId=' + $(this).data('fileId'),
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
        $(addFileButton).off('click');
        $(document).off('click', '.file-block-item');
        $(document).off('click', '.file-up-order');
        $(document).off('click', '.file-down-order');
    }
})();
JS;

$this->registerJs($js);

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title" id="myModalLabel">
            <?= $fileBlock->getName() ?>
        </h3>
        <h4>
            <?= $fileBlock->getDescription() ?>
        </h4>
    </div>

    <div class="modal-body files-modal-body"
         data-home-url="<?= \yii\helpers\Url::base() ?>"
         data-pjax-container-name="files-pjax-container"
         data-files-modal-name="files-modal"
         data-file-block-id="<?= $fileBlock->id ?>"
         data-file-reference="<?= $fileReference ?>"
         data-return-url="<?= \yii\helpers\Url::toRoute(
             [
                 'files-list',
                 'fileBlockId' => $fileBlock->id,
                 'fileReference' => $fileReference,
             ]) ?>"
        >
            <button class="btn btn-primary add-file-button">
                Add new file
            </button>
            <hr>
        <?php foreach($filesList as $file): ?>
            <div class="row list-items">
                <div class="col-xs-10 list-title">
                    <p data-file-block-id="<?= $fileBlock->id ?>"
                       data-file-id="<?= $file->id ?>"
                       class="file-block-item">
                        <?= $file->getFileName() ?>
                    </p>
                </div>
                <div class="col-xs-2 list-controls">
                    <span class="glyphicon glyphicon-eye-open"></span>

                    <?php if ($file->canUpOrder()): ?>
                        <span class="glyphicon file-up-order glyphicon-arrow-up"
                              data-file-id="<?= $file->id ?>"></span>
                    <?php endif; ?>
                    <?php if ($file->canDownOrder()): ?>
                        <span class="glyphicon file-down-order glyphicon-arrow-down"
                              data-file-id="<?= $file->id ?>"></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>
