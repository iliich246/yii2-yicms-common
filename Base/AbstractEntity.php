<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class AbstractEntity
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class AbstractEntity extends ActiveRecord
{
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

    public function getPath()
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
}
