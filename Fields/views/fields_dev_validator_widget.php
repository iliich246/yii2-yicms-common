<?php

use yii\bootstrap\Html;

/** @var $widget \Iliich246\YicmsCommon\Fields\FieldsDevValidatorWidget */

$js = <<<JS
;(function() {
    var addField = $('.add-field');

    var homeUrl = $(addField).data('homeUrl');
    var fieldTemplateReference = $(addField).data('fieldTemplateReference');
    var pjaxContainerName = '#' + $(addField).data('pjaxContainerName');
    var pjaxFieldsModalName = '#' + $(addField).data('fieldsModalName');
    var imageLoaderScr = $(addField).data('loaderImageSrc');

    var testUrl = homeUrl + '/common/dev-validators/test';

    $(document).on('click', '.validator-button', function() {
        $.pjax({
            url: testUrl,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });
})();
JS;

$this->registerJs($js, $this::POS_READY);

?>
<div class="row">
    <div class="col-xs-12">
        <h3>Validators</h3>

        <div class="row">
            <div class="col-xs-3">
                <button type="button" class="btn btn-primary add-validator">
                    <span class="glyphicon glyphicon-plus-sign"></span> Add new validator
                </button>
            </div>
            <div class="col-xs-9">
                <div class="form-group field-fieldtemplate-type has-success">
                    <?= Html::dropDownList(
                        'list-of-validators',
                        null,
                        \Iliich246\YicmsCommon\Validators\AbstractValidatorForm::listOfWidgets(),
                        [
                            'class' => 'form-control'

                        ]
                    );
                    ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <button type="button"
                        class="btn btn-success validator-button"
                        data-validator-type="required"
                        >
                    Required
                </button>
                <button type="button"
                        class="btn btn-default validator-button"
                        data-validator-type="string"
                       >
                    String
                </button>
            </div>
        </div>
    </div>
</div>
