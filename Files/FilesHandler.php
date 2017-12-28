<?php

namespace Iliich246\YicmsCommon\Files;

use Iliich246\YicmsCommon\Base\AbstractHandler;

/**
 * Class FilesHandler
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FilesHandler extends AbstractHandler
{
    /**
     * FilesHandler constructor.
     * @param FilesReferenceInterface $aggregator
     */
    public function __construct(FilesReferenceInterface $aggregator)
    {
        $this->aggregator = $aggregator;
    }

    /**
     * Return instance of file block by name
     * @param $name
     * @return bool|object
     */
    public function getFileBlock($name)
    {
        return $this->getOrSet($name, function() use($name) {
//            return FilesBlock::getInstance(
//                $this->aggregator->getFieldTemplateReference(),
//                $this->aggregator->getFieldReference(),
//                $name
//            );
        });
    }
}
