<?php //template

use Iliich246\YicmsCommon\Images\ImagesBlock;
use Iliich246\YicmsCommon\Images\ImagesGroup;

/** @var $this \yii\web\View */
/** @var $widget \Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget */
/** @var $translateModel \Iliich246\YicmsCommon\Images\ImageTranslateForm */
/** @var $imagesBlock ImagesBlock */
/** @var $imagesGroup ImagesGroup */

$imagesBlock = $widget->data[0];
$imagesGroup = $widget->data[1];

?>

<?php if ($imagesBlock->language_type == ImagesBlock::LANGUAGE_TYPE_TRANSLATABLE): ?>
    <?php if ($imagesGroup->scenario == ImagesGroup::SCENARIO_CREATE): ?>
        <?= $form->field($translateModel, "[$translateModel->key]translatedImage",[
            'inputOptions' => [
                'class' => 'image-loader-button',
            ],
        ])->fileInput()->label('load new image') ?>

        <div class="row images-thumbnails">
            <div class="col-md-8 preview-block">
                <img src="<?= $translateModel->getImageEntity()->getSrc() ?>" alt="">
            </div>
        </div>

        <?= $form->field($translateModel, "[$translateModel->key]cropInfo")->hiddenInput([
            'class' => 'hidden-crop-info'
        ])->label(false) ?>

    <?php else: ?>
        <?php if ($translateModel->getCurrentTranslateDb()->isPhysicalExisted()): ?>
            <?= $form->field($translateModel, "[$translateModel->key]translatedImage", [
                'inputOptions' => [
                    'class' => 'image-loader-button',
                ],
            ])->fileInput()->label('replace existed image') ?>

            <div class="row images-thumbnails">
                <div class="col-md-8 preview-block">
                    <img src="<?=$translateModel->getSrc() ?>" alt="" style="width: 100%">
                </div>
            </div>

            <?= $form->field($translateModel, "[$translateModel->key]cropInfo")->hiddenInput([
                'class' => 'hidden-crop-info'
            ])->label(false) ?>

        <?php else: ?>
            <?= $form->field($translateModel, "[$translateModel->key]translatedImage", [
                'inputOptions' => [
                    'class' => 'image-loader-button',
                ],
            ])->fileInput()->label('load image') ?>

            <div class="row images-thumbnails">
                <div class="col-md-8 preview-block">
                    <img src="" alt="">
                </div>
            </div>

            <?= $form->field($translateModel, "[$translateModel->key]cropInfo")->hiddenInput([
                'class' => 'hidden-crop-info'
            ])->label(false) ?>

        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
