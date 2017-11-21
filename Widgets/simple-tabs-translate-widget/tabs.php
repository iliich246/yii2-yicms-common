<?php

/* @var $this \yii\web\View */
/* @var $widget \Iliich246\YicmsCommon\Widgets\SimpleTabsTranslatesWidget */
/* @var $translateModels \Iliich246\YicmsCommon\Base\AbstractTranslate[] */
/* @var $translateModel \Iliich246\YicmsCommon\Base\AbstractTranslate */

?>

<div class="tabs-block">
    <ul class="nav nav-tabs" role="tablist">
        <?php foreach ($widget->translateModels as $translateModel): ?>
            <li role="presentation" <?php if ($translateModel->isActive()) { ?>class="active"<?php } ?>>
                <a href="#<?= $translateModel->getIdName() ?>"
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
                 id="<?= $translateModel->getIdName() ?>"
                >
                <?= $this->render($widget->getTranslateView(), [
                    'form' => $widget->form,
                    'translateModel' => $translateModel,
                ]) ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<pre>
    <?php print_r(count($widget->translateModels))?>
</pre>