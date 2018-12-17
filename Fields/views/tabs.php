<?php

/* @var $this \yii\web\View */
/* @var $widget Iliich246\YicmsCommon\Fields\FieldsRenderWidget */

$tabSelector = uniqid();
?>

<div class="tabs-block">
    <ul class="nav nav-tabs" role="tablist">
        <?php foreach ($widget->fieldsArray as $translateModelList): ?>
            <li role="presentation" <?php if ($widget->isActive($translateModelList)) { ?>class="active"<?php } ?>>
                <a href="#<?= $widget->getIdName($translateModelList) ?>_<?= $tabSelector ?>"
                   data-toggle="tab"
                   aria-controls="<?= $widget->getIdName($translateModelList) ?>_<?= $widget->id ?>"
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
                 id="<?= $widget->getIdName($translateModelList) ?>_<?= $tabSelector ?>"
            >
                <?php foreach($translateModelList as $field): ?>

                    <?= \Iliich246\YicmsCommon\Fields\FieldTypeWidget::widget([
                        'form'       => $widget->form,
                        'fieldModel' => $field,
                        'isModal'    => $widget->isModal
                    ]) ?>

                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
