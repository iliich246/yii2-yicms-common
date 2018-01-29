<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsCommon\Images\ImagesThumbnails;

/** @var $this \yii\web\View */
/** @var $imagesBlock \Iliich246\YicmsCommon\Images\ImagesBlock */
/** @var $thumbnail \Iliich246\YicmsCommon\Images\ImagesThumbnails */

if (isset($returnBack)) $return = 'true';
else $return = 'false';

$js = <<<JS
;(function() {

    var thumbnailForm = $('.thumbnail-form');
    var deleteButton  = $('#thumbnail-delete');

    var pjaxContainer   = $(thumbnailForm).parent('.pjax-container');
    var pjaxContainerId = '#' + $(pjaxContainer).attr('id');

    var homeUrl  = $(thumbnailForm).data('homeUrl');
    var returnUrl = $(thumbnailForm).data('returnUrl');
    var deleteUrl = homeUrl + '/common/dev-images/delete-thumbnail-configurator';

    var isReturn = $(thumbnailForm).data('returnBack');

    if (isReturn) goBack();

    $('.thumbnail-form-back').on('click', function(){
        goBack();
    });

    $(deleteButton).on('click', function() {
        if (!($(this).hasClass('thumbnail-confirm-state'))) {
            $(this).before('<span>Are you sure? </span>');
            $(this).text('Yes, I`am sure!');
            $(this).addClass('thumbnail-confirm-state');
        } else {
            $.ajax({
                type: 'POST',
                url: deleteUrl + '?thumbnailId=' + $(this).data('thumbnailId'),
                success: goBack,
                error: function(err) {
                    bootbox.alert({
                        size: 'large',
                        title: "Ajax error",
                        message: 'There are some error on ajax request!',
                        className: 'bootbox-error'
                    });
                }
            })
        }
    });

    function goBack() {
        $.pjax({
            url: returnUrl,
            container: pjaxContainerId,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500,
        });
    }
})();
JS;

$this->registerJs($js);

?>

<?php $form = ActiveForm::begin([
    'id' => 'create-update-thumbnail-form',
    'options' => [
        'class'            => 'thumbnail-form',
        'data-pjax'        => true,
        'data-home-url'    => \yii\helpers\Url::base(),
        'data-return-url' => \yii\helpers\Url::toRoute([
            '/common/dev-images/show-thumbnails-list',
            'imageTemplateId' => $imagesBlock->id,
        ]),
        'data-return-back' => $return
    ],
]);
?>

<div class="modal-content thumbnails-edit-block">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title">

            <?php if ($thumbnail->scenario == ImagesThumbnails::SCENARIO_CREATE): ?>
            Create thumbnail configurator
            <?php else: ?>
            Edit thumbnail configurator <?= $thumbnail->program_name ?>
            <?php endif; ?>

            <span class="glyphicon glyphicon-arrow-left thumbnail-form-back" aria-hidden="true" style="float: right;margin-right: 20px"></span>
        </h3>
    </div>
    <div class="modal-body">

        <?= $form->field($thumbnail, 'program_name') ?>

        <?= $form->field($thumbnail, 'divider') ?>

        <?= $form->field($thumbnail, 'quality') ?>

        <?php if ($thumbnail->scenario == ImagesThumbnails::SCENARIO_UPDATE): ?>
        <button type="button"
                class="btn btn-danger"
                data-thumbnail-id="<?= $thumbnail->id ?>"
                id="thumbnail-delete">
            Delete thumbnail configurator
        </button>
        <?php endif; ?>

    </div>
    <div class="modal-footer">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::submitButton('Save and back',
            ['class' => 'btn btn-success',
                'value' => 'true', 'name' => '_saveAndBack']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
<?php ActiveForm::end(); ?>
