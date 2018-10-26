<?php

namespace Iliich246\YicmsCommon\Base;

use Iliich246\YicmsCommon\CommonModule;
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
    //Modes of widget
    const DEV_MODE   = 0;
    const ADMIN_MODE = 1;
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
        $this->findOrder($this->findModuleWidgets());



        return $this->render('top_' . strtolower($this->getType()) . '_menu', [
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

        foreach ($this->widgets as $key => $widgetClass) {
            if (!class_exists($widgetClass)) continue;

            /** @var Widget $moduleWidget */
            $moduleWidget = new $widgetClass();
            $result .= $moduleWidget->run();
        }

        return $result;
    }

    /**
     * Make correct widgets order correspond order array
     * @param $modulesArray
     * @throws CommonException
     */
    private function findOrder($modulesArray)
    {
        foreach ($this->order as $item) {
            if (isset($modulesArray[$item])) {
                $this->widgets[$item] = $modulesArray[$item];
                unset($modulesArray[$item]);
            }

            if (strripos($item, 'custom_') !== false)
                $this->widgets[$item] = $this->getCustomClassName($item);
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
            if ($this->getMode() == self::DEV_MODE) {
                $class = $yicmsModule->getNameSpace() . '\Widgets\ModuleDevMenuWidget';
            } else {
                $class = CommonModule::getInstance()->yicmsNamespace . '\\' .
                         $yicmsModule->getModuleName() . '\Widgets\ModuleMenuWidget';
            }

            if (class_exists($class)) {
                /** @var $class AbstractModuleMenuWidget */
                $resultClasses[strtolower($yicmsModule::getModuleName())] = $class;
            }
        }

        return $resultClasses;
    }

    /**
     * Return custom menu widget class name
     * @param $name
     * @return string
     * @throws CommonException
     */
    private function getCustomClassName($name)
    {
        if ($this->getMode() == self::DEV_MODE) {
            $class = CommonModule::getInstance()->getNameSpace() . '\Widgets\'';
        } else {
            $class = CommonModule::getInstance()->yicmsNamespace . '\\' .
                CommonModule::getInstance()->getModuleName() . '\Widgets\\';
        }

        $customName = preg_replace('/custom_+&?/','',$name);
        $customName = ucfirst($customName);

        $class .= $customName . 'MenuCustomWidget';

        $resultClasses = [];

        if (class_exists($class)) {
            return $class;
        }

        return false;


    }

    /**
     * Return string type of widget
     * @return string
     */
    private function getType()
    {
        if ($this->getMode() == self::DEV_MODE)
            return 'Dev';

        return 'Admin';
    }

    /**
     * Return mode of widget
     * @return integer
     */
    abstract protected function getMode();
}
