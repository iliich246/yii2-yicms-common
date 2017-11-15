<?php

namespace Iliich246\YicmsCommon\Tests\_testEssences\abstractHandler;

use Iliich246\YicmsCommon\Base\AbstractHandler;

/**
 * Class TestHandler
 *
 * This class is only for test AbstractHandler
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class TestHandler extends AbstractHandler
{
    public function testGet($key)
    {
        return $this->getFromCache($key);
    }

    public function testSet($key, $data)
    {
        $this->setToCache($key, $data);
    }

    public function testExist($key)
    {
        return $this->existsInCache($key);
    }

    public function testGetOrSet($key, $callable)
    {
        return $this->getOrSet($key, $callable);
    }
}
