<?php

namespace Iliich246\YicmsCommon\Images;

use yii\web\UploadedFile;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AbstractTranslateForm;

/**
 * Class ImageTranslateForm
 *
 * @property ImageTranslate $currentTranslateDb
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ImageTranslateForm extends AbstractTranslateForm implements ImagesProcessorInterface
{
    /**
     * @var UploadedFile
     */
    public $translatedImage;
    /**
     * @var mixed information about crop
     */
    public $cropInfo;
    /**
     * @var ImagesBlock associated with this model
     */
    private $imagesBlock;
    /**
     * @var Image associated instance
     */
    private $imageEntity;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'translatedImage',
                'cropInfo'
            ],
            self::SCENARIO_UPDATE => [
                'translatedImage',
                'cropInfo'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        //TODO: makes validators
        return [
            [['translatedFile'], 'file'],
            ['cropInfo', 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getViewName()
    {
        return CommonModule::getInstance()->yicmsLocation . '/Common/Views/images/image-translate';
    }

    /**
     * Sets ImagesBlock
     * @param ImagesBlock $imagesBlock
     */
    public function setImageBlock(ImagesBlock $imagesBlock)
    {
        $this->imagesBlock = $imagesBlock;
    }

    /**
     * Sets Image
     * @param Image $imageEntity
     */
    public function setImageEntity(Image $imageEntity)
    {
        $this->imageEntity = $imageEntity;
    }

    /**
     * Image getter
     * @return Image
     */
    public function getImageEntity()
    {
        return $this->imageEntity;
    }

    /**
     * @inheritdoc
     */
    public function getImagesBlock()
    {
        return $this->imagesBlock;
    }

    /**
     * @inheritdoc
     */
    public function getCropInfo()
    {
        return $this->cropInfo;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->imageEntity->getPath($this->language);
    }

    /**
     * @inheritdoc
     */
    public function getFileName()
    {
        return $this->currentTranslateDb->system_name;
    }

    /**
     * @inheritdoc
     */
    public function getKey()
    {
        return $this->language->id . '-' . $this->imagesBlock->id;
    }

    /**
     * Proxies src method of image to this translate form
     * @return bool|string
     */
    public function getSrc()
    {
        return $this->getImageEntity()->getSrc($this->getLanguage());
    }

    /**
     * @inheritdoc
     */
    public function getCurrentTranslateDb()
    {
        if ($this->currentTranslateDb) return $this->currentTranslateDb;

        $this->currentTranslateDb = ImageTranslate::find()->where([
            'common_image_id' =>  $this->imageEntity->id,
            'common_language_id' => $this->language->id,
        ])->one();

        if (!$this->currentTranslateDb)
            $this->createTranslateDb();

        return $this->currentTranslateDb;
    }

    /**
     * @inheritdoc
     */
    protected function createTranslateDb()
    {
        $this->currentTranslateDb = new ImageTranslate();
        $this->currentTranslateDb->common_language_id = $this->language->id;
        $this->currentTranslateDb->common_image_id = $this->imageEntity->id;
        $this->currentTranslateDb->system_name = null;
        $this->currentTranslateDb->original_name = null;
        $this->currentTranslateDb->size = null;
        $this->currentTranslateDb->type = null;
        $this->currentTranslateDb->editable = true;
        $this->currentTranslateDb->visible = true;

        return $this->currentTranslateDb->save();
    }
}
