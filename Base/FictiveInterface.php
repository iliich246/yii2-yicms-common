<?php

namespace Iliich246\YicmsCommon\Base;

/**
 * Interface FictiveInterface
 *
 * This interface used for marking objects, what they existed in storage or not
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface FictiveInterface
{
    /**
     * Marks object that he is fictive
     * @return void
     */
    public function setFictive();

    /**
     * Marks object that he is not fictive
     * @return void
     */
    public function clearFictive();

    /**
     * Returns true if object is fictive
     * @return bool
     */
    public function isFictive();
}
