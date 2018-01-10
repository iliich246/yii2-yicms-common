<?php

namespace Iliich246\YicmsCommon\Images;

/**
 * Interface ImagesInterface
 *
 * This interface must implement any class, that must has images functionality.
 * All that objects must have ability to give two variables for correct work with images functionality:
 *
 * Variable templateImageReference used for pointing on group of images, called images block templates.
 * Variable imageReference user for pointing on single image block.
 * @author iliich246 <iliich246@gmail.com>
 */
interface ImagesReferenceInterface
{
    /**
     * Returns imageTemplateReference
     * @return string
     */
    public function getImageTemplateReference();

    /**
     * Returns imageReference
     * @return string
     */
    public function getImageReference();
}
