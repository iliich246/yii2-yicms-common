<?php

/* @var $widget \Iliich246\YicmsCommon\Widgets\ReloadAlertWidget */
/* @var $this \yii\web\View */

$js = <<< JS
$(function() {
    var el = $('#reload-alert');

    if(el.length == 0) return;

    var title = $(el).data('title');
    var message = $(el).data('message');
    var type  = $(el).data('classType');

    bootbox.dialog({
        message: message,
        title: title,
        className: type,
        buttons: {
            success: {
                label: "Ok",
                className: "btn btn-default send-order"
            }
        }
    });
});
JS;

if ($widget->type !== null) {
    $this->registerAssetBundle(\Iliich246\YicmsCommon\Assets\BootboxAsset::className());
    $this->registerJs($js);
}
?>

<?php if ($widget->type !== null) {  ?>
<input type="hidden" id="reload-alert"
   data-title="<?= $widget->getTitle() ?>"
   data-message="<?= $widget->getMessage() ?>"
   data-class-type="<?= $widget->getTypeClass() ?>"
/>
<?php } ?>