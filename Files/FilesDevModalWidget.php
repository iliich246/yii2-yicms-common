<?php

namespace Iliich246\YicmsCommon\Files;

use Yii;
use yii\helpers\Url;
use yii\bootstrap\Widget;
use Iliich246\YicmsCommon\Fields\FieldTemplate;

/**
 * Class FilesDevModalWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FilesDevModalWidget extends Widget
{
    /** @var DevFilesGroup */
    public $devFilesGroup;
    /** @var bool true means that widget initialized after success data save in DevFilesGroup */
    public $dataSaved = false;
    /** @var string part of link for delete file block template */
    public $deleteLink;
    /** @var string keeps current form action */
    public $action;
    /** @var FieldTemplate[] array of translate able fields for current file block template */
    public $fieldTemplatesTranslatable;
    /** @var FieldTemplate[] array of single fields for current file block template */
    public $fieldTemplatesSingle;
    /** @var string if true widget must close modal window after save data */
    public $saveAndExit = 'false';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->deleteLink = Url::toRoute(['/common/dev-files/delete-file-block-template']);

        if (Yii::$app->request->post('_saveAndExit'))
            $this->saveAndExit = 'true';
    }

    /**
     * Returns name of form name of widget
     * @return string
     */
    public static function getFormName()
    {
        return 'create-update-files';
    }

    /**
     * Return name of modal window of widget
     * @return string
     */
    public static function getModalWindowName()
    {
        return 'filesDevModal';
    }

    /**
     * Returns name of pjax container for this widget
     * @return string
     */
    public static function getPjaxContainerId()
    {
        return 'files-pjax-container';
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('files_dev_modal_widget', [
            'widget' => $this
        ]);
    }
}
