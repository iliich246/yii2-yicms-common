<?php

namespace Iliich246\YicmsCommon\Images;

use Iliich246\YicmsCommon\Base\AbstractEntity;

/**
 * Class Image
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Image extends AbstractEntity
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_images}}';
    }
}
