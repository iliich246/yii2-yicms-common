<?php

namespace Iliich246\YicmsCommon\Fields;

use yii\helpers\Url;
use yii\bootstrap\Widget;
use Iliich246\YicmsCommon\Fields\DevFieldsGroup;

/**
 * Class FieldsDevInputWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldsDevModalWidget extends Widget
{
    /** @var DevFieldsGroup  */
    public $devFieldGroup;
    /** @var bool true means that widget initialized after success data save in devFieldGroup */
    public $dataSaved = false;
    /** @var string part of link for delete field template  */
    public $deleteLink;
    /** @var string keeps current form action  */
    public $action;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->deleteLink = Url::toRoute(['/common/dev-fields/delete-field-template']);
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
