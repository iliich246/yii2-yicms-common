<?php

namespace Iliich246\YicmsCommon\Base;

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
     * Method sets entity block to this entity
     * @param AbstractEntityBlock $entityBlock
     */
    public function setEntityBlock(AbstractEntityBlock $entityBlock)
    {
        $this->entityBlock = $entityBlock;
    }
}
