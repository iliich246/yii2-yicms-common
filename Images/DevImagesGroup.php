<?php

namespace Iliich246\YicmsCommon\Images;

use yii\base\Model;
use yii\widgets\ActiveForm;
use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class DevImagesGroup
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DevImagesGroup extends AbstractGroup
{
    /** @var integer fileTemplateReference value for current group */
    protected $imageTemplateReference;
    /** @var ImagesBlock current images block template with group is working (create or update) */
    public $imagesBlock;
    /** @var ImageNamesTranslatesForm[] */
    public $imagesNameTranslates;
    /** @var bool indicate that data in this group was saved in this action */
    public $justSaved = false;

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
    public function initialize($imagesBlockId = null)
    {
        if (!$imagesBlockId) {
            $this->imagesBlock = new ImagesBlock();
            $this->imagesBlock->image_template_reference = $this->imageTemplateReference;
            $this->imagesBlock->scenario = ImagesBlock::SCENARIO_CREATE;
            $this->scenario = self::SCENARIO_CREATE;
        } else {
            $this->imagesBlock = ImagesBlock::findOne($imagesBlockId);
            if (!$this->imagesBlock) throw new CommonException("Wrong imagesBlock = $imagesBlockId");

            $this->imagesBlock->scenario = ImagesBlock::SCENARIO_UPDATE;
            $this->scenario = self::SCENARIO_UPDATE;
        }

        $languages = Language::getInstance()->usedLanguages();

        $this->imagesNameTranslates = [];

        foreach($languages as $key => $language) {

            $imageNameTranslates = new ImageNamesTranslatesForm();
            $imageNameTranslates->setLanguage($language);
            $imageNameTranslates->setImagesBlockTemplate($this->imagesBlock);

            if (!$this->imagesBlock->isNewRecord)
                $imageNameTranslates->loadFromDb();

            $this->imagesNameTranslates[$key] = $imageNameTranslates;
        }
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return ($this->imagesBlock->validate() && Model::validateMultiple($this->imagesNameTranslates));
    }

    /**
     * @inheritdoc
     */
    public function load($data)
    {
        return $this->imagesBlock->load($data) && Model::loadMultiple($this->imagesNameTranslates, $data);
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        $needSaveImageBlock = false;

        if (!$needSaveImageBlock &&
            $this->imagesBlock->getOldAttribute('program_name') != $this->imagesBlock->program_name)
            $needSaveImageBlock = true;

        if (!$needSaveImageBlock &&
            $this->imagesBlock->getOldAttribute('type') != $this->imagesBlock->type)
            $needSaveImageBlock = true;

        if (!$needSaveImageBlock &&
            $this->imagesBlock->getOldAttribute('language_type') != $this->imagesBlock->language_type)
            $needSaveImageBlock = true;

        if (!$needSaveImageBlock &&
            $this->imagesBlock->getOldAttribute('visible') != $this->imagesBlock->visible)
            $needSaveImageBlock = true;

        if (!$needSaveImageBlock &&
            $this->imagesBlock->getOldAttribute('editable') != $this->imagesBlock->editable)
            $needSaveImageBlock = true;

        if (!$needSaveImageBlock &&
            $this->imagesBlock->getOldAttribute('max_images') != $this->imagesBlock->max_images)
            $needSaveImageBlock = true;

        if (!$needSaveImageBlock &&
            $this->imagesBlock->getOldAttribute('crop_type') != $this->imagesBlock->crop_type)
            $needSaveImageBlock = true;

        if (!$needSaveImageBlock &&
            $this->imagesBlock->getOldAttribute('crop_height') != $this->imagesBlock->crop_height)
            $needSaveImageBlock = true;

        if (!$needSaveImageBlock &&
            $this->imagesBlock->getOldAttribute('crop_width') != $this->imagesBlock->crop_width)
            $needSaveImageBlock = true;

        $success = true;

        if ($needSaveImageBlock)
            $success = $this->imagesBlock->save(false);

        if (!$success) return false;

        /** @var ImageNamesTranslatesForm $imageNameTranslate */
        foreach($this->imagesNameTranslates as $imageNameTranslate) {
            $needSaveImageTemplateName = false;

            if (!$needSaveImageTemplateName &&
                $imageNameTranslate->name != $imageNameTranslate->getCurrentTranslateDb()->name)
                $needSaveImageTemplateName = true;

            if (!$needSaveImageTemplateName &&
                $imageNameTranslate->description != $imageNameTranslate->getCurrentTranslateDb()->description)
                $needSaveImageTemplateName = true;

            if ($needSaveImageTemplateName)
                $imageNameTranslate->save();
        }

        $this->justSaved = true;

        //TODO: makes error handling
        return true;
    }

    /**
     * @inheritdoc
     */
    public function render(ActiveForm $form)
    {
        throw new CommonException('Not implemented for developer images group (not necessary)');
    }
}
