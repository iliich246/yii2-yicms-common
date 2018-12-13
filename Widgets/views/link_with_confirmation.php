<?php

/* @var $this \yii\web\View */
/* @var $widget \Iliich246\YicmsCommon\Widgets\LinkWithConfirmationWidget */

$this->registerAssetBundle(\Iliich246\YicmsCommon\Assets\LinkWithConfirmationAsset::className())
?>

<?php if (!$widget->glyphicon): ?>
    <?php if ($widget->withBlock): ?>
        <div class="row control-buttons">
        <div class="col-xs-12">
    <?php endif; ?>
    <a href="<?= $widget->url ?>"
       class="btn-boot-box btn btn-danger"
       data-title="<?= $widget->title ?>"
       data-url="<?= $widget->url ?>"
       data-message="<?= $widget->message ?>"
       data-ok-label="<?= $widget->okLabel ?>"
       data-cancel-label="<?= $widget->cancelLabel ?>"
       data-via-pjax="<?= $widget->viaPjax ?>"
       data-pjax-container="<?= $widget->pjaxContainer ?>">
        <?= $widget->caption ?>
    </a>
    <?php if ($widget->withBlock): ?>
        </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <a href="<?= $widget->url ?>"
       class="btn-boot-box"
       data-title="<?= $widget->title ?>"
       data-url="<?= $widget->url ?>"
       data-message="<?= $widget->message ?>"
       data-ok-label="<?= $widget->okLabel ?>"
       data-cancel-label="<?= $widget->cancelLabel ?>"
       data-via-pjax="<?= $widget->viaPjax ?>"
       data-pjax-container="<?= $widget->pjaxContainer ?>"
       style="color: red;
       float: right"
    >
        <span class="glyphicon <?= $widget->glyphicon ?>" aria-hidden="true"></span>
    </a>
<?php endif; ?>
