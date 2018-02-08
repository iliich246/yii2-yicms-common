<?php

namespace Iliich246\YicmsCommon\Images;

use yii\imagine\Image;
use yii\base\Component;
use yii\helpers\FileHelper;
use Imagine\Image\Box;
use Iliich246\YicmsCommon\CommonModule;

/**
 * Class ThumbnailsProcessor
 *
 * This class makes operations for create thumbnails for images
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ThumbnailsProcessor extends Component
{
    /**
     * @var ImagesProcessorInterface instance
     */
    private $imageEntity;

    /**
     * Create thumbnails for send image entity
     * @param ImagesProcessorInterface $imageEntity
     * @param $oldSystemName
     */
    public static function handle(ImagesProcessorInterface $imageEntity, $oldSystemName = false)
    {
        $processor = new self();
        $processor->imageEntity = $imageEntity;

        if ($oldSystemName) $processor->deleteOldThumbnails($oldSystemName,
            CommonModule::getInstance()->imagesThumbnailsPath);

        $processor->makeThumbs(CommonModule::getInstance()->imagesThumbnailsPath);
    }

    /**
     * Create thumbnails for crops of send image entity
     * @param ImagesProcessorInterface $imageEntity
     * @param bool|false $oldSystemName
     * @return bool
     */
    public static function handleCrop(ImagesProcessorInterface $imageEntity, $oldSystemName = false)
    {
        if ($imageEntity->getImagesBlock()->crop_type == ImagesBlock::NO_CROP) return;

        $processor = new self();
        $processor->imageEntity = $imageEntity;

        if ($oldSystemName) $processor->deleteOldThumbnails($oldSystemName,
            CommonModule::getInstance()->imagesCropPath);

        $processor->makeThumbs(CommonModule::getInstance()->imagesCropPath);
    }

    /**
     * Delete old thumbnails on image update
     * @param $oldSystemName
     * @param $imagesPath
     * @return bool
     */
    private function deleteOldThumbnails($oldSystemName, $imagesPath)
    {
        $imagesBlock = $this->imageEntity->getImagesBlock();

        /** @var ImagesThumbnails[] $thumbnailsList */
        $thumbnailsList = ImagesThumbnails::find()
            ->where([
                'common_images_templates_id' => $imagesBlock->id,
            ])->all();

        if (!$thumbnailsList) return false;

        foreach($thumbnailsList as $thumbnail) {
            $fileName = $thumbnail->program_name . '_' . $oldSystemName;

            if (file_exists($imagesPath . $fileName) &&
                !is_dir($imagesPath . $fileName))
                unlink($imagesPath . $fileName);
        }

        return true;
    }


    /**
     * Created all needed thumbnails
     * @param $savePath
     * @return bool
     */
    private function makeThumbs($savePath)
    {
        $imagesBlock = $this->imageEntity->getImagesBlock();

        /** @var ImagesThumbnails[] $thumbnailsList */
        $thumbnailsList = ImagesThumbnails::find()
                            ->where([
                                'common_images_templates_id' => $imagesBlock->id,
                            ])->all();

        if (!$thumbnailsList) return false;

        if (!is_dir($savePath))
            FileHelper::createDirectory($savePath);

        $image = Image::getImagine()->open($this->imageEntity->getPath());

        foreach($thumbnailsList as $thumbnail) {
            $sizeBox = $image->getSize();
            $resizeBox = new Box($sizeBox->getWidth() / $thumbnail->divider,
                                 $sizeBox->getHeight() / $thumbnail->divider);

            $fileName = $thumbnail->program_name . '_' . $this->imageEntity->getFileName();

            $image->resize($resizeBox)
                  ->save($savePath . $fileName, [
                      'quality' => $thumbnail->quality
                  ]);
        }

        return true;
    }
}
