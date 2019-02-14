<?php

namespace Iliich246\YicmsCommon\Validators;

use yii\validators\RequiredValidator;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class RequireValidatorForm
 *
 * Form class for configure yii\validators\RequiredValidator
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class RequiredValidatorForm extends AbstractValidatorForm
{
    /** @var array of messages of validator on all languages  */
    public $message;
    /** @inheritdoc */
    public $serializeAble = [
        'message',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),[
            ['message', 'safe']
        ]);
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function buildValidator()
    {
        if (!$this->isActivate) return false;

        $validator = new RequiredValidator();
        $validator->attributes = ['value'];

        $currentLanguage = Language::getInstance()->getCurrentLanguage();
        $code = '\'' . $currentLanguage->code . '\'';

        if (isset($this->message[$code]) && trim($this->message[$code]))
            $validator->message = $this->message[$code];

        return $validator;
    }

    /**
     * @inheritdoc
     */
    public function getRenderView()
    {
        return '@yicms-common/Validators/views/require_form';
    }

    /**
     * @inheritdoc
     */
    protected function getValidatorClass()
    {
        return 'yii\validators\RequiredValidator';
    }

    /**
     * @inheritdoc
     */
    protected function getValidatorFormName()
    {
        return 'required';
    }
}
