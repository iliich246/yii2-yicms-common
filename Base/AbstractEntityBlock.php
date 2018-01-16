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
     * @var AbstractEntity[] that`s contains this block
     */
    private $entityBuffer = null;

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getEntities());
    }

    /**
     * Return one first entity for this block
     * @return bool|AbstractEntity
     */
    public function getEntity()
    {
        if (is_null($this->entityBuffer)) $this->fetchEntities();

        foreach($this->entityBuffer as $entity)
            return $entity;

        return false;
    }

    /**
     * Return array of entities of this block
     * @return AbstractEntity[]
     */
    public function getEntities()
    {
        if (is_null($this->entityBuffer)) $this->fetchEntities();

        return $this->entityBuffer;
    }

    /**
     * Returns count of entities in this block
     * @return int
     */
    public function countEntities()
    {
        if (is_null($this->entityBuffer)) $this->fetchEntities();

        return count($this->entityBuffer);
    }

    /**
     * Return true if this block has entities
     * @return bool
     */
    public function isEntities()
    {
        if (is_null($this->entityBuffer)) $this->fetchEntities();

        return !!$this->entityBuffer;
    }

    public function delete()
    {

    }

    /**
     * Return array of fetched from db instances of entities
     * @return static[]
     */
    private function fetchEntities()
    {
        $this->entityBuffer = $this->getEntityQuery()->all();

        foreach($this->entityBuffer as $entity)
            $entity->setEntityBlock($this);
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
