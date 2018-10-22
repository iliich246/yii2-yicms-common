<?php

namespace Iliich246\YicmsCommon\Images;

use Iliich246\YicmsCommon\Base\AbstractHandler;

/**
 * Class ImagesHandler
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ImagesHandler extends AbstractHandler
{
    /**
     * FilesHandler constructor.
     * @param ImagesReferenceInterface $aggregator
     */
    public function __construct(ImagesReferenceInterface $aggregator)
    {
        $this->aggregator = $aggregator;
    }

    /**
     * Returns instance of image block template by name
     * @param $name
     * @return bool|object
     */
    public function getImageBlock($name)
    {
        return $this->getOrSet($name, function() use($name) {
            return ImagesBlock::getInstance(
                $this->aggregator->getImageTemplateReference(),
                $name,
                $this->aggregator->getImageReference()
            );
        });
    }
}
