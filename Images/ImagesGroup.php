<?php

namespace Iliich246\YicmsCommon\Images;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class ImagesGroup
 *
 * This class implements images group for admin part
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ImagesGroup extends AbstractGroup
{
    /**
     * @var string imageTemplateReference value for current group
     */
    protected $imageTemplateReference;
    /**
     * @var ImagesBlock instance associated with this block
     */
    public $imagesBlock;
    /**
     * @var string current image reference key
     */
    public $imageReference;
    /**
     * @var Image instance for this group
     */
    public $imageEntity;
    /**
     * @var ImageTranslateForm[]
     */
    public $translateForms = [];

    /**
     * Sets current imageTemplateReference
     * @param $imageTemplateReference
     */
    public function setImageTemplateReference($imageTemplateReference)
    {
        $this->imageTemplateReference = $imageTemplateReference;
    }

    /**
     * Sets current imageReference key
     * @param $imageReference
     */
    public function setImageReference($imageReference)
    {
        $this->imageReference = $imageReference;
    }

    /**
     * Sets current ImagesBlock
     * @param ImagesBlock $imagesBlock
     */
    public function setImagesBlock(ImagesBlock $imagesBlock)
    {
        $this->imagesBlock = $imagesBlock;
    }

    /**
     * Sets image entity for update mode
     * @param Image $imageEntity
     */
    public function setImageEntity(Image $imageEntity)
    {
        $this->imageEntity = $imageEntity;
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        if ($this->scenario == self::SCENARIO_CREATE) {
            $this->imageEntity = new Image();
            $this->imageEntity->setEntityBlock($this->imagesBlock);
        }

        $this->imageEntity->prepareValidators();

        if ($this->imagesBlock->language_type == ImagesBlock::LANGUAGE_TYPE_SINGLE)
            return;

        $imagesBlockId = $this->imagesBlock->id;

        $languages = Language::getInstance()->usedLanguages();

        foreach ($languages as $languageKey => $language) {

            $imageTranslate = new ImageTranslateForm();
            $imageTranslate->scenario = ImageTranslateForm::SCENARIO_CREATE;
            $imageTranslate->setImageBlock($this->imagesBlock);
            $imageTranslate->setImageEntity($this->imageEntity);
            $imageTranslate->setLanguage($language);

            if ($this->scenario == self::SCENARIO_UPDATE) {
                $imageTranslate->loadFromDb();
            }

            $this->translateForms["$languageKey-$imagesBlockId"] = $imageTranslate;
        }
    }

    /**
     * @inheritdoc
     */
    public function load($data)
    {
        $success = false;

        if ($this->imageEntity->load($data)) {
            $this->imageEntity->image = UploadedFile::getInstance($this->imageEntity, "image");
            $success = true;
        }

        if ($this->imagesBlock->language_type == ImagesBlock::LANGUAGE_TYPE_SINGLE)
            return $success;

        if ($success | Model::loadMultiple($this->translateForms, $data)) {
            foreach ($this->translateForms as $fileTranslateForm) {
                $key = $fileTranslateForm->getKey();
                $fileTranslateForm->translatedImage = UploadedFile::getInstance($fileTranslateForm, "[$key]translatedImage");
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        if ($this->imagesBlock->language_type == ImagesBlock::LANGUAGE_TYPE_SINGLE) {
            return $this->imageEntity->validate();
        }

        return Model::validateMultiple($this->translateForms);
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

    }

    /**
     * Returns instance of Image entity that necessarily exists in the database
     * @return Image
     */
    public function getImageExistedInDbEntity()
    {
        if ($this->imageEntity->isNewRecord) {
            $this->imageEntity->common_images_templates_id = $this->imagesBlock->id;
            $this->imageEntity->image_reference = $this->imageReference;
            $this->imageEntity->image_order = $this->imageEntity->maxOrder();
            $this->imageEntity->visible = true;
            $this->imageEntity->editable = true;

            $this->imageEntity->save();
        }

        return $this->imageEntity;
    }

}
