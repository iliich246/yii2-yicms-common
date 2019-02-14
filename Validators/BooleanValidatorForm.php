<?php

namespace Iliich246\YicmsCommon\Validators;

use yii\validators\BooleanValidator;
use yii\validators\FileValidator;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class BooleanValidatorForm
 *
 * Form class for configure yii\validators\BooleanValidator
 *
 * @package Iliich246\YicmsCommon\Validators
 */
class BooleanValidatorForm extends AbstractValidatorForm
{
    /**
     * The value representing false status. Defaults to '0'.
     * @var mixed
     */
    public $falseValue;
    /**
     * Whether the comparison to $trueValue and $falseValue is strict.
     * @var boolean
     */
    public $strict;
    /**
     * The value representing true status.
     * @var mixed
     */
    public $trueValue;

    /**
     * @inheritdoc
     */
    public $serializeAble = [
        'falseValue',
        'strict',
        'trueValue',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),[
            [['falseValue', 'trueValue',], 'safe'],
            ['strict', 'boolean'],
        ]);
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function buildValidator()
    {
        if (!$this->isActivate) return false;

        $validator = new BooleanValidator();
        $validator->attributes = ['value'];

        if ($this->trueValue)
            $validator->trueValue = $this->trueValue;

        if ($this->falseValue)
            $validator->falseValue = $this->falseValue;

        if ($this->strict)
            $validator->strict = $this->strict;

        return $validator;
    }

    /**
     * @inheritdoc
     */
    public function getRenderView()
    {
        return '@yicms-common/Validators/views/boolean_form';
    }

    /**
     * @inheritdoc
     */
    protected function getValidatorClass()
    {
        return 'yii\validators\BooleanValidator';
    }

    /**
     * @inheritdoc
     */
    protected function getValidatorFormName()
    {
        return 'boolean';
    }
}
