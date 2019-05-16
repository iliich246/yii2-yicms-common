<?php

namespace app\yicms\Common\Widgets;

use Iliich246\YicmsCommon\Base\AbstractTopMenuWidget;

/**
 * Class TopAdminMenuWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class TopAdminMenuWidget extends AbstractTopMenuWidget
{
    /**
     * @inheritdoc
     */
    public $order = [
        'common',
        'pages',
        //'custom_test2',
        'essences',
        'feedback',
        //'custom_test',

    ];

    /**
     * @inheritdoc
     */
    public function getMode()
    {
        return self::ADMIN_MODE;
    }
}
