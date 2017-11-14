<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;

/**
 * Class DataBaseConfigurator
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DataBaseConfigurator implements ConfiguratorInterface
{
    /** @var AbstractConfigurableModule instance of module that must be configured  */
    private $moduleInstance;
    /** @var null|AbstractModuleConfiguratorDb instance of configurator db  */
    private $configuratorDb;

    /**
     * Constructor
     * @param AbstractConfigurableModule $module
     * @throws CommonException
     */
    public function __construct(AbstractConfigurableModule $module)
    {
        $this->moduleInstance = $module;
        $this->findDbConfigurator();
    }

    /**
     * @inheritdoc
     */
    public function configure()
    {
        if (!$this->configuratorDb) return;

        $attributes = $this->configuratorDb->attributes;
        unset($attributes['id']);

        $id = $this->moduleInstance->id;

        foreach($this->moduleInstance->configurable as $configItem) {
            if (array_key_exists($configItem, $attributes)) {
                $this->moduleInstance->$configItem = $attributes[$configItem];
                unset($attributes[$configItem]);
            } else
                Yii::warning("In configurable array of '$id' module existed field '$configItem',
                    that provider not give, it will not be configured", __METHOD__);
        }
    }

    /**
     * Tries to find database configurator according with module that is invoked
     * @return void
     * @throws CommonException
     */
    private function findDbConfigurator()
    {
        /** @var AbstractModuleConfiguratorDb $className */
        $className = $this->moduleInstance->getNameSpace() . '\base\\' .
                     $this->moduleInstance->getModuleName() . 'ConfigDb';

        if (!class_exists($className)) {
            Yii::warning("Can`t find $className class", __METHOD__);

            if (defined('YICMS_STRICT')) throw new CommonException("Can`t find $className class");
            return;
        }

//        if (!($className instanceof AbstractModuleConfiguratorDb)) {
//
//            throw new \yii\base\Exception(print_r($className, true));
//            Yii::warning("$className class is not instance of AbstractModuleConfiguratorDb", __METHOD__);
//
//            if (YICMS_STRICT)
//                throw new CommonException("$className class is not instance of AbstractModuleConfiguratorDb");
//            return;
//        }

        $this->configuratorDb = $className::getInstance();

        if (!$this->configuratorDb) {

            Yii::warning('Record in table ' . $className::tableName() . 'for configure is not existed', __METHOD__);

            if (defined('YICMS_STRICT'))
                throw new CommonException('Record in table ' . $className::tableName() . 'for configure is not existed');
            return;
        }
    }
}
