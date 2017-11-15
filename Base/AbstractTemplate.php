<?php

namespace Iliich246\YicmsCommon\Base;

use yii\db\ActiveRecord;

/**
 * Class AbstractTemplate
 *
 * All templates must inherit this class. This class implements methods for buffering.
 *
 * @property array $buffer variable that must be declared as static property in child classes;
 * It`s used for buffering templates and reduce database accesses
 * self::$buffer = [
 *     'templateReference1' => [
 *         'programName1' => <instance of child of AbstractTemplate with data fetched from data base>
 *         'programName2' => <object>
 *          ...
 *         'programNameN' => <object>
 *     ]
 *     ...
 *     'templateReferenceN' => [
 *       'programName1' => <object>
 *       'programName2' => <object>
 *        ...
 *       'programNameN' => <object>
 *     ]
 * ]
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractTemplate extends ActiveRecord
{
    /**
     * Returns instance of template object with data fetched from database;
     * @param $templateReference
     * @param $programName
     * @return static|null
     */
    public static function getInstance($templateReference, $programName)
    {
        if (($value = self::getFromCache($templateReference, $programName)) !== false) {
            return $value;
        }

        $value = self::fetchTemplate($templateReference, $programName);

        self::setToCache($templateReference, $programName, $value);

        return $value;
    }

    /**
     * This method must be overridden in child and return him static buffer
     * (abstract static methods violates the PHP strict standards)
     * @return array
     */
    protected static function getBuffer()
    {
        return [];
    }

    protected static function setBuffer($buffer)
    {
        static::setBuffer($buffer);
    }

    /**
     * @param $templateReference
     * @param $programName
     * @return static|bool
     */
    private static function getFromCache($templateReference, $programName)
    {
        $buffer = static::getBuffer();

        if (isset($buffer[$templateReference]) && array_key_exists($programName, $buffer[$templateReference]))
            return $buffer[$templateReference][$programName];

        return false;
    }

    /**
     * Stores a value identified by a key into cache.
     * @param integer $templateReference
     * @param string $programName
     * @param static $value
     * @return void
     */
    private static function setToCache($templateReference, $programName, $value)
    {
        $buffer = static::getBuffer();
        $buffer[$templateReference][$programName] = $value;

        self::setBuffer($buffer);
    }



    /**
     * Fetch from data base template object
     * @param $templateReference
     * @param $programName
     * @return static|null
     */
    private static function fetchTemplate($templateReference, $programName)
    {
        return static::find()->where([
            static::getTemplateReferenceName() => $templateReference,
            'program_name' => $programName
        ])->one();//->one();
    }

    /**
     * @return string
     */
    protected static function getTemplateReferenceName() {
        return [];
    }
}
