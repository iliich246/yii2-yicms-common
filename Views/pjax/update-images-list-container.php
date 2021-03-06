<?php

use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Assets\ImagesDevAsset;
use Iliich246\YicmsCommon\Images\ImagesDevModalWidget;

/* @var $this \yii\web\View */
/* @var $imageTemplateReference string */
/* @var $imagesBlocks \Iliich246\YicmsCommon\Images\ImagesBlock[] */

$bundle = \Iliich246\YicmsCommon\Assets\DeveloperAsset::register($this);
$src = $bundle->baseUrl . '/loader.svg';

ImagesDevAsset::register($this);

?>

<div class="row content-block form-block">
    <div class="col-xs-12">
        <div class="content-block-title">
            <h3>List of image blocks</h3>
        </div>
        <div class="row control-buttons">
            <div class="col-xs-12">
                <button class="btn btn-primary add-image-block"
                        data-toggle="modal"
                        data-target="#imagesDevModal"
                        data-image-template-reference="<?= $imageTemplateReference ?>"
                        data-home-url="<?= \yii\helpers\Url::base() ?>"
                        data-pjax-container-name="<?= ImagesDevModalWidget::getPjaxContainerId() ?>"
                        data-images-modal-name="<?= ImagesDevModalWidget::getModalWindowName() ?>"
                        data-loader-image-src="<?= $src ?>"
                        data-current-selected-image-template="null">
                    <span class="glyphicon glyphicon-plus-sign"></span> Add image block
                </button>
            </div>
        </div>
        <?php if (isset($imagesBlocks)): ?>
            <?php Pjax::begin([
                'options' => [
                    'id' => 'update-images-list-container'
                ],
                'enablePushState'    => false,
                'enableReplaceState' => false
            ]) ?>
            <div class="list-block">
                <?php foreach ($imagesBlocks as $imageBlock): ?>
                    <div class="row list-items image-item">
                        <div class="col-xs-10 list-title">
                            <p data-image-template-id="<?= $imageBlock->id ?>">
                                <?= $imageBlock->program_name ?> (<?= $imageBlock->getTypeName() ?>)
                            </p>
                        </div>
                        <div class="col-xs-2 list-controls">
                            <?php if ($imageBlock->visible): ?>
                                <span class="glyphicon glyphicon-eye-open"></span>
                            <?php else: ?>
                                <span class="glyphicon glyphicon-eye-close"></span>
                            <?php endif; ?>
                            <?php if ($imageBlock->editable): ?>
                                <span class="glyphicon glyphicon-pencil"></span>
                            <?php endif; ?>
                            <?php if ($imageBlock->canUpOrder()): ?>
                                <span class="glyphicon image-arrow-up glyphicon-arrow-up"
                                      data-image-template-id="<?= $imageBlock->id ?>"></span>
                            <?php endif; ?>
                            <?php if ($imageBlock->canDownOrder()): ?>
                                <span class="glyphicon image-arrow-down glyphicon-arrow-down"
                                      data-image-template-id="<?= $imageBlock->id ?>"></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php Pjax::end() ?>
        <?php endif; ?>
    </div>
</div>
