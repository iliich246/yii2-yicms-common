<?php

/**
 * Class AbstractHandlerTest
 */
class AbstractHandlerTest extends \Codeception\Test\Unit
{
    use \Codeception\Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var \Iliich246\YicmsCommon\Tests\_testEssences\abstractHandler\TestHandler  */
    private $testHandler;

    protected function _before()
    {
        $this->testHandler = new \Iliich246\YicmsCommon\Tests\_testEssences\abstractHandler\TestHandler();
    }

    protected function _after()
    {
    }

    public function testInitialization()
    {
        $reflection = new ReflectionClass($this->testHandler);
        $buffer = $reflection->getParentClass()->getProperty('buffer');
        $buffer->setAccessible(true);

        $this->tester->assertEquals([], $buffer->getValue($this->testHandler));
    }

    /**
     * @depends testInitialization
     * @dataProvider cacheProvider
     * @param $key
     * @param $data
     */
    public function testSetInCache($key, $data)
    {
        $reflection = new ReflectionClass($this->testHandler);
        $buffer = $reflection->getParentClass()->getProperty('buffer');
        $buffer->setAccessible(true);

        $this->testHandler->testSet($key, $data);

        $this->tester->assertArrayHasKey($key, $buffer->getValue($this->testHandler));
        $this->tester->assertEquals($data, $buffer->getValue($this->testHandler)[$key]);
    }

    /**
     * @depends testSetInCache
     * @dataProvider cacheProvider
     * @param $key
     * @param $data
     */
    public function testGetFromCache($key, $data)
    {
       $this->testHandler->testSet($key, $data);
       $this->assertEquals($data, $this->testHandler->testGet($key));
       $this->assertEquals(false, $this->testHandler->testGet($key . '_not'));
    }

    /**
     * @depends testSetInCache
     * @dataProvider cacheProvider
     * @param $key
     * @param $data
     */
    public function testExistedMethod($key, $data)
    {
        $this->testHandler->testSet($key, $data);
        $this->assertEquals(true, $this->testHandler->testExist($key));
        $this->assertEquals(false, $this->testHandler->testExist($key . '_not'));
    }

    /**
     * @depends testSetInCache
     * @dataProvider cacheProvider
     * @param $key
     * @param $data
     */
    public function testGetOrSetMethod($key, $data)
    {
        $this->assertEquals($data, $this->testHandler->testGetOrSet($key, function() use($data)  {
            return $data;
        }));

        $this->assertEquals($data, $this->testHandler->testGetOrSet($key, function() use($data)  {
            return $data;
        }));

        $this->assertEquals($data, $this->testHandler->testGet($key));
    }

    public function cacheProvider()
    {
        return [
            ['key1', 'data1'],
            ['key2', 'data2'],
            [5, 25],
            ['erqrt', 'data2'],
            ['erqrt', 'data3'],
            [123, '0xABCD'],
        ];
    }
}
