<?php

use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Assets\ConditionsDevAsset;
use Iliich246\YicmsCommon\Conditions\ConditionsDevModalWidget;

/** @var $this \yii\web\View */
/** @var $conditionTemplateReference string */
/** @var $conditionsTemplates \Iliich246\YicmsCommon\Conditions\ConditionTemplate[] */

$bundle = \Iliich246\YicmsCommon\Assets\DeveloperAsset::register($this);
$src = $bundle->baseUrl . '/loader.svg';

ConditionsDevAsset::register($this);

?>

<div class="row content-block form-block">
    <div class="col-xs-12">
        <div class="content-block-title">
            <h3>List of conditions</h3>
        </div>
        <div class="row control-buttons">
            <div class="col-xs-12">
                <button class="btn btn-primary add-condition-template"
                        data-toggle="modal"
                        data-target="#conditionsDevModal"
                        data-condition-template-reference="<?= $conditionTemplateReference ?>"
                        data-home-url="<?= \yii\helpers\Url::base() ?>"
                        data-pjax-container-name="<?= ConditionsDevModalWidget::getPjaxContainerId() ?>"
                        data-condition-modal-name="<?= ConditionsDevModalWidget::getModalWindowName() ?>"
                        data-loader-image-src="<?= $src ?>"
                        data-current-selected-condition-template="null">
                    <span class="glyphicon glyphicon-plus-sign"></span> Add new condition
                </button>
            </div>
        </div>

        <?php if (isset($conditionsTemplates)): ?>
            <?php Pjax::begin([
                'options' => [
                    'id' => 'update-conditions-list-container'
                ]
            ]) ?>
            <div class="list-block">
                <?php foreach ($conditionsTemplates as $conditionsTemplate): ?>
                    <div class="row list-items condition-item">
                        <div class="col-xs-10 list-title">
                            <p data-condition-template-id="<?= $conditionsTemplate->id ?>">
                                <?= $conditionsTemplate->program_name ?> (<?= $conditionsTemplate->getTypeName()  ?>)
                            </p>
                        </div>
                        <div class="col-xs-2 list-controls">
                            <?php if ($conditionsTemplate->editable): ?>
                                <span class="glyphicon glyphicon-pencil"></span>
                            <?php endif; ?>
                            <?php if ($conditionsTemplate->canUpOrder()): ?>
                                <span class="glyphicon condition-arrow-up glyphicon-arrow-up"
                                      data-condition-template-id="<?= $conditionsTemplate->id ?>"></span>
                            <?php endif; ?>
                            <?php if ($conditionsTemplate->canDownOrder()): ?>
                                <span class="glyphicon condition-arrow-down glyphicon-arrow-down"
                                      data-condition-template-id="<?= $conditionsTemplate->id ?>"></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php Pjax::end() ?>
        <?php endif; ?>
    </div>
</div>
