<?php

/* @var $this \yii\web\View */
/* @var $widget Iliich246\YicmsCommon\Fields\FieldsRenderWidget */

?>

<div class="tabs-block">
    <ul class="nav nav-tabs" role="tablist">
        <?php foreach ($widget->fieldsArray as $translateModelList): ?>
            <li role="presentation" <?php if ($widget->isActive($translateModelList)) { ?>class="active"<?php } ?>>
                <a href="#<?= $widget->getIdName($translateModelList) ?>"
                   data-toggle="tab"
                   aria-controls="<?= $widget->getIdName($translateModelList) ?>"
                >
                    <?= $widget->getLanguageName($translateModelList) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="tab-content">
        <?php foreach ($widget->fieldsArray as $languageKey => $translateModelList): ?>
            <div role="tabpanel"
                 class="tab-pane fade <?php if ($widget->isActive($translateModelList)) { ?>in active<?php } ?>"
                 id="<?= $widget->getIdName($translateModelList) ?>"
            >
                <?php foreach($translateModelList as $field): ?>

                    <?= \Iliich246\YicmsCommon\Fields\FieldTypeWidget::widget([
                        'form' => $widget->form,
                        'fieldModel' => $field
                    ]) ?>

                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
