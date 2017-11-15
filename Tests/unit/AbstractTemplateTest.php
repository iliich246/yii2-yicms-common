<?php

use Iliich246\YicmsCommon\Base\AbstractTemplate;

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

    protected function _before()
    {
        //$this->testTemplate = new \Iliich246\YicmsCommon\Tests\_testEssences\abstractTemplate\TestTemplate();
    }

    protected function _after()
    {
    }

    /**
     * @dataProvider dataProviderIssetInDb
     * @param $reference
     * @param $name
     */
    private function testFetchTemplate($reference, $name)
    {
        $reflection = new ReflectionClass($this->testTemplate);
        $method = $reflection->getParentClass()->getMethod('fetchTemplate');
        $method->setAccessible(true);

        $this->assertInstanceOf(AbstractTemplate::className(),
            $method->invokeArgs($this->testTemplate, [$reference, $name])
        );
    }

    /**
     * @dataProvider dataProviderIssetInDb
     * @param $reference
     * @param $name
     */
    public function testGetInstance($reference, $name)
    {
        $this->assertInstanceOf(AbstractTemplate::className(),
            \Iliich246\YicmsCommon\Tests\_testEssences\abstractTemplate\TestTemplate::getInstance(
                $reference, $name
            ));
    }


    public function dataProviderIssetInDb()
    {
        return [
            ['100100', 'name1'],
            ['100100', 'name2'],
            ['100100', 'name3'],
            ['230101', 'name1'],
            ['230101', 'name2'],
            ['230101', 'name3'],
            ['230101', 'name4'],
            ['912233', 'name1'],
            ['912233', 'name2'],
            ['912233', 'name3'],
            ['912233', 'name99'],
        ];
    }
}
