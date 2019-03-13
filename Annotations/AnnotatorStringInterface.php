<?php

namespace Iliich246\YicmsCommon\Annotations;

/**
 * Interface AnnotatorStringInterface
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface AnnotatorStringInterface
{
    /**
     * Return array of string annotations
     * @param $searchData
     * @return mixed
     */
    public static function getAnnotationsStringArray($searchData);
}
