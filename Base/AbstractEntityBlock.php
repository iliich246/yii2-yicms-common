<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;
use yii\db\ActiveQuery;

/**
 * Class AbstractEntityBlock
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractEntityBlock extends AbstractTemplate
{
    /** @var AbstractEntity[] that`s contains this block */
    private $entityBuffer = null;
    /** @var bool sets true, when entity block is nonexistent (can`t be fetched from db)/ */
    private $isNonexistent = false;

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
        if ($this->isNonexistent) return (self::getNoExistentEntity());

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
        if ($this->isNonexistent) return [];

        if (is_null($this->entityBuffer)) $this->fetchEntities();

        return $this->entityBuffer;
    }

    /**
     * Returns count of entities in this block
     * @return int
     */
    public function countEntities()
    {
        if ($this->isNonexistent) return 0;

        if (is_null($this->entityBuffer)) $this->fetchEntities();

        return count($this->entityBuffer);
    }

    /**
     * Return true if this block has entities
     * @return bool
     */
    public function isEntities()
    {
        if ($this->isNonexistent) return false;

        if (is_null($this->entityBuffer)) $this->fetchEntities();

        return !!$this->entityBuffer;
    }

    /**
     * @inheritdoc
     */
    public static function getInstance($templateReference, $programName)
    {
        $value = parent::getInstance($templateReference, $programName);

        if ($value) return $value;

        Yii::warning(
            "Can`t fetch for " . static::className() . " name = $programName and templateReference = $templateReference",
            __METHOD__);

        if (defined('YICMS_STRICT')) {
            throw new CommonException(
                "YICMS_STRICT_MODE:
                Can`t fetch for " . static::className() . " name = $programName and templateReference = $templateReference");
        }

        $value = new static();
        $value->isNonexistent = true;

        self::setToCache($templateReference, $programName, $value);

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        if ($this->isEntities()) {
            foreach($this->getEntities() as $entity)
                $entity->delete();
        }

        if (!$this->deleteSequence() && defined('YICMS_STRICT'))
            throw new CommonException(
                "YICMS_STRICT_MODE:
                Can`t perform delete sequence for " . static::className());

        return parent::delete();
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
     * Implements actions for correct delete children objects
     * @return bool
     */
    abstract protected function deleteSequence();

    /**
     * Returns class of entity of concrete block
     * @return string
     */
     protected static function getNoExistentEntity()
     {
         return static::getNoExistentEntity();
     }

    /**
     * @inheritdoc
     */
    public static function generateTemplateReference()
    {
        return parent::generateTemplateReference();
    }
}
