<?php

namespace Iliich246\YicmsCommon\Validators;

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

    /**
     * @inheritdoc
     */
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
     */
    protected function getValidatorClass()
    {
        return 'yii\validators\RequiredValidator';
    }

    /**
     * @inheritdoc
     */
    protected function getRenderView()
    {
        return '@yicms-common/Validators/views/require_form';
    }

    /**
     * @inheritdoc
     */
    protected function getValidatorFormName()
    {
        return 'required';
    }
}
