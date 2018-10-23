<?php

namespace Iliich246\YicmsCommon\Validators;

use yii\validators\ImageValidator;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class ImageValidatorForm
 *
 * Form class for configure yii\validators\ImageValidator
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ImageValidatorForm extends AbstractValidatorForm
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
     * @var array of messages of validator on all languages
     * The error message used when the uploaded file is not an image
     */
    public $notImage;
    /** @var integer the minimum width in pixels */
    public $minWidth;
    /** @var integer the maximum width in pixels */
    public $maxWidth;
    /** @var integer the minimum height in pixels. */
    public $minHeight;
    /** @var integer the maximum height in pixels. */
    public $maxHeight;
    /**
     * @var array of messages of validator on all languages
     * the error message used when the image is under $minHeight
     */
    public $underWidth;
    /**
     * @var array of messages of validator on all languages
     * the error message used when the image is over $maxWidth
     */
    public $overWidth;
    /**
     * @var array of messages of validator on all languages
     * the error message used when the image is under $minHeight
     */
    public $underHeight;
    /**
     * @var array of messages of validator on all languages
     * the error message used when the image is over $maxHeight
     */
    public $overHeight;

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
        'notImage',
        'minWidth',
        'maxWidth',
        'minHeight',
        'maxHeight',
        'underWidth',
        'overWidth',
        'underHeight',
        'overHeight',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),[
            [[
                'message', 'tooBig', 'tooSmall', 'extensions', 'wrongExtension', 'wrongMimeType',
                'notImage', 'underWidth', 'overWidth', 'underHeight', 'overHeight'
            ], 'safe'],
            [['maxSize', 'minSize', 'minWidth', 'maxWidth', 'minHeight', 'maxHeight'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function buildValidator()
    {
        if (!$this->isActivate) return false;

        $validator = new ImageValidator();
        $validator->attributes = ['image'];

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

        if (isset($this->notImage[$code]) && trim($this->notImage[$code]))
            $validator->notImage = $this->notImage[$code];

        if ($this->minWidth)
            $validator->minWidth = $this->minWidth;

        if ($this->maxWidth)
            $validator->maxWidth = $this->maxWidth;

        if ($this->minHeight)
            $validator->minHeight = $this->minHeight;

        if ($this->maxHeight)
            $validator->maxHeight = $this->maxHeight;

        if (isset($this->underWidth[$code]) && trim($this->underWidth[$code]))
            $validator->underWidth = $this->underWidth[$code];

        if (isset($this->overWidth[$code]) && trim($this->overWidth[$code]))
            $validator->overWidth = $this->overWidth[$code];

        if (isset($this->underHeight[$code]) && trim($this->underHeight[$code]))
            $validator->underHeight = $this->underHeight[$code];

        if (isset($this->overHeight[$code]) && trim($this->overHeight[$code]))
            $validator->overHeight = $this->overHeight[$code];

        return $validator;
    }

    /**
     * @inheritdoc
     */
    public function getRenderView()
    {
        return '@yicms-common/Validators/views/image_form';
    }

    /**
     * @inheritdoc
     */
    protected function getValidatorClass()
    {
        return 'yii\validators\ImageValidator';
    }

    /**
     * @inheritdoc
     */
    protected function getValidatorFormName()
    {
        return 'image';
    }
}
