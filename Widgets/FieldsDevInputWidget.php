<?php

namespace Iliich246\YicmsCommon\Widgets;

use yii\bootstrap\Widget;

/**
 * Class FieldsDevInputWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldsDevInputWidget extends Widget
{
    public function run()
    {
        return $this->render('fields_dev_input_widget');
    }
}
