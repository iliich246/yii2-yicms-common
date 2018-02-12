<?php

/* @var $this \yii\web\View */
/* @var $widget \Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget */
/* @var $translateModels \Iliich246\YicmsCommon\Base\AbstractTranslateForm[] */
/* @var $translateModel \Iliich246\YicmsCommon\Base\AbstractTranslateForm */

?>

<div class="tabs-block">
    <ul class="nav nav-tabs" role="tablist">
        <?php foreach ($widget->translateModels as $translateModel): ?>
            <li role="presentation" <?php if ($translateModel->isActive()) { ?>class="active"<?php } ?>>
                <a href="#<?= $translateModel->getIdName() ?><?= $widget->tabModification ?>"
                   data-toggle="tab"
                   aria-controls="<?= $translateModel->getIdName() ?>"
                >
                    <?= $translateModel->getLanguageName() ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="tab-content">
        <?php foreach ($widget->translateModels as $translateModel): ?>
            <div role="tabpanel"
                 class="tab-pane fade <?php if ($translateModel->isActive()) { ?>in active<?php } ?>"
                 id="<?= $translateModel->getIdName() ?><?= $widget->tabModification ?>"
                >
                <?= $this->render($widget->getTranslateView(), [
                    'form' => $widget->form,
                    'translateModel' => $translateModel,
                    'widget' => $widget
                ]) ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
