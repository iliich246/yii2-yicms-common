<?php

namespace Iliich246\YicmsCommon\Conditions;

/**
 * Interface ConditionsReferenceInterface
 *
 * This interface must implement any class, that must has conditions functionality.
 * All that objects must have ability to give two variables for correct work with conditions functionality:
 *
 * Variable conditionTemplateReference used for pointing on condition templates.
 * Variable conditionReference user for pointing on single condition template.
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface ConditionsReferenceInterface
{
    /**
     * Returns conditionTemplateReference
     * @return string
     */
    public function getConditionTemplateReference();

    /**
     * Returns conditionReference
     * @return string
     */
    public function getConditionReference();
}
