<?php

namespace Iliich246\YicmsCommon\Widgets;

use Yii;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AbstractModuleMenuWidget;

/**
 * Class ModuleDevMenuWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ModuleDevMenuWidget extends AbstractModuleMenuWidget
{
    /**
     * @inheritdoc
     */
    public static function getModuleName()
    {
        return strtolower(CommonModule::getModuleName());
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->route = Yii::$app->controller->action->getUniqueId();

        return $this->render('module_dev_menu', [
            'widget' => $this,
        ]);
    }
}
