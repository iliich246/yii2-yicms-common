<?php

namespace Iliich246\YicmsCommon\Images;

use Yii;
use yii\helpers\Json;
use yii\imagine\Image;
use yii\base\Component;
use yii\helpers\FileHelper;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\CommonException;

/**
 * Class CropProcessor
 *
 * This class makes operations for create cropped images
 * Works with cropper.js https://github.com/fengyuanchen/cropper
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class CropProcessor extends Component
{
    /** @var ImagesProcessorInterface instance */
    private $imageEntity;
    /**
     * x data from crop
     * @var int|null
     */
    private $cropX = null;
    /**
     * y data form crop
     * @var int|null
     */
    private $cropY = null;
    /**
     * crop width data from crop
     * @var int|null
     */
    private $cropWidth = null;
    /**
     * crop height data form crop
     * @var int|null
     */
    private $cropHeight = null;
    /**
     * rotate data form crop
     * @var int|null
     */
    private $cropRotate = null;
    /**
     * crop scale x data from crop
     * @var int|null
     */
    private $cropScaleX = null;
    /**
     * crop scale y data form crop
     * @var int|null
     */
    private $cropScaleY = null;

    /**
     * Creates cropped images for send image entity
     * @param ImagesProcessorInterface $imageEntity
     * @param bool|false $oldSystemName
     * @return bool
     * @throws CommonException
     */
    public static function handle(ImagesProcessorInterface $imageEntity, $oldSystemName = false)
    {
        if ($imageEntity->getImagesBlock()->crop_type == ImagesBlock::NO_CROP) return false;

        $processor = new self();
        $processor->imageEntity = $imageEntity;

        if (!$processor->handleCropArray($imageEntity->getCropInfo())) return false;

        if ($oldSystemName) $processor->deleteOldImages($oldSystemName);

        $processor->crop();

        ThumbnailsProcessor::handleCrop($processor->imageEntity, $oldSystemName);

        return true;
    }

    /**
     * Delete old cropped image on update
     * @param $oldSystemName
     */
    private function deleteOldImages($oldSystemName)
    {
        if (file_exists(CommonModule::getInstance()->imagesCropPath . $oldSystemName) &&
            !is_dir(CommonModule::getInstance()->imagesCropPath . $oldSystemName))
            unlink(CommonModule::getInstance()->imagesCropPath . $oldSystemName);
    }

    /**
     * Method fill information about crop and checks it
     * @param $cropInfo
     * @return bool
     * @throws CommonException
     */
    private function handleCropArray($cropInfo)
    {
        $cropArray = Json::decode($cropInfo);

        if (isset($cropArray['x']))
            $this->cropX = (int)$cropArray['x'];

        if (isset($cropArray['y']))
            $this->cropY = (int)$cropArray['y'];

        if (isset($cropArray['width']))
            $this->cropWidth = (int)$cropArray['width'];

        if (isset($cropArray['height']))
            $this->cropHeight = (int)$cropArray['height'];

        if (isset($cropArray['rotate']))
            $this->cropRotate = (int)$cropArray['rotate'];

        if (isset($cropArray['scaleX']))
            $this->cropScaleX = (int)$cropArray['scaleX'];

        if (isset($cropArray['scaleY']))
            $this->cropScaleY = (int)$cropArray['scaleY'];

        if (!is_null($this->cropX) ||
            !is_null($this->cropY) ||
            !is_null($this->cropWidth) ||
            !is_null($this->cropHeight) ||
            !is_null($this->cropRotate) ||
            !is_null($this->cropScaleX) ||
            !is_null($this->cropScaleY)
        )
            return true;

        Yii::warning("Wrong crop array", __METHOD__);

        if (defined('YICMS_STRICT')) {
            throw new CommonException(
                "YICMS_STRICT_MODE:
                Wrong crop array"
            );
        }

        return false;
    }

    /**
     * Crop method crop image and save in crop directory
     * @return bool
     */
    private function crop()
    {
        $path = $this->imageEntity->getPath();

        if (!$path) return false;

        if (!is_dir(CommonModule::getInstance()->imagesCropPath))
            FileHelper::createDirectory(CommonModule::getInstance()->imagesCropPath);

        $image = Image::getImagine()->open($this->imageEntity->getPath());

        $cropSize  = new Box($this->cropWidth, $this->cropHeight);
        $cropStart = new Point($this->cropX, $this->cropY);
        $newSize   = new Box($this->imageEntity->getImagesBlock()->crop_width,
                             $this->imageEntity->getImagesBlock()->crop_height);

        $image->crop($cropStart, $cropSize)
              ->resize($newSize)
              ->save(CommonModule::getInstance()->imagesCropPath .
                     $this->imageEntity->getFileName());

        return true;
    }
}
