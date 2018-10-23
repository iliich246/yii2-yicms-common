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
    /** @var integer the maximum number of bytes required for the uploaded file */
    public $maxSize;
    /** @var integer the minimum number of bytes required for the uploaded file */
    public $minSize;
    /** @var string a list of file MIME types that are allowed to be uploaded */
    public $mimeTypes;
    /** @var string a list of file name extensions that are allowed to be uploaded */
    public $extensions;
    /**
     * @var array of messages of validator on all languages
     * the error message used when a file is not uploaded correctly.
     */
    public $message;
    /**
     * @var array of messages of validator on all languages
     * the error message used when the uploaded file is too large
     */
    public $tooBig;
    /**
     * @var array of messages of validator on all languages
     * the error message used when the uploaded file is too small
     */
    public $tooSmall;
    /**
     * @var array of messages of validator on all languages
     * the error message used when the uploaded file has an extension name that is not listed in $extensions.
     */
    public $wrongExtension;
    /**
     * @var array of messages of validator on all languages
     * the error message used when the file has an mime type that is not allowed by $mimeTypes property
     */
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
        ]);
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function buildValidator()
    {
        if (!$this->isActivate) return false;

        $validator = new FileValidator();
        $validator->attributes = ['file'];

        $currentLanguage = Language::getInstance()->getCurrentLanguage();
        $code = '\'' . $currentLanguage->code . '\'';

        if ($this->maxSize)
            $validator->maxSize = $this->maxSize;

        if ($this->minSize)
            $validator->minSize = $this->minSize;

        if ($this->mimeTypes)
            $validator->mimeTypes = $this->mimeTypes;

        if ($this->extensions)
            $validator->extensions = $this->extensions;

        if (isset($this->message[$code]) && trim($this->message[$code]))
            $validator->message = $this->message[$code];

        if (isset($this->tooBig[$code]) && trim($this->tooBig[$code]))
            $validator->tooBig = $this->tooBig[$code];

        if (isset($this->tooSmall[$code]) && trim($this->tooSmall[$code]))
            $validator->tooSmall = $this->tooSmall[$code];

        if (isset($this->wrongExtension[$code]) && trim($this->wrongExtension[$code]))
            $validator->wrongExtension = $this->wrongExtension[$code];

        if (isset($this->wrongMimeType[$code]) && trim($this->wrongMimeType[$code]))
            $validator->wrongMimeType = $this->wrongMimeType[$code];

        return $validator;
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
