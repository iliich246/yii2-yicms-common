<?php

namespace Iliich246\YicmsCommon\Base;

/**
 * Class CommonConfigDb
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class CommonConfigDb extends AbstractModuleConfiguratorDb
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_config}}';
    }
}
