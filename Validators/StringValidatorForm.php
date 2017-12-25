<?php

namespace Iliich246\YicmsCommon\Validators;

/**
 * Class StringValidatorForm
 *
 * Form class for configure yii\validators\StringValidator
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class StringValidatorForm extends AbstractValidatorForm
{
    /**
     * @var array of messages of validator on all languages
     * user-defined error message used when the value is not a string
     */
    public $message;
    /**
     * @var integer maximum length
     */
    public $max;
    /**
     * @var array of messages of validator on all languages
     * user-defined error message used when the length of the value is greater than $max.
     */
    public $tooLong;
    /**
     * @var integer minimum  length
     */
    public $min;
    /**
     * @var array of messages of validator on all languages
     * user-defined error message used when the length of the value is smaller than $min.
     */
    public $tooShort;
    /**
     * @var integer the exact length that the value should be of
     */
    public $length;
    /**
     * @var array of messages of validator on all languages
     * for the customized message for a string that does not match desired length
     */
    public $notEqual;

    /**
     * @inheritdoc
     */
    public $serializeAble = [
        'message',
        'max',
        'tooLong',
        'min',
        'tooShort',
        'length',
        'notEqual',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),[
            [['message', 'tooLong', 'tooShort', 'notEqual'], 'safe'],
            [['max', 'min', 'length'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getRenderView()
    {
        return '@yicms-common/Validators/views/string_form';
    }

    /**
     * @inheritdoc
     */
    protected function getValidatorClass()
    {
        return 'yii\validators\StringValidator';
    }

    /**
     * @inheritdoc
     */
    protected function getValidatorFormName()
    {
        return 'string';
    }
}
