<?php

namespace Iliich246\YicmsCommon\Tests\_testEssences\configurableModule\base;

use Iliich246\YicmsCommon\Base\AbstractModuleConfiguratorDb;

/**
 * Class Test1ConfigDb
 *
 * This class is only for test AbstractModuleConfiguratorDb
 *
 * @property string $field1
 * @property string $field2
 * @property string $field3
 * @property string $field4
 * @property integer $field5
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Test1ConfigDb extends AbstractModuleConfiguratorDb
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%test_config_module1}}';
    }
}