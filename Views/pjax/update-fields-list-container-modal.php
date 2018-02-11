<?php

use yii\widgets\Pjax;
use Iliich246\YicmsCommon\Assets\FieldsDevAsset;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\FieldsDevModalWidget;

/* @var $this \yii\web\View */
/* @var $fieldTemplateReference integer */
/* @var $fieldTemplatesTranslatable FieldTemplate[] */
/* @var $fieldTemplatesSingle FieldTemplate[] */
/* @var $isInModal bool */

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title">
            Fields list
        </h3>
    </div>
    <div class="modal-body">
        <button class="btn btn-primary add-image-button">
            Add new field
        </button>
        <hr>
    </div>
</div>
