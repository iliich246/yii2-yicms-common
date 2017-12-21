<?php

use Iliich246\YicmsCommon\Tests\_testEssences\sortOrder\TestOfSortOrder;

/**
 * Class SortOrderTest
 */
class SortOrderTest extends \Codeception\Test\Unit
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

    public function testCreateElement()
    {
        $testSort = new TestOfSortOrder();
        $testSort->scenario = TestOfSortOrder::SCENARIO_CREATE;

        $this->assertTrue($testSort->save());

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 1,
            'test_order' => 1,
        ]);

        $testSort = new TestOfSortOrder();
        $testSort->scenario = TestOfSortOrder::SCENARIO_CREATE;
        $this->assertTrue($testSort->save());

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 2,
            'test_order' => 2,
        ]);

        $testSort = new TestOfSortOrder();
        $testSort->scenario = TestOfSortOrder::SCENARIO_CREATE;
        $this->assertTrue($testSort->save());

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 3,
            'test_order' => 3,
        ]);
    }

    /**
     * @depends testCreateElement
     */
    public function testCanOrderMethods()
    {
        /** @var TestOfSortOrder $testSort */
        $testSort = TestOfSortOrder::findOne(1);

        $this->assertTrue($testSort->canDownOrder());
        $this->assertFalse($testSort->canUpOrder());

        /** @var TestOfSortOrder $testSort */
        $testSort = TestOfSortOrder::findOne(2);

        $this->assertTrue($testSort->canDownOrder());
        $this->assertTrue($testSort->canUpOrder());

        /** @var TestOfSortOrder $testSort */
        $testSort = TestOfSortOrder::findOne(3);

        $this->assertFalse($testSort->canDownOrder());
        $this->assertTrue($testSort->canUpOrder());
    }

    /**
     * @depends testCreateElement
     * @depends testCanOrderMethods
     */
    public function testChangeOfOrder()
    {
        /** @var TestOfSortOrder $testSort */
        $testSort = TestOfSortOrder::findOne(1);
        $this->assertTrue($testSort->downOrder());

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 1,
            'test_order' => 2,
        ]);

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 2,
            'test_order' => 1,
        ]);

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 3,
            'test_order' => 3,
        ]);

        $this->assertTrue($testSort->downOrder());

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 1,
            'test_order' => 3,
        ]);

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 3,
            'test_order' => 2,
        ]);

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 2,
            'test_order' => 1,
        ]);

        $this->assertFalse($testSort->downOrder());

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 1,
            'test_order' => 3,
        ]);

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 3,
            'test_order' => 2,
        ]);

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 2,
            'test_order' => 1,
        ]);

        /** @var TestOfSortOrder $testSort */
        $testSort = TestOfSortOrder::findOne(2);
        $this->assertFalse($testSort->upOrder());

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 1,
            'test_order' => 3,
        ]);

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 3,
            'test_order' => 2,
        ]);

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 2,
            'test_order' => 1,
        ]);

        $this->assertTrue($testSort->downOrder());

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 1,
            'test_order' => 3,
        ]);

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 3,
            'test_order' => 1,
        ]);

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 2,
            'test_order' => 2,
        ]);

        $this->assertTrue($testSort->downOrder());

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 1,
            'test_order' => 2,
        ]);

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 3,
            'test_order' => 1,
        ]);

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 2,
            'test_order' => 3,
        ]);

        $this->assertFalse($testSort->downOrder());

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 1,
            'test_order' => 2,
        ]);

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 3,
            'test_order' => 1,
        ]);

        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 2,
            'test_order' => 3,
        ]);
    }

    /**
     * @depends testCreateElement
     */
    public function testGetMaxOrder()
    {
        /** @var TestOfSortOrder $testSort */
        $testSort = TestOfSortOrder::findOne(1);
        $this->assertEquals(4,$testSort->maxOrder());
        $this->assertEquals(3,$testSort->maxOrder(false));

        $testSort = new TestOfSortOrder();
        $testSort->scenario = TestOfSortOrder::SCENARIO_CREATE;

        $this->assertTrue($testSort->save());
        $this->tester->seeRecord(TestOfSortOrder::className(), [
            'id' => 4,
            'test_order' => 4,
        ]);

        $this->assertEquals(5,$testSort->maxOrder());
        $this->assertEquals(4,$testSort->maxOrder(false));
    }
}
