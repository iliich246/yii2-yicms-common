<?php

namespace Iliich246\YicmsCommon\Images;

use yii\base\Model;
use yii\widgets\ActiveForm;
use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Base\CommonException;

/**
 * Class DevImagesGroup
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DevImagesGroup extends AbstractGroup
{
    /**
     * @var integer fileTemplateReference value for current group
     */
    protected $imageTemplateReference;

    /**
     *  Sets imageTemplateReference
     * @param $imageTemplateReference
     */
    public function setImagesTemplateReference($imageTemplateReference)
    {
        $this->imageTemplateReference = $imageTemplateReference;
    }

    /**
     * @inheritdoc
     */
    public function initialize($filesBlockId = null)
    {

    }

    /**
     * @inheritdoc
     */
    public function validate()
    {

    }

    /**
     * @inheritdoc
     */
    public function load($data)
    {

    }

    /**
     * @inheritdoc
     */
    public function save()
    {

    }

    /**
     * @inheritdoc
     */
    public function render(ActiveForm $form)
    {
        throw new CommonException('Not implemented for developer images group (not necessary)');
    }
}
