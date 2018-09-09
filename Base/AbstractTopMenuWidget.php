<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;
use yii\base\Widget;

/**
 * Class AbstractTopMenuWidget
 *
 * This class builds admin menu for modules, that included in yicms
 *
 * For use custom menu widgets put name of view in order array
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractTopMenuWidget extends Widget
{
    /**
     * @var array order of render of menu elements of modules and custom blocks
     * This field is set in the classes inheritors
     *
     * [
     *     'common', // <--- module name
     *     'pages',  // <--- module name
     *     'custom_block1', // <--- custom widget
     *     'custom_block2', // <--- custom widget
     *     'gallery', // <--- module name
     * ]
     *
     * For custom widgets: 'custom_' - identificator that need to render custom widget
     * 'block1 and block2 - names of views in view directory corresponding to custom widget
     */
    public $order = [];
    /** @var array with widgets that must be rendered */
    public $widgets = [];

    /**
     * @inheritdoc
     */
    public function run()
    {
        //$this->findOrder(array_merge($this->findModuleWidgets(), $this->findCustomWidgets()));

        $this->findOrder($this->findModuleWidgets());

        return $this->render('main_' . strtolower(self::getType()) . '_menu', [
            'widget' => $this,
        ]);
    }

    /**
     * Renders all menu widgets for this top widget
     * @return string
     * @throws CommonException
     */
    public function renderMenuWidgets()
    {
        $result = '';
        //$customClassName = $this->getCustomClassName();

        foreach ($this->widgets as $key => $widgetClass) {

            // if ($widgetClass == $customClassName) {
            /** @var AbstractCustomMenuWidget $customObj */
            //    $customObj = new $widgetClass();
            //   $result .= $customObj->run($key);
            //} else {
            /** @var AbstractModuleMenuWidget $moduleWidget */
            $moduleWidget = new $widgetClass();
            $result .= $moduleWidget->run();
            // }
        }

        return $result;
    }

    /**
     * Make correct widgets order correspond order array
     * @param $modulesArray
     */
    private function findOrder($modulesArray)
    {
        foreach ($this->order as $item) {
            if (isset($modulesArray[$item])) {
                $this->widgets[$item] = $modulesArray[$item];
                unset($modulesArray[$item]);
            }

            if (strripos($item, 'custom_') !== false)
                $this->widgets[$item] = $this->getCustomClassName();
        }
    }

    /**
     * Search Module<Type>MenuWidget in all modules in directory widgets,
     * return array of class names with namespaces
     * @return array
     */
    private function findModuleWidgets()
    {
        $yicmsModules = [];

        foreach (Yii::$app->modules as $module) {
            if (!$module instanceof YicmsModuleInterface) continue;
            $yicmsModules[] = $module;
        }

        $resultClasses = [];

        /** @var AbstractConfigurableModule $yicmsModule */
        foreach ($yicmsModules as $yicmsModule) {
            $class = $yicmsModule->getNameSpace() . '\Widgets\Module' . self::getType() . 'MenuWidget';

            if (class_exists($class)) {
                /** @var $class AbstractModuleMenuWidget */
                $resultClasses[strtolower($yicmsModule::getModuleName())] = $class;
            }
        }

        return $resultClasses;
    }

    /**
     * Return custom menu widget class name
     * @return string
     * @throws CommonException
     */
    private function getCustomClassName()
    {
        static $customName = false;

        if ($customName) return $customName;

        $customName = 'app\modules\common\widgets\Custom' . $this->_type . 'MenuWidget';

        if (!class_exists($customName))
            throw new CommonException('Custom menu class not found for ' . $this->_type . ' menu');

        return $customName;
    }

    /**
     * Returns type of widget (Dev or Admin)
     * @return string
     */
    protected static function getType()
    {
        return static::getType();
    }

    /**
     * Returns namespace of descendant
     * @return mixed
     */
    protected static function getNameSpace()
    {
        return static::getNameSpace();
    }
}
