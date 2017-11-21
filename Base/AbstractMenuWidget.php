<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;
use yii\base\Widget;

/**
 * Class AbstractMenuWidget
 *
 * This class builds admin menu for modules, that included in yicms
 *
 * For use custom menu widgets put name of view in order array
 * @package Iliich246\YicmsCommon\Base
 */
class AbstractMenuWidget extends Widget
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

    /** @var array with widgets that must be rendered*/
    public $widgets = [];

    /**
     * @inheritdoc
     */
    public function run()
    {
        //$this->findOrder($this->findModuleWidgets());

        return $this->render('main' . $this->_type . 'Menu',[
            'widget' => $this,
        ]);
    }

    /**
     * Make correct widgets order correspond order array
     * @param $modulesArray
     */
    private function findOrder( $modulesArray )
    {
        foreach($this->order as $item) {
            if (isset($modulesArray[$item])) {
                $this->_widgets[$item] = $modulesArray[$item];
                unset($modulesArray[$item]);
            }

            if (strripos($item, 'custom_') !== false)
                $this->_widgets[$item] = $this->getCustomClassName();
        }

        //this string will render all widgets, even not existed in $this->order - it`s semantic wrong
        //if (count($modulesArray))
        //$this->_widgets = array_merge($this->_widgets, $modulesArray);
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
            throw new CommonException('Custom menu class not found for '. $this->_type . ' menu');

        return $customName;
    }


}