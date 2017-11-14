<?php

namespace Iliich246\YicmsCommon\Tests\_testEssences\configurableModule\Base;

use Iliich246\YicmsCommon\Base\AbstractModuleConfiguratorDb;

/**
 * Class Test1ConfigDb
 *
 * This class is only for test AbstractModuleConfiguratorDb
 *
 * @property string $field1
 * @property string $field2
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Test2ConfigDb extends AbstractModuleConfiguratorDb
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%test_config_module2}}';
    }
}
