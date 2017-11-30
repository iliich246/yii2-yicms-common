<?php

namespace Iliich246\YicmsCommon\Base;

use yii\db\ActiveQuery;

/**
 * Interface SortOrderInterface
 *
 * This interface must implement any class that must have order functionality
 * Also this class must use SortOrderTrait
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface SortOrderInterface
{
    /**
     * Returns group of objects that must be sorted by order
     * @return ActiveQuery
     */
    public function getOrderQuery();

    /**
     * Returns name of field, used in object to keep info about sort order
     * @return string
     */
    public static function getOrderFieldName();

    /**
     * Returns value of sort order
     * @return integer
     */
    public function getOrderValue();

    /**
     * Sets new value of sor order in object (without save in data base)
     * @param $value
     * @return mixed
     */
    public function setOrderValue($value);

    /**
     * This method configs object to mode compatible with change of order
     * @return void
     */
    public function configToChangeOfOrder();
}
