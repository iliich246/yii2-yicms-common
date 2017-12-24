<?php

use yii\bootstrap\Html;
use Iliich246\YicmsCommon\Validators\AbstractValidatorForm;
/** @var $widget \Iliich246\YicmsCommon\Validators\ValidatorsListWidget */

$js = <<<JS
;(function() {
    var addField = $('.add-field');
    var addValidator = $('.add-validator');

    var homeUrl = $(addField).data('homeUrl');
    var fieldTemplateReference = $(addField).data('fieldTemplateReference');
    var pjaxContainerName = '#' + $(addField).data('pjaxContainerName');
    var pjaxFieldsModalName = '#' + $(addField).data('fieldsModalName');
    var imageLoaderScr = $(addField).data('loaderImageSrc');

    var updateValidatorUrl = homeUrl + '/common/dev-validators/update-validator?';



    //$(document).on('click', '.validator-button', function() {
    //    $.pjax({
    //        url: testUrl,
    //        container: pjaxContainerName,
    //        scrollTo: false,
    //        push: false,
    //        type: "POST",
    //        timeout: 2500
    //    });
    //});

    $(addValidator).on('click', function() {
        //alert(1);
    });

    $('.validator-button').on('click', function() {

        //console.log($(this).data('validatorId'));
        $.pjax({
            url: updateValidatorUrl + 'validatorId=' + $(this).data('validatorId') ,
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
                        AbstractValidatorForm::subtractListOfValidators(
                            $widget->validatorReference->getValidatorReference()
                        ),
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
                <?php foreach(AbstractValidatorForm::getValidatorsDb($widget->validatorReference->getValidatorReference()) as $validatorDb): ?>
                    <button type="button"
                            class="btn btn-success validator-button
                            <?php if ($validatorDb->is_active): ?>btn-success <?php else: ?>btn-default<?php endif ?>"
                            data-validator-id="<?= $validatorDb->id ?>"
                    >
                        <?= $validatorDb->validator ?>
                    </button>
                <?php endforeach; ?>


            </div>
        </div>
    </div>
</div>
