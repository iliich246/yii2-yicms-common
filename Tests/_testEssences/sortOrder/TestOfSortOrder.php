<?php

namespace Iliich246\YicmsCommon\Tests\_testEssences\sortOrder;

use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\Base\SortOrderTrait;
use Iliich246\YicmsCommon\Base\SortOrderInterface;

/**
 * Class TestOfSortOrder
 *
 * This class is only for test SortOrderTrait and
 *
 * @property integer $id
 * @property integer $test_order
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class TestOfSortOrder extends ActiveRecord implements SortOrderInterface
{
    use SortOrderTrait;

    const SCENARIO_CREATE = 0;
    const SCENARIO_UPDATE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%test_sort_order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['test_order', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'test_order',
            ],
            self::SCENARIO_UPDATE => [
                'test_order',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->scenario == self::SCENARIO_CREATE)
            $this->test_order = $this->maxOrder();

        return parent::save($runValidation, $attributeNames);
    }

    /**
     * @inheritdoc
     */
    public function getOrderQuery()
    {
        return self::find();
    }

    /**
     * @inheritdoc
     */
    public static function getOrderFieldName()
    {
        return 'test_order';
    }

    /**
     * @inheritdoc
     */
    public function getOrderValue()
    {
        return $this->test_order;
    }

    /**
     * @inheritdoc
     */
    public function setOrderValue($value)
    {
        $this->test_order = $value;
    }

    /**
     * @inheritdoc
     */
    public function configToChangeOfOrder()
    {
        $this->scenario = self::SCENARIO_UPDATE;
    }

    /**
     * @inheritdoc
     */
    public function getOrderAble()
    {
        return $this;
    }
}
