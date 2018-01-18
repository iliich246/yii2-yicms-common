<?php

use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;

/** @var $this yii\web\View */
/** @var $filesGroup \Iliich246\YicmsCommon\Files\FilesGroup */

?>

<?= $form->field($filesGroup->fileEntity, "file")->fileInput() ?>

<?= SimpleTabsTranslatesWidget::widget([
    'form' => $form,
    'translateModels' => $filesGroup->translateForms,
    'data' => $filesGroup->fileBlock,
])
?>


