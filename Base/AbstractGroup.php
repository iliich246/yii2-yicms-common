<?php

namespace Iliich246\YicmsCommon\Base;

use yii\base\Component;
use yii\widgets\ActiveForm;

/**
 * Class AbstractGroup
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractGroup extends Component
{
    const SCENARIO_DEFAULT = 0x00;
    const SCENARIO_CREATE = 0x01;
    const SCENARIO_UPDATE = 0x02;

    public $scenario = self::SCENARIO_DEFAULT;

    /**
     * Initializes all group for correct work
     * @return void
     */
    abstract public function initialize();

    /**
     * Load data to group
     * @see Model::load($data)
     * @param $data
     * @return bool
     */
    abstract public function load($data);

    /**
     * Validates group
     * @see Model::validate()
     * @return bool
     */
    abstract public function validate();

    /**
     * Render group
     * @param ActiveForm $form
     * @return string
     */
    abstract public function render(ActiveForm $form);

    /**
     * Saves group
     * @return bool
     */
    abstract public function save();
}
