<?php

namespace Iliich246\YicmsCommon\Files;

use Iliich246\YicmsCommon\Base\AbstractHandler;
use Iliich246\YicmsCommon\Base\NonexistentInterface;

/**
 * Class FilesHandler
 *
 * @property FilesReferenceInterface|NonexistentInterface $aggregator
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FilesHandler extends AbstractHandler
{
    /**
     * FilesHandler constructor.
     * @param FilesReferenceInterface|NonexistentInterface $aggregator
     */
    public function __construct(FilesReferenceInterface $aggregator)
    {
        $this->aggregator = $aggregator;
    }

    /**
     * Returns instance of file block template by name
     * @param $name
     * @return bool|object
     */
    public function getFileBlock($name)
    {
        if ($this->aggregator->isNonexistent()) {
            $nonexistentFileBlock = new FilesBlock();
            $nonexistentFileBlock->setNonexistent();
            $nonexistentFileBlock->setNonexistentName($name);
            return $nonexistentFileBlock;
        }
        
        return $this->getOrSet($name, function() use($name) {
            return FilesBlock::getInstance(
                $this->aggregator->getFileTemplateReference(),
                $name,
                $this->aggregator->getFileReference()
            );
        });
    }
}
