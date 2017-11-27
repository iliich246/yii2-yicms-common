<?php

namespace Iliich246\YicmsCommon\Widgets;

use yii\bootstrap\Widget;
use Iliich246\YicmsCommon\Fields\DevFieldsGroup;

/**
 * Class FieldsDevInputWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldsDevInputWidget extends Widget
{
    /** @var DevFieldsGroup  */
    public $devFieldGroup;

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
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('fields_dev_input_widget', [
            'widget' => $this
        ]);
    }
}
