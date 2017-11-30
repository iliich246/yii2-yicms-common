<?php

namespace Iliich246\YicmsCommon\Base;

use yii\db\ActiveRecord;

/**
 * Class SortOrderTrait
 *
 * This trait realize methods for controlling order of objects, that use it
 *
 * @author iliich246 <iliich246@gmail.com>
 */
trait SortOrderTrait
{
    /**
     * Returns false if this elements can up his order
     * @return bool
     */
    public function canDownOrder()
    {
        $orderAble = $this->getOrderAble();

        $node = $orderAble->getOrderQuery()
                          ->andWhere([
                              '>', $orderAble->getOrderFieldName(), $orderAble->getOrderValue()
                          ])
                          ->one();

        if ($node) return true;
        return false;
    }

    /**
     * Returns true if this elements can up his order
     * @return bool
     */
    public function canUpOrder()
    {
        $orderAble = $this->getOrderAble();

        $node = $orderAble->getOrderQuery()
                            ->andWhere([
                                '<', $orderAble->getOrderFieldName(), $orderAble->getOrderValue()
                            ])
                            ->one();

        if ($node) return true;
        return false;
    }


    /**
     * Down order
     * @return bool
     */
    public function downOrder()
    {
        if (!$this->canDownOrder()) return false;

        $orderAble = $this->getOrderAble();

        /** @var SortOrderInterface[]|ActiveRecord[] $nodes */
        $nodes = $orderAble->getOrderQuery()
                           ->andWhere([
                               '>', $orderAble->getOrderFieldName(), $orderAble->getOrderValue()
                           ])
                           ->all();

        if (!$nodes) return false;

        $minOrderNode = $nodes[0];

        foreach ($nodes as $node) {
            if ($node->getOrderValue() < $minOrderNode->getOrderValue())
                $minOrderNode = $node;
        }

        $tempOrderValue = $orderAble->getOrderValue();
        $orderAble->setOrderValue($minOrderNode->getOrderValue());
        $minOrderNode->setOrderValue($tempOrderValue);

        $minOrderNode->configToChangeOfOrder();
        $orderAble->configToChangeOfOrder();

        if ($orderAble->save(false) & $minOrderNode->save(false)) return true;

        return false;
    }

    /**
     * Up order
     * @return bool
     */
    public function upOrder()
    {
        //throw new Exception(print_r(1,true));
        if (!$this->canUpOrder()) return false;

        $orderAble = $this->getOrderAble();

        /** @var SortOrderInterface[]|ActiveRecord[] $nodes */
        $nodes = $orderAble->getOrderQuery()
            ->andWhere([
                '<', $orderAble->getOrderFieldName(), $orderAble->getOrderValue()
            ])
            ->all();

        if (!$nodes) return false;

        $maxOrderNode = $nodes[0];

        foreach ($nodes as $node) {
            if ($node->getOrderValue() > $maxOrderNode->getOrderValue())
                $maxOrderNode = $node;
        }

        $tempOrderValue = $orderAble->getOrderValue();
        $orderAble->setOrderValue($maxOrderNode->getOrderValue());
        $maxOrderNode->setOrderValue($tempOrderValue);

        $maxOrderNode->configToChangeOfOrder();
        $orderAble->configToChangeOfOrder();

        if ($orderAble->save(false) & $maxOrderNode->save(false)) return true;

        return false;
    }


    /**
     * Returns max order of object
     * @param bool|true $isIncrease if true, result will be increased on 1
     * @return int|mixed
     */
    public function maxOrder($isIncrease = true)
    {
        $orderAble = $this->getOrderAble();

        $max = $orderAble->getOrderQuery()
                         ->orderBy([
                             $orderAble->getOrderFieldName() => SORT_DESC
                         ])
                         ->asArray()
                         ->one();

        if (!$max) return $isIncrease ? 1 : 0;

        return $isIncrease ? ++$max[$orderAble->getOrderFieldName()] : $max[$orderAble->getOrderFieldName()] ;
    }

    /**
     * This method will sets concrete order to element and correctly shifts order of other
     * @param $order
     */
    public function setOrder($order)
    {
        //TODO: implement method, needed for drag and drop sortable
    }

    /**
     * Return instance of SortOrderInterface object who use this trait
     * @return SortOrderInterface|ActiveRecord
     */
    abstract public function getOrderAble();
}
