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
class ImageValidatorForm extends FileValidatorForm
{
    public $notImage;

    public $minWidth;

    public $maxWidth;

    public $minHeight;

    public $maxHeight;

    public $underWidth;

    public $overWidth;

    public $underHeight;

    public $overHeight;

    public function __construct($config = [])
    {
        parent::__construct($config);

        //$this->serializeAble = array_merge(parent::$serializeAble, )
    }

    /**
     * @inheritdoc
     */
    public $serializeAble = [
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
    public function buildValidator()
    {

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
