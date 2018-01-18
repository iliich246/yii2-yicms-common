<?php

use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;

/** @var $this yii\web\View */
/** @var $filesGroup \Iliich246\YicmsCommon\Files\FilesGroup */

?>

<?= SimpleTabsTranslatesWidget::widget([
    'form' => $form,
    'translateModels' => $filesGroup->translateForms,
    'data' => $filesGroup->fileBlock,
])
?>

