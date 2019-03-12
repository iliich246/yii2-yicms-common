<?php

namespace Iliich246\YicmsCommon\Annotations;

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
    private $annotatorFileObject;

    /**
     * Sets AnnotatorFileInterface owner of this object
     * @param AnnotatorFileInterface $instance
     */
    public function setAnnotatorFileObject(AnnotatorFileInterface $instance)
    {
        $this->annotatorFileObject = $instance;
    }

    private function createAnnotatorFile()
    {

    }

    private function openAnnotatorFile()
    {

    }
}
