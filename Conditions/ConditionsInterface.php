<?php

namespace Iliich246\YicmsCommon\Conditions;

/**
 * Interface ConditionsInterface
 *
 * This interface must implement any class, that must has conditions functionality.
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface ConditionsInterface
{
    /**
     * @return ConditionsHandler object, that aggregated in object with conditions functionality.
     */
    public function getConditionsHandler();

    /**
     * This method must proxy ConditionTemplate method for work with him directly from aggregator.
     * @param $name
     * @return ConditionTemplate
     */
    public function getCondition($name);
}
