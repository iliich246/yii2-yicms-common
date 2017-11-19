<?php

namespace Iliich246\YicmsCommon\Base;

/**
 * Interface YicmsUserInterface
 *
 * This interface must inherit any user class working with yicms
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface YicmsUserInterface
{
    /**
     * Static method for detect if current user is developer
     * @return bool
     */
    public static function isDev();

    /**
     * Returns true, is this user is developer
     * @return bool
     */
    public function isThisDev();

    /**
     * Static method for detect if current user is admin
     * @return bool
     */
    public static function isAdmin();

    /**
     * Returns true, is this user is admin
     * @return bool
     */
    public function isThisAdmin();
}
