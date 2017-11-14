<?php

namespace Iliich246\YicmsCommon;

use Iliich246\YicmsCommon\Base\AbstractConfigurableModule;

/**
 * Class CommonModule
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class CommonModule extends AbstractConfigurableModule
{

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
    public function getModuleName()
    {
        return 'Common';
    }
}
