<?php

use yii\widgets\Pjax;

$js = <<<JS
;(function() {
    $('#images-pjax-container').on('pjax:complete', function() {
        if ($(this).data('needToRefreshImagesList')) {
            $(this).data('needToRefreshImagesList', 0);

            $.pjax({
                container: '#images-list-pjax-container',
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500,
            });
        }
    });

    $('#images-list-pjax-container').on('pjax:complete', function() {
        var imagesPjaxContainer = $('#images-pjax-container');

        if ($(imagesPjaxContainer).data('needRedirectFromCreateToUpdate')) {

            $(imagesPjaxContainer).data('needRedirectFromCreateToUpdate', 0);

            var homeUrl  = $(imagesPjaxContainer).data('homeUrl');
            var imageId  = $(imagesPjaxContainer).data('needToUpdateImageId');

            var updateImageUrl = homeUrl + 'common/admin-images/update-image';

            $.pjax({
                 url: updateImageUrl  + '?imageId=' + imageId,
                 container: '#images-pjax-container',
                 scrollTo: false,
                 push: false,
                 type: "POST",
                 timeout: 2500,
             });
            
            return;
        }
        
        if ($(imagesPjaxContainer).data('needToGoBack')) {
            
            $(imagesPjaxContainer).data('needToGoBack', 0);            
        
            $.pjax({
                url: $(imagesPjaxContainer).data('returnUrl'),
                container: '#images-pjax-container',
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500,
            });
        }
    });
})();
JS;

$this->registerJs($js);
?>

<div class="modal fade"
     id="images-modal"
     tabindex="-1"
     role="dialog"
     data-backdrop="static"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <?php Pjax::begin([
            'options' => [
                'id'                                       => 'images-pjax-container',
                'class'                                    => 'pjax-container',
                'data-home-url'                            => Yii::$app->homeUrl,
                'data-return-url'                          => '0',
                'data-need-to-refresh-images-list'         => '0',
                'data-need-redirect-from-create-to-update' => '0',
                'data-need-to-update-image-id'             => '0',
                'data-need-to-go-back'                     => '0',
            ],
            'enablePushState'    => false,
            'enableReplaceState' => false,
        ]); ?>
        <?php Pjax::end() ?>
    </div>
</div>
