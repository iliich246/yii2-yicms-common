<?php

namespace Iliich246\YicmsCommon\Annotations;

use Yii;
use SplFileObject;
use yii\base\Component;
use yii\db\Exception;
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
    /** @var SplFileObject of annotator file */
    private $fileResource;
    /** @var array of strings of file */
    private $fileStringArray = [];
    /** @var null|integer number of string of begin of auto annotation  */
    private $autoBlockStartIndex = null;
    /** @var array for keep auto annotations */
    private $autoAnnotationsArray = [];

    /**
     * Sets AnnotatorFileInterface owner of this object
     * @param AnnotatorFileInterface $instance
     * @throws \ReflectionException
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
        return $this->annotatorFileObject->getExtendsUseClass();
    }

    /**
     * Returns name of parent class
     * @return string
     */
    public function getExtendsClassName()
    {
        return $this->annotatorFileObject->getExtendsClassName();
    }

    private $lineNumber;

    /**
     * This method prepare annotator for work
     * @return array
     */
    public function prepare()
    {
        if (!$this->isAnnotatorFile())
            $this->createAnnotatorFile();

        $this->openAnnotatorFile('r');

        $lineNumber = 0;
        $isAutoBlock = false;

        while (!$this->fileResource->eof()) {
            $line = $this->fileResource->fgets();

            if (!$isAutoBlock && preg_match("/\|\|\|->/", $line)) {
                $this->fileStringArray[] = $line;
                $this->autoBlockStartIndex = $lineNumber + 1;
                $isAutoBlock = true;
                continue;
            }

            if ($isAutoBlock && preg_match("/\|\|\|<-/", $line)) {
                $this->fileStringArray[] = $line;
                $isAutoBlock = false;
                continue;
            }

            if (!$isAutoBlock)
                $this->fileStringArray[] = $line;

            $lineNumber++;

            if ($this->lineNumber > 1000) break;
        }
        $this->closeAnnotatorFile();

        return $this->fileStringArray;
    }

    /**
     * Method add annotation array
     * @param $array
     */
    public function addAnnotationArray($array)
    {
        $this->autoAnnotationsArray = array_merge($this->autoAnnotationsArray, $array);
    }

    /**
     * This method add auto annotations to annotation file
     */
    public function finish()
    {
        $this->openAnnotatorFile('w');

        $this->autoAnnotationsArray[] = ' *' . PHP_EOL;

        array_splice($this->fileStringArray, $this->autoBlockStartIndex, 0,  $this->autoAnnotationsArray);

        foreach ($this->fileStringArray as $line)
                 $this->fileResource->fwrite($line);

        $this->closeAnnotatorFile();
    }

    /**
     * Returns true if annotated file existed
     * @return bool
     */
    private function isAnnotatorFile()
    {
        if (!file_exists($this->annotatorFileObject->getAnnotationFilePath() .
            '/' . $this->annotatorFileObject->getAnnotationFileName() . '.php'))
            return false;

        return true;
    }

    /**
     * Method creates annotator file
     */
    private function createAnnotatorFile()
    {
        if (!is_dir($this->annotatorFileObject->getAnnotationFilePath()))
            mkdir($this->annotatorFileObject->getAnnotationFilePath());

        $file = fopen($this->annotatorFileObject->getAnnotationFilePath() .
            '/' . $this->annotatorFileObject->getAnnotationFileName() . '.php', "w");

        $view = new View();

        fwrite($file,
            $view->renderFile($this->annotatorFileObject->getAnnotationTemplateFile(), [
                'annotator' => $this
            ]));

        fclose($file);
    }

    /**
     * Open annotator file with selected mode
     * @param $mode
     * @return SplFileObject
     */
    private function openAnnotatorFile($mode)
    {
        $this->fileResource = new SplFileObject($this->annotatorFileObject->getAnnotationFilePath() .
            '/' . $this->annotatorFileObject->getAnnotationFileName() . '.php', $mode);

        return $this->fileResource;
    }

    /**
     * Close annotator file
     */
    private function closeAnnotatorFile()
    {
        $this->fileResource = null;
    }
}
