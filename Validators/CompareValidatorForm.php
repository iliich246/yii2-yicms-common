<?php

namespace Iliich246\YicmsCommon\Validators;

use yii\validators\CompareValidator;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class CompareValidatorForm
 *
 * Form class for configure yii\validators\CompareValidator
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class CompareValidatorForm extends AbstractValidatorForm
{
    /**
     * @var array of messages of validator on all languages
     * the user-defined error message
     */
    public $message;
    /**
     * @var mixed The constant value to be compared with.
     */
    public $compareValue;
    /**
     * @var string The operator for comparison.
     */
    public $operator;
    /**
     * @var string The type of the values being compared
     */
    public $type;

    /**
     * @inheritdoc
     */
    public $serializeAble = [
        'message',
        'compareValue',
        'operator',
        'type',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),[
            [['message', 'compareValue', 'operator', 'type'], 'safe'],
        ]);
    }

    /**
     * Return list of validator types for drop down list
     * @return array
     */
    public function getTypesList()
    {
        return [
            'TYPE_STRING' => CompareValidator::TYPE_STRING,
            'TYPE_NUMBER' => CompareValidator::TYPE_NUMBER,
        ];
    }

    /**
     * Return list of validator operators for drop down list
     * @return array
     */
    public function getOperatorsList()
    {
        return [
            '=='  => '==',
            '===' => '===',
            '!='  => '!=',
            '!==' => '!==',
            '>'   => '>',
            '>='  => '>=',
            '<'   => '<',
            '<='  => '<=',
        ];
    }
    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function buildValidator()
    {
        if (!$this->isActivate) return false;

        $validator = new CompareValidator();
        $validator->attributes = ['value'];

        $currentLanguage = Language::getInstance()->getCurrentLanguage();
        $code = '\'' . $currentLanguage->code . '\'';

        if (isset($this->message[$code]) && trim($this->message[$code]))
            $validator->message = $this->message[$code];

        if ($this->type)
            $validator->type = $this->type;

        if ($this->operator)
            $validator->operator = $this->operator;

        if ($this->compareValue)
            $validator->compareValue = $this->compareValue;

        return $validator;
    }

    /**
     * @inheritdoc
     */
    public function getRenderView()
    {
        return '@yicms-common/Validators/views/compare_form';
    }

    /**
     * @inheritdoc
     */
    protected function getValidatorClass()
    {
        return 'yii\validators\CompareValidator';
    }

    /**
     * @inheritdoc
     */
    protected function getValidatorFormName()
    {
        return 'compare';
    }
}
