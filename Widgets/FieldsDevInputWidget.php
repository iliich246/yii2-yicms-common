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

    public function run()
    {
        return $this->render('fields_dev_input_widget', [
            'widget' => $this
        ]);
    }
}
