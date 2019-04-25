<?php

namespace Iliich246\YicmsCommon\Base;

use yii\base\Event;

/**
 * Class HookEvent
 * 
 * @author iliich246 <iliich246@gmail.com>
 */
class HookEvent extends Event
{
    protected $hook = null;

    /**
     * @param $hook
     */
    public function setHook($hook)
    {
        $this->hook = $hook;
    }

    /**
     * @return null
     */
    public function getHook()
    {
        return $this->hook;
    }
}
