<?php

namespace Iliich246\YicmsCommon\Files;

use Iliich246\YicmsCommon\Base\AbstractEntity;

/**
 * Class File
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class File extends AbstractEntity
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_files}}';
    }
}
