<?php

use yii\widgets\Pjax;

$js = <<<JS
;(function() {
    $('#files-pjax-container').on('pjax:complete', function() {
        if ($(this).data('needToRefreshFilesList')) {
            $(this).data('needToRefreshFilesList', 0);

            $.pjax({
                container: '#files-list-pjax-container',
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500,
            });
        }
    });

    $('#files-list-pjax-container').on('pjax:complete', function() {

        var filesPjaxContainer = $('#files-pjax-container');

        if ($(filesPjaxContainer).data('needRedirectFromCreateToUpdate')) {

            $(filesPjaxContainer).data('needRedirectFromCreateToUpdate', 0);

            var homeUrl = $(filesPjaxContainer).data('homeUrl');
            var fileId  = $(filesPjaxContainer).data('needToUpdateFileId');

            var updateFileUrl = homeUrl + 'common/admin-files/update-file';

            $.pjax({
                 url: updateFileUrl  + '?fileId=' + fileId,
                 container: '#files-pjax-container',
                 scrollTo: false,
                 push: false,
                 type: "POST",
                 timeout: 2500
             });

            return;
        }

        if ($(filesPjaxContainer).data('needToGoBack')) {

            $(filesPjaxContainer).data('needToGoBack', 0);

            $.pjax({
                url: $(filesPjaxContainer).data('returnUrl'),
                container: '#files-pjax-container',
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

?>

<div class="modal fade"
     id="files-modal"
     tabindex="-1"
     role="dialog"
     data-backdrop="static"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <?php Pjax::begin([
            'options' => [
                'id'                                       => 'files-pjax-container',
                'class'                                    => 'pjax-container',
                'data-home-url'                            => Yii::$app->homeUrl,
                'data-return-url'                          => '0',
                'data-need-to-refresh-files-list'          => '0',
                'data-need-redirect-from-create-to-update' => '0',
                'data-need-to-update-file-id'              => '0',
                'data-need-to-go-back'                     => '0',
            ],
            'enablePushState'    => false,
            'enableReplaceState' => false,
        ]); ?>
        <?php Pjax::end() ?>
    </div>
</div>
