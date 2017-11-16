<?php

use Iliich246\YicmsCommon\Base\AbstractTemplate;
use Iliich246\YicmsCommon\Tests\_testEssences\abstractTemplate\TestTemplate;
use Iliich246\YicmsCommon\Tests\_testEssences\abstractTemplate\TestTemplate2;

/**
 * Class AbstractTemplateTest
 */
class AbstractTemplateTest  extends \Codeception\Test\Unit
{
    use \Codeception\Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

    private $testTemplate;
    private $testTemplate2;

    protected function _before()
    {
        $this->testTemplate = new \Iliich246\YicmsCommon\Tests\_testEssences\abstractTemplate\TestTemplate();
        $this->testTemplate2 = new TestTemplate2();
        TestTemplate::eventToDataFetch();
        TestTemplate2::eventToDataFetch();
    }

    protected function _after()
    {
    }

    /**
     * @dataProvider dataProviderIssetInDb
     * @param $reference
     * @param $name
     * @param $count
     * @param $subCount
     * @param $countAccesses
     */
    public function testGetInstance($reference, $name, $count, $subCount, $countAccesses)
    {
        $this->tester->assertInstanceOf(AbstractTemplate::className(),
            \Iliich246\YicmsCommon\Tests\_testEssences\abstractTemplate\TestTemplate::getInstance(
                $reference, $name
            ));

        $reflection = new \ReflectionClass($this->testTemplate);
        $buffer = $reflection->getProperty('buffer');
        $buffer->setAccessible(true);

        $this->tester->assertArrayHasKey($reference, $buffer->getValue($this->testTemplate));
        $this->tester->assertArrayNotHasKey($reference . '_not', $buffer->getValue($this->testTemplate));
        $this->tester->assertEquals($count, count($buffer->getValue($this->testTemplate)));

        $this->tester->assertArrayHasKey($name, $buffer->getValue($this->testTemplate)[$reference]);
        $this->tester->assertArrayNotHasKey($name . '_not', $buffer->getValue($this->testTemplate)[$reference]);
        $this->tester->assertEquals($subCount, count($buffer->getValue($this->testTemplate)[$reference]));

        $this->tester->assertInstanceOf(\Iliich246\YicmsCommon\Tests\_testEssences\abstractTemplate\TestTemplate::className(),
            $buffer->getValue($this->testTemplate)[$reference][$name]);

        $this->tester->assertEquals($countAccesses, TestTemplate::$accessesToDb);
    }

    /**
     * @depends testGetInstance
     * @dataProvider dataProviderNotIssetInDb
     * @param $reference
     * @param $name
     * @param $count
     * @param $subCount
     * @param $countAccesses
     */
    public function testUnExistedInDbData($reference, $name, $count, $subCount, $countAccesses)
    {
        $this->tester->assertNull(\Iliich246\YicmsCommon\Tests\_testEssences\abstractTemplate\TestTemplate::getInstance(
                $reference, $name
        ));

        $reflection = new \ReflectionClass($this->testTemplate);
        $buffer = $reflection->getProperty('buffer');
        $buffer->setAccessible(true);

        $this->tester->assertArrayHasKey($reference, $buffer->getValue($this->testTemplate));
        $this->tester->assertArrayNotHasKey($reference . '_not', $buffer->getValue($this->testTemplate));
        $this->tester->assertEquals($count, count($buffer->getValue($this->testTemplate)));

        $this->tester->assertArrayHasKey($name, $buffer->getValue($this->testTemplate)[$reference]);
        $this->tester->assertArrayNotHasKey($name . '_not', $buffer->getValue($this->testTemplate)[$reference]);
        $this->tester->assertEquals($subCount, count($buffer->getValue($this->testTemplate)[$reference]));

        $this->tester->assertNull($buffer->getValue($this->testTemplate)[$reference][$name]);

        $this->tester->assertEquals($countAccesses, TestTemplate::$accessesToDb);
    }

    /**
     * @dataProvider dataProviderIssetInDb
     * @param $reference
     * @param $name
     * @param $count
     * @param $subCount
     * @param $countAccesses
     */
    public function testGetInstanceObj2($reference, $name, $count, $subCount, $countAccesses)
    {
        $this->tester->assertInstanceOf(AbstractTemplate::className(),
            \Iliich246\YicmsCommon\Tests\_testEssences\abstractTemplate\TestTemplate2::getInstance(
                $reference, $name
            ));

        $reflection = new \ReflectionClass($this->testTemplate2);
        $buffer = $reflection->getProperty('buffer');
        $buffer->setAccessible(true);

        $this->tester->assertArrayHasKey($reference, $buffer->getValue($this->testTemplate2));
        $this->tester->assertArrayNotHasKey($reference . '_not', $buffer->getValue($this->testTemplate2));
        $this->tester->assertEquals($count, count($buffer->getValue($this->testTemplate2)));

        $this->tester->assertArrayHasKey($name, $buffer->getValue($this->testTemplate2)[$reference]);
        $this->tester->assertArrayNotHasKey($name . '_not', $buffer->getValue($this->testTemplate2)[$reference]);
        $this->tester->assertEquals($subCount, count($buffer->getValue($this->testTemplate2)[$reference]));

        $this->tester->assertInstanceOf(\Iliich246\YicmsCommon\Tests\_testEssences\abstractTemplate\TestTemplate2::className(),
            $buffer->getValue($this->testTemplate2)[$reference][$name]);

        $this->tester->assertEquals($countAccesses, TestTemplate2::$accessesToDb);
    }

    /**     *
     * @dataProvider dataProviderNotIssetInDb
     * @param $reference
     * @param $name
     * @param $count
     * @param $subCount
     * @param $countAccesses
     */
    public function testUnExistedInDbDataObj2($reference, $name, $count, $subCount, $countAccesses)
    {
        $this->tester->assertNull(\Iliich246\YicmsCommon\Tests\_testEssences\abstractTemplate\TestTemplate2::getInstance(
            $reference, $name
        ));

        $reflection = new \ReflectionClass($this->testTemplate2);
        $buffer = $reflection->getProperty('buffer');
        $buffer->setAccessible(true);

        $this->tester->assertArrayHasKey($reference, $buffer->getValue($this->testTemplate2));
        $this->tester->assertArrayNotHasKey($reference . '_not', $buffer->getValue($this->testTemplate2));
        $this->tester->assertEquals($count, count($buffer->getValue($this->testTemplate2)));

        $this->tester->assertArrayHasKey($name, $buffer->getValue($this->testTemplate2)[$reference]);
        $this->tester->assertArrayNotHasKey($name . '_not', $buffer->getValue($this->testTemplate2)[$reference]);
        $this->tester->assertEquals($subCount, count($buffer->getValue($this->testTemplate2)[$reference]));

        $this->tester->assertNull($buffer->getValue($this->testTemplate2)[$reference][$name]);

        $this->tester->assertEquals($countAccesses, TestTemplate2::$accessesToDb);
    }

    public function dataProviderIssetInDb()
    {
        return [
            ['100100', 'name1', 1, 1, 1],
            ['100100', 'name2', 1, 2, 2],
            ['100100', 'name3', 1, 3, 3],
            ['230101', 'name1', 2, 1, 4],
            ['230101', 'name2', 2, 2, 5],
            ['230101', 'name3', 2, 3, 6],
            ['230101', 'name4', 2, 4, 7],
            ['912233', 'name1', 3, 1, 8],
            ['912233', 'name2', 3, 2, 9],
            ['912233', 'name3', 3, 3, 10],
            ['912233', 'name99', 3, 4, 11],
        ];
    }

    public function dataProviderNotIssetInDb()
    {
        return [
            ['111', 'name1', 4, 1, 12],
            ['111', 'name1', 4, 1, 12],
            ['111', 'name2', 4, 2, 13],
            ['111', 'name1', 4, 2, 13],
            ['100100', 'name4', 4, 4, 14],
            ['100100', 'name4', 4, 4, 14],
            ['100100', 'name5', 4, 5, 15],
            ['100100', 'name5', 4, 5, 15],
            ['100100', 'name5', 4, 5, 15],
        ];
    }
}
