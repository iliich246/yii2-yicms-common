<?php

use Iliich246\YicmsCommon\Files\FilesBlock;
use Iliich246\YicmsCommon\Files\FilesGroup;

/** @var $this \yii\web\View */
/** @var $widget \Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget */
/** @var $translateModel \Iliich246\YicmsCommon\Files\FileTranslateForm  */
/** @var $fileBlock \Iliich246\YicmsCommon\Files\FilesBlock */
/** @var $filesGroup FilesGroup */

$fileBlock  = $widget->data[0];
$filesGroup = $widget->data[1];

?>
<?php if ($fileBlock->language_type == FilesBlock::LANGUAGE_TYPE_TRANSLATABLE): ?>
    <?php if ($filesGroup->scenario == FilesGroup::SCENARIO_CREATE): ?>
        <?= $form->field($translateModel, "[$translateModel->key]translatedFile")->fileInput()->label('load new file') ?>
    <?php else: ?>
        <?php if ($translateModel->getCurrentTranslateDb()->isPhysicalExisted()): ?>
            <?= $form->field($translateModel, "[$translateModel->key]translatedFile")->fileInput()->label('replace existed file') ?>
            <a href="<?= $translateModel->getFileEntity()->uploadUrl($translateModel->getLanguage()) ?>"
               data-pjax="0">
                Upload file "<?= $translateModel->getFileEntity()->getFileName(
                    $translateModel->getLanguage(), true
                ) ?>"
            </a>
        <?php else: ?>
            <?= $form->field($translateModel, "[$translateModel->key]translatedFile")->fileInput()->label('load file') ?>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<?= $form->field($translateModel, "[$translateModel->key]filename")->textInput() ?>
