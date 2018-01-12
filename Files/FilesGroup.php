<?php

namespace Iliich246\YicmsCommon\Files;

use yii\base\Model;
use yii\widgets\ActiveForm;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class FilesGroup
 *
 * This class implements files group for admin part
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FilesGroup extends AbstractGroup
{
    /**
     * @var FilesReferenceInterface|FilesInterface object for current group
     */
    protected $referenceAble;
}
