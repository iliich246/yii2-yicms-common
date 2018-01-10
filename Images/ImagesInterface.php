<?php

namespace Iliich246\YicmsCommon\Images;

/**
 * Interface ImagesInterface
 *
 * This interface must implement any class, that must has images functionality.
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface ImagesInterface
{
    /**
     * @return ImagesHandler object, that aggregated in object with images functionality.
     */
    public function getImagesHandler();

    /**
     * This method must proxy ImagesHandler method for work with him directly from aggregator.
     * @param $name
     * @return ImagesBlock
     */
    public function getImageBlock($name);
}
