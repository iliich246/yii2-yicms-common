<?php

namespace Iliich246\YicmsCommon\Files;

/**
 * Interface FilesReferenceInterface
 *
 * This interface must implement any class, that must has files functionality.
 * All that objects must have ability to give two variables for correct work with files functionality:
 *
 * Variable fileTemplateReference used for pointing on group of files, called files block templates.
 * Variable fileReference user for pointing on single file block.
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface FilesReferenceInterface
{
    /**
     * Returns fileTemplateReference
     * @return string
     */
    public function getFileTemplateReference();

    /**
     * Returns fileReference
     * @return string
     */
    public function getFileReference();
}
