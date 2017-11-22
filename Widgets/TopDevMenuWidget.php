<?php

namespace Iliich246\YicmsCommon\Widgets;

use Iliich246\YicmsCommon\Base\AbstractTopMenuWidget;

/**
 * Class MainDevMenuWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class TopDevMenuWidget extends AbstractTopMenuWidget
{
    /**
     * @inheritdoc
     */
    public $order = [
        'common',
        'pages',

        //'custom_test1',

        //'auth',
        //'custom_test2',
        //'essences',
    ];

    /**
     * @inheritdoc
     */
    protected static function getNameSpace()
    {
        return __NAMESPACE__;
    }

    /**
     * @inheritdoc
     */
    public static function getType()
    {
        return 'Dev';
    }
}
