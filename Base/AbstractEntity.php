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

    /**
     * @var AbstractEntityBlock instance of entity block that keep this entity
     */
    protected $entityBlock;

    public function delete()
    {

    }

    public function isEntity()
    {

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
