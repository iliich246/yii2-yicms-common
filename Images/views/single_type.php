<?php

use Iliich246\YicmsCommon\Images\ImagesGroup;
use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;

/** @var $this yii\web\View */
/** @var $imagesGroup \Iliich246\YicmsCommon\Images\ImagesGroup */
/** @var $imagesBlock \Iliich246\YicmsCommon\Images\ImagesBlock */

?>

<?php if ($imagesGroup->scenario == ImagesGroup::SCENARIO_CREATE): ?>
    <?= $form->field($imagesGroup->imageEntity, "image")->fileInput()->label('load new image') ?>
<?php else: ?>
    <?php if ($imagesGroup->imageEntity->getPath()): ?>
        <?= $form->field($imagesGroup->imageEntity, "image")->fileInput()->label('replace existed image') ?>
         TODO: output image thumbnail
        <br>
        <br>
    <?php else: ?>
        <?= $form->field($imagesGroup->imageEntity, "image")->fileInput()->label('load image') ?>
    <?php endif; ?>
<?php endif; ?>

<?= SimpleTabsTranslatesWidget::widget([
    'form' => $form,
    'translateModels' => $imagesGroup->translateForms,
    'data' => [$imagesGroup->imagesBlock, $imagesGroup],
])
?>
