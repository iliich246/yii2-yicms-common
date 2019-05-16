<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Files\FilesBlock;
use Iliich246\YicmsCommon\Files\FilesGroup;
use Iliich246\YicmsCommon\Conditions\ConditionsGroup;

/** @var $this yii\web\View */
/** @var $fileBlock \Iliich246\YicmsCommon\Files\FilesBlock */
/** @var $fieldsGroup \Iliich246\YicmsCommon\Fields\FieldsGroup */
/** @var $filesGroup \Iliich246\YicmsCommon\Files\FilesGroup */
/** @var $conditionsGroup \Iliich246\YicmsCommon\Conditions\ConditionsGroup */

$js = <<<JS
;(function() {
    var fileLoadForm = $('#file-load-form');
    var fileLoadBack = $('.file-load-back');

    var homeUrl = $(fileLoadForm).data('homeUrl');

    var pjaxContainer   = $(fileLoadForm).closest('.pjax-container');
    var pjaxContainerId = '#' + $(pjaxContainer).attr('id');

    var fileBlockId = $(fileLoadForm).data('fileBlockId');

    var isReturn  = $(fileLoadForm).data('returnBack');
    var returnUrl = $(pjaxContainer).data('returnUrl');

    var updateRedirect = $(fileLoadForm).data('updateRedirect');
    var fileId         = $(fileLoadForm).data('fileId');

    if (isReturn) return goBack();

    if (updateRedirect) return redirectToUpdateFile();

    if (returnUrl) {
        $(fileLoadBack).css('display', 'block');
    }

    function goBack() {
        var filesPjaxContainer = $('#files-pjax-container');

        $(filesPjaxContainer).data('needToGoBack', 1);
        $(filesPjaxContainer).data('backUrlData', returnUrl);
    }

    function redirectToUpdateFile() {
        var filesPjaxContainer = $('#files-pjax-container');

        $(filesPjaxContainer).data('needRedirectFromCreateToUpdate', 1);

        $(filesPjaxContainer).data('needToUpdateFileId', fileId);
    }

    $('.file-save-button').click(function() {
        $(pjaxContainer).data('needToRefreshFilesList', 1);
    });

    $(fileLoadBack).click(function() {
        $.pjax({
            url: returnUrl,
            container: pjaxContainerId,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500,
        });
    });

    $('#file-delete').on('click', function() {
        var button = ('#file-delete');

        if (!$(button).is('[data-file-id]')) return;

        if (!($(this).hasClass('file-confirm-state'))) {
            $(this).before('<span>Are you sure? This action will delete file</span>');
            $(this).text('Yes, I`am sure!');
            $(this).addClass('file-confirm-state');
        } else {
            $.pjax({
                url: $(this).data('deleteUrl'),
                container: '#files-pjax-container',
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500
            });
        }
    });
})();
JS;

$this->registerJs($js);

if (isset($returnBack)) $return = 'true';
else $return = 'false';

if (isset($updateRedirect)) $toUpdate = 'true';
else $toUpdate = 'false';

if (isset($fileIdForRedirect)) $fileId = $fileIdForRedirect;
else $fileId = '0';

?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <?php if ($filesGroup->scenario == FilesGroup::SCENARIO_CREATE): ?>
        <h3>Create new file
            <?php else: ?>
            <h3>Update file
                <?php endif; ?>

                <span class="glyphicon glyphicon-arrow-left file-load-back" aria-hidden="true"
                      style="float: right;margin-right: 20px;display: none"></span>

            </h3>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'file-load-form',
        'options' => [
            'class' => 'file-load-form',
            'data-pjax' => true,
            'data-home-url' => \yii\helpers\Url::base(),
            'data-file-block-id' => $fileBlock->id,
            'data-return-back' => $return,
            'data-update-redirect' => $toUpdate,
            'data-file-id' => $fileId
        ],
    ]);
    ?>
    <div class="modal-body">

        <?= $filesGroup->render($form) ?>

        <div class="row">
            <div class="col-sm-4 col-xs-12 ">
                <?= $form->field($filesGroup->fileEntity, 'visible')->checkbox() ?>
            </div>
            <?php if (CommonModule::isUnderDev()): ?>
                <div class="col-sm-4 col-xs-12 ">
                    <?= $form->field($filesGroup->fileEntity, 'editable')->checkbox() ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($filesGroup->scenario == FilesGroup::SCENARIO_UPDATE && $filesGroup->fileBlock->hasFields()): ?>

            <br>

            <hr>

            <h3>Fields of the file:</h3>

            <?= $this->render(CommonModule::getInstance()->yicmsLocation . '/Common/Views/pjax/fields-modal', [
                'fieldTemplateReference' => $fileBlock->getFieldTemplateReference(),
                'fieldsGroup' => $fieldsGroup,
                'refreshUrl' => \yii\helpers\Url::toRoute(
                    [
                        '/common/admin-files/update-file',
                        'fileId' => $filesGroup->fileEntity->id,
                    ]),
                'form' => $form
            ]) ?>

        <?php endif; ?>

        <?php if (isset($conditionsGroup) && $conditionsGroup->conditionTemplates): ?>


            <br>

            <hr>

            <h3>Conditions of the file:</h3>

            <?= $this->render(CommonModule::getInstance()->yicmsLocation . '/Common/Views/conditions/conditions-modal', [
            'conditionsTemplateReference' => $fileBlock->getConditionTemplateReference(),
            'conditionsGroup' => $conditionsGroup,
            'refreshUrl' => \yii\helpers\Url::toRoute(
                [
                    '/common/admin-files/update-file',
                    'fileId' => $filesGroup->fileEntity->id,
                ]),
            'form' => $form
        ]) ?>

        <?php endif; ?>

        <?php if ($fileBlock->type != FilesBlock::TYPE_ONE_FILE): ?>
            <div class="row delete-button-row-files">
                <div class="col-xs-12">
                    <button type="button"
                            class="btn btn-danger"
                            id="file-delete"
                            data-delete-url="<?= \yii\helpers\Url::toRoute([
                                '/common/admin-files/delete-file',
                                'fileId' => $filesGroup->fileEntity->id
                            ]) ?>"
                            data-file-id="<?= $filesGroup->fileEntity->id ?>"
                        >
                        Delete file
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="modal-footer">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success file-save-button']) ?>

        <?php if ($fileBlock->type != FilesBlock::TYPE_ONE_FILE): ?>
            <?= Html::submitButton('Save and back',
                ['class' => 'btn btn-success file-save-button',
                    'value' => 'true', 'name' => '_saveAndBack']) ?>
        <?php endif; ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
    <?php ActiveForm::end(); ?>
</div>
