<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;
use yii\Base\Module;

/**
 * Class ConfigurableModule
 *
 * This abstract class provides ability of concrete children's modules to be configured by data provider
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractConfigurableModule extends Module
{
    /**
     * @var array contains properties of module, that must be configured from data provider.
     * for example
     * $configurable = [
     *    'field1',
     *    'field2 ,
     * ]
     * where field1 and field2 is a public properties of module
     *
     * Only specified properties of module will be configured, even if he has much more properties.
     * If data provider has no concrete property, this property will be configured by default
     */
    public $configurable = [];

    /**
     * @var ConfiguratorInterface instance of configurator;
     */
    protected $configurator;

    private $instance;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->checkConfigurableArray();
        $this->configurator =  new DataBaseConfigurator($this);
        $this->configurator->configure();
    }

    /**
     * Method checks configurable array and deletes non existent element from him
     * @return array
     */
    private function checkConfigurableArray()
    {
        foreach($this->configurable as $key => $item) {
            if (!property_exists($this, $item)) {
                Yii::warning("Try to config nonexistent property '$item' in module '$this->id'", __METHOD__);
                unset($this->configurable[$key]);
            }
        }
    }

    /**
     * Return namespace of module
     * @return string
     */
    abstract public function getNameSpace();

    /**
     * Return name of module
     * @return string
     */
    abstract public function getModuleName();
}
