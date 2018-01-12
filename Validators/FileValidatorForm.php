<?php

namespace Iliich246\YicmsCommon\Validators;

use yii\validators\FileValidator;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class FileValidatorForm
 *
 * Form class for configure yii\validators\FileValidator
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FileValidatorForm extends AbstractValidatorForm
{
    public $maxSize;

    public $minSize;

    public $mimeTypes;

    public $extensions;
    /**
     * @var string the error message used when a file is not uploaded correctly.
     */
    public $message;

    public $uploadRequired;

    public $tooBig;

    public $tooSmall;

    public $wrongExtension;

    public $wrongMimeType;

    /**
     * @inheritdoc
     */
    public $serializeAble = [
        'maxSize',
        'minSize',
        'mimeTypes',
        'extensions',
        'message',
        'uploadRequired',
        'tooBig',
        'tooSmall',
        'wrongExtension',
        'wrongMimeType',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),[
            [['message', 'tooBig', 'tooSmall', 'extensions', 'wrongExtension', 'wrongMimeType'], 'safe'],
            [['maxSize', 'minSize'], 'integer'],
            ['uploadRequired', 'boolean'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function buildValidator()
    {

    }

    /**
     * @inheritdoc
     */
    public function getRenderView()
    {
        return '@yicms-common/Validators/views/file_form';
    }

    /**
     * @inheritdoc
     */
    protected function getValidatorClass()
    {
        return 'yii\validators\FileValidator';
    }

    /**
     * @inheritdoc
     */
    protected function getValidatorFormName()
    {
        return 'file';
    }
}
