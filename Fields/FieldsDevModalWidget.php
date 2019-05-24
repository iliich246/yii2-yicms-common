<?php

namespace Iliich246\YicmsCommon\Fields;

use Yii;
use yii\helpers\Url;
use yii\bootstrap\Widget;

/**
 * Class FieldsDevInputWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldsDevModalWidget extends Widget
{
    /** @var DevFieldsGroup instance  */
    public $devFieldGroup;
    /** @var bool true means that widget initialized after success data save in devFieldGroup */
    public $dataSaved = false;
    /** @var string part of link for delete field template  */
    public $deleteLink;
    /** @var string keeps current form action  */
    public $action;
    /** @var string if true widget must close modal window after save data */
    public $saveAndExit = 'false';
    /** @var string keeps path for annotating */
    public $annotatePath;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->deleteLink = Url::toRoute(['/common/dev-fields/delete-field-template']);

        if (Yii::$app->request->post('_saveAndExit'))
            $this->saveAndExit = 'true';
    }

    /**
     * Returns name of form name of widget
     * @return string
     */
    public static function getFormName()
    {
        return 'create-update-fields';
    }

    /**
     * Return name of modal window of widget
     * @return string
     */
    public static function getModalWindowName()
    {
        return 'fieldsDevModal';
    }

    /**
     * Returns name of pjax container for this widget
     * @return string
     */
    public static function getPjaxContainerId()
    {
        return 'fields-pjax-container';
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('fields_dev_modal_widget', [
            'widget' => $this
        ]);
    }
}
