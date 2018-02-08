<?php

namespace Iliich246\YicmsCommon\Images;

/**
 * Interface ImagesProcessorInterface
 *
 * This interface must implement any class that must work with image processors
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface ImagesProcessorInterface
{
    /**
     * Returns associated images block
     * @return ImagesBlock
     */
    public function getImagesBlock();

    /**
     * Return information about crop
     * @return array
     */
    public function getCropInfo();

    /**
     * Returns physical path to image
     * @return string
     */
    public function getPath();

    /**
     * Return name of image file
     * @return string
     */
    public function getFileName();
}
