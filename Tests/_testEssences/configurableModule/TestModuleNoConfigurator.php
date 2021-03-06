<?php

namespace Iliich246\YicmsCommon\Tests\_testEssences\configurableModule;

use Iliich246\YicmsCommon\Base\AbstractConfigurableModule;

/**
 * Class TestModule2
 *
 * This class is only for test AbstractConfigurableModule
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class TestModuleNoConfigurator extends AbstractConfigurableModule
{
    public $field1 = 'not_configured';

    public $field2 = 'not_configured';

    public $field3 = 'not_configured';

    /**
     * @inherited
     */
    public $configurable = [
        'field1',
        'field2',
        'field3'
    ];

    /**
     * @inherited
     */
    public function getNameSpace()
    {
        return __NAMESPACE__;
    }

    /**
     * @inherited
     */
    public static function getModuleName()
    {
        return 'No_configurator';
    }
}
