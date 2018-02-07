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
     * @return mixed
     */
    public function getCropInfo();

    /**
     * Returns physical path to image
     * @return mixed
     */
    public function getPath();
}
