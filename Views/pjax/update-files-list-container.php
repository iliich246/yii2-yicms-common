<?php
use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Files\FilesDevModalWidget;

/* @var $this \yii\web\View */
/* @var $fileTemplateReference string */
/* @var $filesBlocks \Iliich246\YicmsCommon\Files\FilesBlock[] */

$bundle = \Iliich246\YicmsCommon\Assets\DeveloperAsset::register($this);
$src = $bundle->baseUrl . '/loader.svg';

?>

<div class="row content-block form-block">
    <div class="col-xs-12">
        <div class="content-block-title">
            <h3>List of file blocks</h3>
        </div>
        <div class="row control-buttons">
            <div class="col-xs-12">
                <button class="btn btn-primary add-file-block"
                        data-toggle="modal"
                        data-target="#filesDevModal"
                        data-file-template-reference="<?= $fileTemplateReference ?>"
                        data-home-url="<?= \yii\helpers\Url::base() ?>"
                        data-pjax-container-name="<?= FilesDevModalWidget::getPjaxContainerId() ?>"
                        data-files-modal-name="<?= FilesDevModalWidget::getModalWindowName() ?>"
                        data-loader-image-src="<?= $src ?>"
                        data-current-selected-file-template="null">
                <span class="glyphicon glyphicon-plus-sign"></span> Add new file block
                </button>
            </div>
        </div>
        <?php if (isset($filesBlocks)): ?>
            <?php Pjax::begin([
                'options' => [
                    'id' => 'update-files-list-container'
                ]
            ]) ?>
            <div class="list-block">
                <?php foreach ($filesBlocks as $filesBlock): ?>
                    <div class="row list-items file-item">
                        <div class="col-xs-10 list-title">
                            <p data-file-template-id="<?= $filesBlock->id ?>">
                                <?= $filesBlock->program_name ?> (<?= $filesBlock->getTypeName() ?>)
                            </p>
                        </div>
                        <div class="col-xs-2 list-controls">
                            <?php if ($filesBlock->visible): ?>
                                <span class="glyphicon glyphicon-eye-open"></span>
                            <?php else: ?>
                                <span class="glyphicon glyphicon-eye-close"></span>
                            <?php endif; ?>
                            <?php if ($filesBlock->editable): ?>
                                <span class="glyphicon glyphicon-pencil"></span>
                            <?php endif; ?>
                            <?php if ($filesBlock->canUpOrder()): ?>
                                <span class="glyphicon file-arrow-up glyphicon-arrow-up"
                                      data-file-template-id="<?= $filesBlock->id ?>"></span>
                            <?php endif; ?>
                            <?php if ($filesBlock->canDownOrder()): ?>
                                <span class="glyphicon file-arrow-down glyphicon-arrow-down"
                                      data-file-template-id="<?= $filesBlock->id ?>"></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php Pjax::end() ?>
        <?php endif; ?>
    </div>
</div>
