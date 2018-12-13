<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class AbstractEntity
 *
 * @property integer $id
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractEntity extends ActiveRecord
{
    const SCENARIO_CREATE = 0x00;
    const SCENARIO_UPDATE = 0x01;

    /** @var AbstractEntityBlock instance of entity block that keep this entity */
    protected $entityBlock;
    /** @var bool sets true, when entity is nonexistent (can`t be fetched from db) */
    protected $isNonexistent = false;
    /** @var string value for keep program name in nonexistent mode */
    private $nonexistentProgramName;

    /**
     * @inheritdoc
     */
    public function delete()
    {
        if (!$this->deleteSequence() && defined('YICMS_STRICT'))
            throw new CommonException(
            "YICMS_STRICT_MODE:
                Can`t perform delete sequence for " . static::className());

        return parent::delete();
    }

    public function isEntity()
    {

    }

    /**
     * Set`s entity as nonexistent
     * @return void
     */
    public function setNonexistent()
    {
        $this->isNonexistent = true;
    }

    /**
     * Returns image nonexistent state
     * @return bool
     */
    public function isNonexistent()
    {
        return $this->isNonexistent;
    }

    /**
     * Generates reference key
     * @return string
     * @throws CommonException
     */
    public static function generateReference()
    {
        $value = strrev(uniqid());

        $coincidence = true;
        $counter = 0;

        while($coincidence) {
            if (!static::find()->where([
                static::getReferenceName() => $value
            ])->one()) return $value;

            if ($counter++ > 100) {
                Yii::error('Looping', __METHOD__);
                throw new CommonException('Looping in ' . __METHOD__);
            }
        }

        throw new CommonException('Can`t reach there 0_0' . __METHOD__);
    }

    /**
     * This method must be overridden in child and return name of db field with template reference
     * (abstract static methods violates the PHP strict standards)
     * @return string
     */
    protected static function getReferenceName()
    {
        return '';
    }

    /**
     * Method sets entity block to this entity
     * @param AbstractEntityBlock $entityBlock
     */
    public function setEntityBlock(AbstractEntityBlock $entityBlock)
    {
        $this->entityBlock = $entityBlock;
    }

    /**
     * Return entity block for this entity
     * @return array|AbstractEntityBlock|null|ActiveRecord
     */
    public function getEntityBlock()
    {
        if ($this->entityBlock) return $this->entityBlock;

        return $this->entityBlock = $this->entityBlockQuery()->one();
    }

    /**
     * Implements actions for correct delete children objects
     * @return bool
     */
    abstract protected function deleteSequence();

    /**
     * Return path to physical destination of this entity
     * @return string
     */
    abstract public function getPath();

    /**
     * Returns query for find child entity block
     * @return ActiveQuery
     */
    abstract public function entityBlockQuery();
}
