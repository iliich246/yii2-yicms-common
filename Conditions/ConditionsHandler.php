<?php

namespace Iliich246\YicmsCommon\Conditions;

use Iliich246\YicmsCommon\Base\AbstractHandler;

/**
 * Class ConditionsHandler
 *
 * Object of this class must aggregate any object, that must implement conditions functionality.
 *
 * @property ConditionsReferenceInterface $aggregator
 *
 * @package Iliich246\YicmsCommon\Conditions
 */
class ConditionsHandler extends AbstractHandler
{
    /**
     * ConditionsHandler constructor.
     * @param ConditionsReferenceInterface $aggregator
     */
    public function __construct(ConditionsReferenceInterface $aggregator)
    {
        $this->aggregator = $aggregator;
    }

    /**
     * Return instance of condition by name
     * @param $name
     * @return bool|object
     */
    public function getCondition($name)
    {
        return $this->getOrSet($name, function() use($name) {
            //Condition::get
//            return Field::getInstance(
//                $this->aggregator->getFieldTemplateReference(),
//                $this->aggregator->getFieldReference(),
//                $name
//            );
        });
    }
}