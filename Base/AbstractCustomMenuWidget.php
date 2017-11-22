<?php

namespace Iliich246\YicmsCommon\Base;

use yii\bootstrap\Widget;

/**
 * Class AbstractCustomMenuWidget
 *
 * This class must inherit all custom widgets
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class AbstractCustomMenuWidget extends Widget
{
    /**
     * Returns name of widget
     * @return string
     */
    public static function getName()
    {
        return '';
    }
}
