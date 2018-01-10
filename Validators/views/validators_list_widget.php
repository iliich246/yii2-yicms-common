<?php

use yii\bootstrap\Html;
use Iliich246\YicmsCommon\Validators\AbstractValidatorForm;

/** @var $widget \Iliich246\YicmsCommon\Validators\ValidatorsListWidget */

$js = <<<JS
;(function() {
    setTimeout(function(){
        var currentModal = $('.modal').filter('.in');

        var validatorsBlock = $(currentModal).find('.validators-block');
        var addValidator = $(currentModal).find('.add-validator');
        var validatorsList = $(currentModal).find('.validators-list');

        var homeUrl = $(validatorsBlock).data('homeUrl');
        var returnUrl =  $(validatorsBlock).data('returnUrl');
        var pjaxContainerName = '#' + $(validatorsBlock).data('ownerPjaxContainerName');

        var updateValidatorUrl = homeUrl + '/common/dev-validators/update-validator?';
        var addValidatorUrl = homeUrl + '/common/dev-validators/add-validator?';

        $(addValidator).on('click', function() {
            $.pjax({
                url: addValidatorUrl + 'validatorReference=' + $(this).data('validatorReference') +
                    '&validator=' + $(validatorsList).find(":selected").text(),
                container: pjaxContainerName,
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500
            });
        });

        $('.validator-button').on('click', function() {

            $(pjaxContainerName).data('returnUrl', returnUrl);

            $.pjax({
                url: updateValidatorUrl + 'validatorId=' + $(this).data('validatorId') ,
                container: pjaxContainerName,
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500
            });
        });
    },250);
})();
JS;

$this->registerJs($js, $this::POS_READY);

?>
<div class="row">
    <div class="col-xs-12">
        <h3>Validators</h3>
        <?php if (AbstractValidatorForm::canAddNewValidator($widget->validatorReference->getValidatorReference())): ?>
            <div class="row">
                <div class="col-xs-3">
                    <button type="button"
                            class="btn btn-primary add-validator"
                            data-validator-reference="<?= $widget->validatorReference->getValidatorReference() ?>">
                        <span class="glyphicon glyphicon-plus-sign"></span> Add new validator
                    </button>
                </div>
                <div class="col-xs-9">
                    <div class="form-group has-success">
                        <?= Html::dropDownList(
                            'list-of-validators',
                            null,
                            AbstractValidatorForm::subtractListOfValidators(
                                $widget->validatorReference->getValidatorReference()
                            ),
                            [
                                'class' => 'form-control validators-list'
                            ]
                        );
                        ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-xs-12 validators-block"
                 data-return-url="<?= $widget->returnUrl ?>"
                 data-owner-pjax-container-name="<?= $widget->ownerPjaxContainerName ?>"
                 data-owner-modal-id="<?= $widget->ownerModalId ?>"
                 data-home-url="<?= \yii\helpers\Url::base() ?>"
                >
                <?php foreach (AbstractValidatorForm::getValidatorsDb($widget->validatorReference->getValidatorReference()) as $validatorDb): ?>
                    <button type="button"
                            class="btn validator-button
                            <?php if ($validatorDb->is_active): ?>btn-success <?php else: ?>btn-default<?php endif ?>"
                            data-validator-id="<?= $validatorDb->id ?>"
                        >
                        <?= AbstractValidatorForm::validatorNameByClass($validatorDb->validator) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
