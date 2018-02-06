<?php

use Iliich246\YicmsCommon\Images\ImagesGroup;
use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;

/** @var $this yii\web\View */
/** @var $imagesGroup \Iliich246\YicmsCommon\Images\ImagesGroup */
/** @var $imagesBlock \Iliich246\YicmsCommon\Images\ImagesBlock */

?>

<?php if ($imagesGroup->scenario == ImagesGroup::SCENARIO_CREATE): ?>
    <?= $form->field($imagesGroup->imageEntity, "image", [
        'inputOptions' => [
            'class' => 'image-loader-button',
        ],
    ])->fileInput()->label('load new image') ?>

    <div class="row images-thumbnails">
        <div class="col-md-8 preview-block">
            <img src="" alt="" style="max-width: 100%">
        </div>
    </div>

    <?= $form->field($imagesGroup->imageEntity, "cropInfo")->hiddenInput([
        'class' => 'hidden-crop-info'
    ])->label(false) ?>
<?php else: ?>
    <?php if ($imagesGroup->imageEntity->getPath()): ?>
        <?= $form->field($imagesGroup->imageEntity, "image",[
            'inputOptions' => [
                'class' => 'image-loader-button',
            ],
        ])->fileInput()->label('replace existed image') ?>

        <div class="row images-thumbnails">
            <div class="col-md-8 preview-block">
                <img src="<?= $imagesGroup->imageEntity->getPath() ?>" alt="" style="max-width: 100%">
            </div>
        </div>


        <?= $form->field($imagesGroup->imageEntity, "cropInfo")->hiddenInput([
            'class' => 'hidden-crop-info'
        ])->label(false) ?>
        <br>
        <br>
    <?php else: ?>
        <?= $form->field($imagesGroup->imageEntity, "image", [
            'inputOptions' => [
                'class' => 'image-loader-button',
            ],
        ])->fileInput()->label('load image') ?>

        <div class="row images-thumbnails">
            <div class="col-md-8 preview-block">
                <img src="" alt="" style="max-width: 100%">
            </div>
        </div>

        <?= $form->field($imagesGroup->imageEntity, "cropInfo")->hiddenInput([
            'class' => 'hidden-crop-info'
        ])->label(false) ?>
    <?php endif; ?>
<?php endif; ?>

<?= SimpleTabsTranslatesWidget::widget([
    'form' => $form,
    'translateModels' => $imagesGroup->translateForms,
    'data' => [$imagesGroup->imagesBlock, $imagesGroup],
])
?>
