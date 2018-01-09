<?php

use yii\helpers\Url;
use Iliich246\YicmsCommon\Assets\FieldsDevAsset;
use Iliich246\YicmsCommon\Fields\FieldsDevModalWidget;
use Iliich246\YicmsCommon\Fields\FieldTemplate;

/* @var $this \yii\web\View */
/* @var $filesBlock \Iliich246\YicmsCommon\Files\FilesBlock */
/* @var $fieldTemplatesTranslatable FieldTemplate[] */
/* @var $fieldTemplatesSingle FieldTemplate[] */
/* @var $devFieldGroup \Iliich246\YicmsCommon\Fields\DevFieldsGroup */

FieldsDevAsset::register($this);

?>


<div class="col-sm-9 content">
    <div class="row content-block content-header">
        <h1>List of fields for file template "<?= $filesBlock->program_name ?>"</h1>
    </div>

    <div class="row content-block breadcrumbs">
        <a href="<?= Url::previous('dev') ?>" style="float: right"><span>Go back</span></a>
    </div>

    <?= $this->render('/pjax/update-fields-list-container', [
        'fieldTemplateReference' => $filesBlock->getFieldTemplateReference(),
        'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
        'fieldTemplatesSingle' => $fieldTemplatesSingle
    ]) ?>
</div>

<?= FieldsDevModalWidget::widget([
    'devFieldGroup' => $devFieldGroup,
    'action' => Url::toRoute(['/common/dev/update-free-essence', 'id' => $filesBlock->id])
])
?>
