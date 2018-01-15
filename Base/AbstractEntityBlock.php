<?php

namespace Iliich246\YicmsCommon\Base;

use yii\db\ActiveQuery;

/**
 * Class AbstractEntityBlock
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractEntityBlock extends AbstractTemplate
{
    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getEntities());
    }

    public function getEntity()
    {

    }

    public function getEntities()
    {

    }

    public function countEntities()
    {

    }

    public function delete()
    {

    }

    /**
     * Return query for searching entities for concrete entity block
     * @return ActiveQuery
     */
    abstract public function getEntityQuery();


    /**
     * @inheritdoc
     */
    public static function generateTemplateReference()
    {
        return parent::generateTemplateReference();
    }
}
