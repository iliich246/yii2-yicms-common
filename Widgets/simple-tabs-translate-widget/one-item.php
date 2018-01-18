<?php

/* @var $this \yii\web\View */
/* @var $widget \Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget */

?>

<?= $this->render($widget->getTranslateView(), [
    'form' => $widget->form,
    'translateModel' => array_pop($widget->translateModels),
    'widget' => $widget
]) ?>
