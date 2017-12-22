<?php

namespace Iliich246\YicmsCommon\Validators;

/**
 * Class RequireValidatorForm
 *
 * Form class for configure yii\validators\RequiredValidator
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class RequireValidatorForm extends AbstractValidatorForm
{
    public $message;

    /**
     * @inheritdoc
     */
    protected function getValidatorClass()
    {
        return 'yii\validators\RequiredValidator';
    }
}
