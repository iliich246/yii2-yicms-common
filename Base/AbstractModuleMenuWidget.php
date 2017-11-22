<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;
use yii\bootstrap\Widget;

/**
 * Class AbstractModuleMenuWidget
 *
 * This class must inherit all classes, what determinate work with modules menus
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractModuleMenuWidget extends Widget
{
    /**
     * @var string current route
     */
    public $route;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->route = Yii::$app->controller->action->getUniqueId();
        parent::init();
    }
}
