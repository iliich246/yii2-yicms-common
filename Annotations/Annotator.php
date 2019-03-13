<?php

namespace Iliich246\YicmsCommon\Annotations;

use Yii;
use yii\base\Component;
use yii\web\View;

/**
 * Class Annotator
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Annotator extends Component
{
    /** @var AnnotatorFileInterface object */
    private $annotatorFileObject;
    /** @var \ReflectionClass of annotatorFileObject */
    private $annotatorReflect;
    /** @var resource of annotator file */
    private $fileResource;

    /**
     * Sets AnnotatorFileInterface owner of this object
     * @param AnnotatorFileInterface $instance
     */
    public function setAnnotatorFileObject(AnnotatorFileInterface $instance)
    {
        $this->annotatorFileObject = $instance;

        $this->annotatorReflect = new \ReflectionClass($instance);
    }

    /**
     * AnnotatorFileInterface getter
     * @return AnnotatorFileInterface
     */
    public function getAnnotatorFileObject()
    {
        return $this->annotatorFileObject;
    }

    /**
     * Return name of creating class for template
     * @return string
     */
    public function getClassName()
    {
        return $this->annotatorFileObject->getAnnotationFileName();
    }

    /**
     * Return correct namespace for template
     * @return string
     */
    public function getNamespace()
    {
        return $this->annotatorFileObject->getAnnotationFileNamespace();
    }

    /**
     * Return correct use record for parent class
     * @return string
     */
    public function getExtendsUseClass()
    {
        return $this->annotatorReflect->getName();
    }

    /**
     * Returns name of parent class
     * @return string
     */
    public function getExtendsClassName()
    {
        return $this->annotatorReflect->getShortName();
    }

    public function test()
    {
        if (!file_exists($this->annotatorFileObject->getAnnotationFilePath() .
            '/' . $this->annotatorFileObject->getAnnotationFileName() . '.php'))
            $this->createAnnotatorFile();
        else {
            $this->fileResource = new \SplFileObject($this->annotatorFileObject->getAnnotationFilePath() .
                '/' . $this->annotatorFileObject->getAnnotationFileName() . '.php');
        }

    }

    private function createAnnotatorFile()
    {
        if (!is_dir($this->annotatorFileObject->getAnnotationFilePath()))
            mkdir($this->annotatorFileObject->getAnnotationFilePath());

        $this->fileResource = fopen($this->annotatorFileObject->getAnnotationFilePath() .
            '/' . $this->annotatorFileObject->getAnnotationFileName() . '.php', "w");

        $view = new View();

        fwrite($this->fileResource,
            $view->renderFile($this->annotatorFileObject->getAnnotationTemplateFile(), [
                'annotator' => $this
            ]));

    }
}
