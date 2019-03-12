<?php

namespace Iliich246\YicmsCommon\Annotation;

use Yii;
use yii\base\Component;

/**
 * Class Annotator
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Annotator extends Component
{
    /** @var AnnotatorFileInterface object */
    private $annotatorFile;

    /**
     * Sets AnnotatorFileInterface owner of this object
     * @param AnnotatorFileInterface $instance
     */
    public function setAnnotatorFileObject(AnnotatorFileInterface $instance)
    {
        $this->annotatorFile = $instance;
    }
}
