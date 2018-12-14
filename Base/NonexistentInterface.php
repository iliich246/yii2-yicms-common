<?php

namespace Iliich246\YicmsCommon\Base;

/**
 * Interface NonexistentInterface
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface NonexistentInterface
{
    /**
     * Return true if element is nonexistent
     * @return bool
     */
    public function isNonexistent();

    /**
     * Sets nonexistent state for the element
     * @return void
     */
    public function setNonexistent();

    /**
     * Return name of nonexistent element
     * @return string
     */
    public function getNonexistentName();

    /**
     * Sets name of nonexistent element
     * @param $name
     * @return void
     */
    public function setNonexistentName($name);
}
