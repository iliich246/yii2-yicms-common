<?php

namespace Iliich246\YicmsCommon\Annotations;

/**
 * Interface AnnotatorFileInterface
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface AnnotatorFileInterface
{
    /**
     * Return configured annotator for current object
     * @return Annotator
     */
    public function getAnnotator();

    /**
     * Return name of file of annotation
     * @return string
     */
    public function getAnnotationFileName();

    /**
     * Return path for file of annotation
     * @return string
     */
    public function getAnnotationFilePath();

    /**
     * Return correct use record for parent class
     * @return string
     */
    public function getExtendsUseClass();

    /**
     * Returns name of parent class
     * @return string
     */
    public function getExtendsClassName();

    /**
     * Return namespace of annotation file
     * @return string
     */
    public static function getAnnotationFileNamespace();

    /**
     * Return path for annotation template file
     * @return string
     */
    public static function getAnnotationTemplateFile();


}
