<?php

namespace Iliich246\YicmsCommon\Base;

use yii\base\Component;

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

    /** @var */
    protected $referenceAble;

    abstract public function initialize();

    abstract public function load($data);

    abstract public function validate();

    abstract public function render();
}
