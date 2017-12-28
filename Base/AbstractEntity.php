<?php

namespace Iliich246\YicmsCommon\Base;

use Iliich246\YicmsCommon\Files\FilesBlock;
use yii\db\ActiveRecord;

/**
 * Class AbstractEntity
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class AbstractEntity extends ActiveRecord
{
    /**
     * @var FilesBlock instance of field template
     */
    private $block;
}
