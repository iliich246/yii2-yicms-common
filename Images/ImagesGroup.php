<?php

namespace Iliich246\YicmsCommon\Images;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Base\CommonException;
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
            $this->imageEntity->visible = true;
            $this->imageEntity->editable = true;
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
        $image = $this->getImageExistedInDbEntity();

        $this->imageEntity->image_reference = $this->imageReference;

        $path = CommonModule::getInstance()->imagesPath . DIRECTORY_SEPARATOR . 'orig' . DIRECTORY_SEPARATOR;

        if ($this->imagesBlock->language_type == ImagesBlock::LANGUAGE_TYPE_SINGLE) {
            if (!$this->imageEntity->image) return true;

            if ($this->imagesBlock->editable == false && !CommonModule::isUnderDev()) return true;

            if (!is_dir($path))
                FileHelper::createDirectory($path);

            if ($this->scenario == self::SCENARIO_UPDATE) {
                if (file_exists($path . $image->system_name) &&
                    !is_dir($path . $image->system_name))
                    unlink($path . $image->system_name);
            }

            $name = uniqid() . '.' . $this->imageEntity->image->extension;
            $this->imageEntity->image->saveAs($path . $name);

            $this->imageEntity->system_name = $name;
            $this->imageEntity->original_name =
                $this->imageEntity->image->baseName;
            $this->imageEntity->size = $this->imageEntity->image->size;
            $this->imageEntity->type = FileHelper::getMimeType($path . $name);

            $success = $this->imageEntity->save();

            if (!$success) return false;

            CropProcessor::handle($this->imageEntity);
            ThumbnailsProcessor::handle($this->imageEntity);

            return true;
        }

        if ($this->imagesBlock->language_type == ImagesBlock::LANGUAGE_TYPE_TRANSLATABLE) {

            if ($this->imagesBlock->editable == false && !CommonModule::isUnderDev()) return true;

            if (!is_dir($path))
                FileHelper::createDirectory($path);

            $success = true;

            foreach ($this->translateForms as $imageTranslateForm) {

                if (!$imageTranslateForm->translatedImage) continue;

                if ($this->scenario == self::SCENARIO_UPDATE) {
                    if (file_exists($path . $imageTranslateForm->getCurrentTranslateDb()->system_name) &&
                        !is_dir($path . $imageTranslateForm->getCurrentTranslateDb()->system_name)
                    )
                        unlink($path . $imageTranslateForm->getCurrentTranslateDb()->system_name);
                }

                $name = uniqid() . '.' . $imageTranslateForm->translatedImage->extension;
                $imageTranslateForm->translatedImage->saveAs($path . $name);

                $imageTranslateForm->getCurrentTranslateDb()->system_name = $name;
                $imageTranslateForm->getCurrentTranslateDb()->original_name =
                    $imageTranslateForm->translatedImage->baseName;
                $imageTranslateForm->getCurrentTranslateDb()->size = $imageTranslateForm->translatedImage->size;
                $imageTranslateForm->getCurrentTranslateDb()->type = FileHelper::getMimeType($path . $name);

                if (!$imageTranslateForm->getCurrentTranslateDb()->save()) $success = false;
            }

            return $success;
        }

        throw new CommonException('Unknown images template type');
    }

    /**
     * @inheritdoc
     */
    public function render(ActiveForm $form)
    {
        return ImagesRenderWidget::widget([
            'form' => $form,
            'imagesGroup' => $this,
            'imagesBlock' => $this->imagesBlock,
        ]);
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
