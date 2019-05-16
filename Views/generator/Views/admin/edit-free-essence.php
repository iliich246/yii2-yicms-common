<?php //template

use Iliich246\YicmsCommon\CommonModule;

/** @var $this yii\web\View */
/** @var $freeEssence \Iliich246\YicmsCommon\FreeEssences\FreeEssences */
/** @var $fieldsGroup \Iliich246\YicmsCommon\Fields\FieldsGroup */
/** @var $fieldTemplateReference string*/
/** @var $filesBlocks \Iliich246\YicmsCommon\Files\FilesBlock[] */
/** @var $imagesBlocks \Iliich246\YicmsCommon\Images\ImagesBlock[] */
/** @var $conditionsGroup \Iliich246\YicmsCommon\Conditions\ConditionsGroup */

$js = <<<JS
;(function() {

})();
JS;

//$this->registerJs($js);

?>

<div class="col-sm-9 content">
    <div class="row content-block content-header">
        <h1>Edit free essence</h1>
    </div>

    <div class="row content-block form-block">
        <div class="col-xs-12">
            <div class="content-block-title">
                <h3>Text fields</h3>
                <h4>Edit of text fields on free essence</h4>
            </div>

            <?= $this->render(CommonModule::getInstance()->yicmsLocation  . '/Common/Views/pjax/fields', [
                'fieldTemplateReference' => $freeEssence->getFieldTemplateReference(),
                'fieldsGroup'            => $fieldsGroup
            ]) ?>

        </div>
    </div>

    <?php if ($filesBlocks): ?>
        <div class="row content-block">
            <div class="col-xs-12">
                <h3>File blocks</h3>
                <h4>Edit of file blocks on the page</h4>

                <?= $this->render(CommonModule::getInstance()->yicmsLocation . '/Common/Views/files/files-blocks', [
                    'filesBlocks'   => $filesBlocks,
                    'fileReference' => $freeEssence->getFileReference(),
                ]) ?>

            </div>
        </div>

        <?= $this->render(CommonModule::getInstance()->yicmsLocation . '/Common/Views/files/files-modal') ?>

    <?php endif; ?>

    <?php if ($imagesBlocks): ?>
        <div class="row content-block">
            <div class="col-xs-12">
                <h3>Image blocks</h3>
                <h4>Edit of image blocks on the page</h4>

                <?= $this->render(CommonModule::getInstance()->yicmsLocation . '/Common/Views/images/images-blocks', [
                    'imagesBlocks'   => $imagesBlocks,
                    'imageReference' => $freeEssence->getImageReference(),
                ]) ?>

            </div>
        </div>

        <?= $this->render(CommonModule::getInstance()->yicmsLocation . '/Common/Views/images/images-modal') ?>

    <?php endif; ?>

    <?php if ($conditionsGroup->isConditions()): ?>
        <div class="row content-block">
            <div class="col-xs-12">
                <h3>Conditions blocks</h3>

                <?= $this->render(CommonModule::getInstance()->yicmsLocation . '/Common/Views/conditions/conditions', [
                    'conditionsGroup'            => $conditionsGroup,
                    'conditionTemplateReference' => $freeEssence->getConditionTemplateReference(),
                ]) ?>

            </div>
        </div>

    <?php endif; ?>
</div>
