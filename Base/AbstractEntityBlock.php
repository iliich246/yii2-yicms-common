<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;
use yii\db\ActiveQuery;

/**
 * Class AbstractEntityBlock
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractEntityBlock extends AbstractTemplate implements NonexistentInterface
{
    /** @var AbstractEntity[] that`s contains this block */
    private $entityBuffer = null;
    /** @var bool if true image block will behaviour as nonexistent   */
    protected $isNonexistent = false;
    /** @var string value for keep program name in nonexistent mode */
    protected $nonexistentProgramName;

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
        if ($this->isNonexistent) return $this->getNoExistentEntity();

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
     * @throws CommonException
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

        $value                         = new static();
        $value->isNonexistent          = true;
        $value->nonexistentProgramName = $programName;

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
     * @return void
     */
    private function fetchEntities()
    {
        $this->entityBuffer = $this->getEntityQuery()->all();

        foreach($this->entityBuffer as $entity)
            $entity->setEntityBlock($this);
    }

    /**
     * @inheritdoc
     */
    public static function generateTemplateReference()
    {
        return parent::generateTemplateReference();
    }

    /**
     * @inheritdoc
     */
    public function isNonexistent()
    {
        return $this->isNonexistent;
    }

    /**
     * @inheritdoc
     */
    public function setNonexistent()
    {
        $this->isNonexistent = true;
    }

    /**
     * @inheritdoc
     */
    public function getNonexistentName()
    {
        return $this->nonexistentProgramName;
    }

    /**
     * @inheritdoc
     */
    public function setNonexistentName($name)
    {
        $this->nonexistentProgramName = $name;
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
     abstract protected function getNoExistentEntity();
}
