<?php

namespace Iliich246\YicmsCommon\Base;

use yii\db\ActiveRecord;

/**
 * Class AbstractModuleConfiguratorDb
 *
 * Base class for data base configurators for modules. All database configurators must inherit this class.
 *
 * @property integer $id
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractModuleConfiguratorDb extends ActiveRecord
{
    const INSTANCE_ID = 1;

    /**
     * Return instance of database configurator
     * @return static
     */
    public static function getInstance()
    {
        return static::findOne(self::INSTANCE_ID);
    }
}
