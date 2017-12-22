<?php

namespace Iliich246\YicmsCommon\Validators;

use yii\base\Model;

/**
 * Class AbstractValidatorForm
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractValidatorForm extends Model
{
    public static $builtInValidators = [
        'required' => 'Iliich246\YicmsCommon\Validators\RequiredValidatorForm',
        'string' => 'Iliich246\YicmsCommon\Validators\StringValidatorForm',
        'number' => 'Iliich246\YicmsCommon\Validators\NumberValidatorForm',
    ];

    public static function listOfWidgets()
    {
        return self::$builtInValidators;
    }

    /**
     * @var string class of yii validator, for which this form
     */
    public $validator;

    public $saveAbleFields = [];

    public $translateAbleFields = [];

    /**
     * returns class of yii validator, for which this form
     * @return string
     */
    protected abstract function getValidatorClass();

//    public function __sleep()
//    {
//
//    }
//
//    public function __wakeup()
//    {
//
//    }
}
