<?php

namespace Iliich246\YicmsCommon\Validators;

/**
 * Class NumberValidatorForm
 *
 * Form class for configure yii\validators\StringValidator
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class NumberValidatorForm extends AbstractValidatorForm
{
    /**
     * @var array of messages of validator on all languages
     * the user-defined error message
     */
    public $message;
    /**
     * @var boolean whether the attribute value can only be an integer
     */
    public $integerOnly;
    /**
     * @var string the regular expression for matching integers
     */
    public $integerPattern;
    /**
     * @var integer upper limit of the number.
     */
    public $max;
    /**
     * @var integer lower limit of the number.
     */
    public $min;
    /**
     * @var string the regular expression for matching numbers.
     */
    public $numberPattern;
    /**
     * @var array of messages of validator on all languages
     * user-defined error message used when the value is bigger than $max.
     */
    public $tooBig;
    /**
     * @var array of messages of validator on all languages
     * user-defined error message used when the value is smaller than $min.
     */
    public $tooSmall;

    /**
     * @inheritdoc
     */
    public $serializeAble = [
        'message',
        'integerOnly',
        'integerPattern',
        'max',
        'min',
        'numberPattern',
        'tooBig',
        'tooSmall',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),[
            [['message', 'tooBig', 'tooSmall'], 'safe'],
            [['max', 'min'], 'integer'],
            [['integerPattern', 'numberPattern'], 'string'],
            ['integerOnly', 'boolean'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getRenderView()
    {
        return '@yicms-common/Validators/views/number_form';
    }

    /**
     * @inheritdoc
     */
    protected function getValidatorClass()
    {
        return 'yii\validators\NumberValidator';
    }

    /**
     * @inheritdoc
     */
    protected function getValidatorFormName()
    {
        return 'number';
    }
}
