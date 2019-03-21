<?php

namespace Iliich246\YicmsCommon\Files;

/**
 * Interface FilesInterface
 *
 * This interface must implement any class, that must has files functionality.
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface FilesInterface
{
    /**
     * @return FilesHandler object, that aggregated in object with files functionality.
     */
    public function getFileHandler();

    /**
     * This method must proxy FilesHandler method for work with him directly from aggregator.
     * @param $name
     * @return FilesBlock
     */
    public function getFileBlock($name);

    /**
     * Returns true if aggregator has file block with name
     * @param $name
     * @return mixed
     */
    public function isFileBlock($name);
}
