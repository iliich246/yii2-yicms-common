<?php

namespace Iliich246\YicmsCommon\Images;

use Iliich246\YicmsCommon\CommonModule;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Yii;
use yii\helpers\Json;
use yii\imagine\Image;
use yii\base\Component;
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
    /**
     * @var ImagesProcessorInterface instance
     */
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
     * @return bool
     * @throws \yii\base\Exception
     */
    public static function handle(ImagesProcessorInterface $imageEntity)
    {
        if ($imageEntity->getImagesBlock()->crop_type == ImagesBlock::NO_CROP) return false;

        $processor = new self();
        $processor->imageEntity = $imageEntity;

        if (!$processor->handleCropArray($imageEntity->getCropInfo())) return false;

        $processor->crop();

        return true;
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

    private function crop()
    {
        $path = $this->imageEntity->getPath();

        if (!$path) return false;

        $imagine = Image::getImagine();
        $image = Image::getImagine()->open($this->imageEntity->getPath());

        $palette = new RGB();
        $color = $palette->color('#3A7');
        $size  = new \Imagine\Image\Box(400, 300);
        $img = $imagine->create($size, $color);



        $point = new Point(100,100);
        $box = new Box(200,200);

        $image->crop($point, $box);

        $img->paste($image, $point);
        $img->save(CommonModule::getInstance()->imagesPath . '/crp.jpg');


        //throw new \yii\base\Exception(print_r($this, true));
    }
}
/*
private function crop(ManipulatorInterface $image)
{
    $cropArray = Json::decode($this->model->getCropInfo());

    $cropWidth = (int)$cropArray['width'];
    $cropHeight = (int)$cropArray['height'];
    $cropX = (int)$cropArray['x'];
    $cropY = (int)$cropArray['y'];
    //$cropRotate = (int)$cropArray['rotate']; //todo: handle rotate

    $newSizeThumb = new Box($this->cropWidth, $this->cropHeight);
    $cropStart = new Point($cropX, $cropY);
    $cropSize = new Box($cropWidth, $cropHeight);

    return $image->crop($cropStart, $cropSize)
        ->resize($newSizeThumb);
}
*/