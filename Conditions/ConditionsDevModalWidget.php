<?php

namespace Iliich246\YicmsCommon\Conditions;

use yii\helpers\Url;
use yii\bootstrap\Widget;
use Iliich246\YicmsCommon\Fields\FieldTemplate;

/**
 * Class ConditionsDevModalWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionsDevModalWidget extends Widget
{
    /** @var DevConditionsGroup */
    public $devConditionsGroup;
    /** @var bool true means that widget initialized after success data save in DevConditionsGroup */
    public $dataSaved = false;
    /** @var string part of link for delete condition template */
    public $deleteLink;
    /** @var string keeps current form action */
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
        $this->deleteLink = Url::toRoute(['/common/dev-conditions/delete-conditions-block-template']);
    }

    /**
     * Returns name of form name of widget
     * @return string
     */
    public static function getFormName()
    {
        return 'create-update-conditions';
    }

    /**
     * Return name of modal window of widget
     * @return string
     */
    public static function getModalWindowName()
    {
        return 'conditionsDevModal';
    }

    /**
     * Returns name of pjax container for this widget
     * @return string
     */
    public static function getPjaxContainerId()
    {
        return 'conditions-pjax-container';
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('conditions_dev_modal_widget', [
            'widget' => $this
        ]);
    }
}
