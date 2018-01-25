<?php

use Iliich246\YicmsCommon\Files\FilesGroup;
use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;

/** @var $this yii\web\View */
/** @var $filesGroup \Iliich246\YicmsCommon\Files\FilesGroup */
/** @var $filesBlock \Iliich246\YicmsCommon\Files\FilesBlock */

?>

<?php if ($filesGroup->scenario == FilesGroup::SCENARIO_CREATE): ?>
    <?= $form->field($filesGroup->fileEntity, "file")->fileInput()->label('load new file') ?>
<?php else: ?>
    <?php if ($filesGroup->fileEntity->getPath()): ?>
    <?= $form->field($filesGroup->fileEntity, "file")->fileInput()->label('replace existed file') ?>
        <a href="<?= $filesGroup->fileEntity->uploadUrl() ?>"
           data-pjax="0">
            Upload file "<?= $filesGroup->fileEntity->getFileName(null, true) ?>"
        </a>
        <br>
        <br>
    <?php else: ?>
        <?= $form->field($filesGroup->fileEntity, "file")->fileInput()->label('load file') ?>
    <?php endif; ?>
<?php endif; ?>

<?= SimpleTabsTranslatesWidget::widget([
    'form' => $form,
    'translateModels' => $filesGroup->translateForms,
    'data' => [$filesGroup->fileBlock, $filesGroup],
])
?>
