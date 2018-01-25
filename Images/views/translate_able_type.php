<?php

use Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget;

/** @var $this yii\web\View */
/** @var $imagesGroup \Iliich246\YicmsCommon\Images\ImagesGroup */
/** @var $imagesBlock \Iliich246\YicmsCommon\Images\ImagesBlock */

?>

<?= SimpleTabsTranslatesWidget::widget([
    'form' => $form,
    'translateModels' => $imagesGroup->translateForms,
    'data' => [$imagesGroup->imagesBlock, $imagesGroup],
])
?>
