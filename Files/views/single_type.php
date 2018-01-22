<?php

use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;
use Iliich246\YicmsCommon\Files\FilesGroup;

/** @var $this yii\web\View */
/** @var $filesGroup \Iliich246\YicmsCommon\Files\FilesGroup */
/** @var $filesBlock \Iliich246\YicmsCommon\Files\FilesBlock */
?>

<?php if ($filesGroup->scenario == FilesGroup::SCENARIO_CREATE): ?>
    <?= $form->field($filesGroup->fileEntity, "file")->fileInput()->label('load new file') ?>
<?php else: ?>
    <?= $form->field($filesGroup->fileEntity, "file")->fileInput()->label('replace existed file') ?>
    <a href="#">Load existed file</a>
<?php endif; ?>

<?= SimpleTabsTranslatesWidget::widget([
    'form' => $form,
    'translateModels' => $filesGroup->translateForms,
    'data' => [$filesGroup->fileBlock, $filesGroup],
])
?>
