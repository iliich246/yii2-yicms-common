<?php

/* @var $this \yii\web\View */
/* @var $widget \Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget */
/* @var $translateModel \Iliich246\YicmsCommon\Base\AbstractTranslate */

?>

<?= $this->render($widget->getTranslateView(), [
    'form' => $widget->form,
    'translateModel' => array_pop($widget->translateModels),
]) ?>


