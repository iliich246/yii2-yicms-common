<?php //template

use yii\helpers\Url;

/** @var $this \yii\web\View */
/** @var $widget \app\yicms\Common\Widgets\ModuleMenuWidget */
/** @var $freeEssences \Iliich246\YicmsCommon\FreeEssences\FreeEssences[] */
?>

<?php if ($freeEssences): ?>
<div class="row link-block">
    <div class="col-xs-12">
        <h2>Free essences list</h2>
        <?php foreach($freeEssences as $freeEssence): ?>
            <a <?php if ($widget->isActive($freeEssence)): ?> class="active" <?php endif; ?>
                href="<?= Url::toRoute(['/common/admin/edit-free-essence', 'id' => $freeEssence->id]) ?>">
                <?= $freeEssence->name() ?>
                <?php if (\Iliich246\YicmsCommon\Base\CommonUser::isDev() && !$freeEssence->editable): ?>
                    (dev only)
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<hr>
<?php endif; ?>

