<?php

use Iliich246\YicmsCommon\Base\AbstractModuleConfiguratorDb;

/**
 * Class ConfigurableModuleTest
 */
class ConfigurableModuleTest extends \Codeception\Test\Unit
{
    use \Codeception\Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testConfiguratorsDb()
    {
        $configurator1 = \Iliich246\YicmsCommon\Tests\_testEssences\configurableModule\Base\Test1ConfigDb::getInstance();
        $configurator2 = \Iliich246\YicmsCommon\Tests\_testEssences\configurableModule\Base\Test2ConfigDb::getInstance();

        $this->tester->assertInstanceOf(\Iliich246\YicmsCommon\Base\AbstractModuleConfiguratorDb::className(),
            $configurator1);
        $this->tester->assertInstanceOf(\Iliich246\YicmsCommon\Base\AbstractModuleConfiguratorDb::className(),
            $configurator2);

        $this->tester->assertEquals(AbstractModuleConfiguratorDb::INSTANCE_ID, $configurator1->id);
        $this->tester->assertEquals(AbstractModuleConfiguratorDb::INSTANCE_ID, $configurator2->id);

        $this->tester->assertEquals('field1_value', $configurator1->field1);
        $this->tester->assertEquals('field2_value', $configurator1->field2);
        $this->tester->assertEquals('field3_value', $configurator1->field3);
        $this->tester->assertEquals('field4_value', $configurator1->field4);
        $this->tester->assertEquals(5, $configurator1->field5);

        $this->tester->assertEquals('field1_value', $configurator2->field1);
        $this->tester->assertEquals(2, $configurator2->field2);
    }

    public function testConfigureOfModuleTest1()
    {
        $configModule1 = new \Iliich246\YicmsCommon\Tests\_testEssences\configurableModule\TestModule1('id1');

        $this->tester->assertEquals([
            1 => 'field1',
            2 => 'field2',
            3 => 'field3',
            4 => 'field4'
        ], $configModule1->configurable);

        $configurator1 = \Iliich246\YicmsCommon\Tests\_testEssences\configurableModule\Base\Test1ConfigDb::getInstance();

        $this->tester->assertEquals($configurator1->field1, $configModule1->field1);
        $this->tester->assertEquals($configurator1->field2, $configModule1->field2);
        $this->tester->assertEquals($configurator1->field3, $configModule1->field3);
        $this->tester->assertEquals($configurator1->field4, $configModule1->field4);
        $this->tester->assertNotEquals($configurator1->field5, $configModule1->field5);
        $this->tester->assertEquals('not_configured', $configModule1->field5);
        $this->tester->assertEquals('not_configured', $configModule1->field6);

    }

    public function testConfigureOfModuleTest2()
    {
        $configModule2 = new \Iliich246\YicmsCommon\Tests\_testEssences\configurableModule\TestModule2('id2');

        $this->tester->assertEquals([
            'field1',
            'field2'
        ], $configModule2->configurable);

        $configurator1 = \Iliich246\YicmsCommon\Tests\_testEssences\configurableModule\Base\Test2ConfigDb::getInstance();

        $this->tester->assertEquals($configurator1->field1, $configModule2->field1);
        $this->tester->assertEquals($configurator1->field2, $configModule2->field2);
        $this->tester->assertEquals('not_configured', $configModule2->field3);
    }

    public function testNoConfigModule()
    {
        $noConfigModule = new \Iliich246\YicmsCommon\Tests\_testEssences\configurableModule\TestModuleNoConfigurator('id3');

        $this->tester->assertEquals([
            'field1',
            'field2',
            'field3'
        ], $noConfigModule->configurable);

        $this->tester->assertEquals('not_configured', $noConfigModule->field1);
        $this->tester->assertEquals('not_configured', $noConfigModule->field2);
        $this->tester->assertEquals('not_configured', $noConfigModule->field3);
    }

    public function testOtherMethods()
    {
        $configModule1 = new \Iliich246\YicmsCommon\Tests\_testEssences\configurableModule\TestModule1('id1');

        $this->tester->assertEquals('Test1', $configModule1->getModuleName());
        $this->tester->assertEquals('Iliich246\YicmsCommon\Tests\_testEssences\configurableModule',
            $configModule1->getNameSpace());
    }
}
