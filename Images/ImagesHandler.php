<?php

namespace Iliich246\YicmsCommon\Images;

use Iliich246\YicmsCommon\Annotations\AnnotatorFileInterface;
use Iliich246\YicmsCommon\Base\AbstractHandler;
use Iliich246\YicmsCommon\Base\NonexistentInterface;

/**
 * Class ImagesHandler
 *
 * @property ImagesReferenceInterface|NonexistentInterface|AnnotatorFileInterface $aggregator
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ImagesHandler extends AbstractHandler
{
    /**
     * FilesHandler constructor.
     * @param ImagesReferenceInterface|NonexistentInterface $aggregator
     */
    public function __construct(ImagesReferenceInterface $aggregator)
    {
        $this->aggregator = $aggregator;
    }

    /**
     * Returns instance of image block template by name
     * @param $name
     * @param null $variation
     * @return bool|ImagesBlock|object
     */
    public function getImageBlock($name, $variation = null)
    {
        if ($this->aggregator->isNonexistent()) {
            $nonexistentImageBlock = new ImagesBlock();
            $nonexistentImageBlock->setNonexistent();
            $nonexistentImageBlock->setNonexistentName($name);
            return $nonexistentImageBlock;
        }

        return $this->getOrSet($name, function() use($name, $variation) {
            if ($this->aggregator instanceof AnnotatorFileInterface) {
                if (!$this->aggregator->isAnnotationActive())
                    return ImagesBlock::getInstance(
                        $this->aggregator->getImageTemplateReference(),
                        $name,
                        $this->aggregator->getImageReference(),
                        $variation
                    );

                /** @var ImagesBlock $className */
                $className = $this->aggregator->getAnnotationFileNamespace() . '\\' .
                    $this->aggregator->getAnnotationFileName() . '\\Images\\' .
                    ucfirst(mb_strtolower($name)) . 'ImageBlock';

                if (class_exists($className))
                    $imagesBlock = $className::getInstance(
                        $this->aggregator->getImageTemplateReference(),
                        $name,
                        $this->aggregator->getImageReference(),
                        $variation
                    );
                else
                    $imagesBlock = ImagesBlock::getInstance(
                        $this->aggregator->getImageTemplateReference(),
                        $name,
                        $this->aggregator->getImageReference(),
                        $variation
                    );

                $imagesBlock->setParentFileAnnotator($this->aggregator);

                return $imagesBlock;
            }

            return ImagesBlock::getInstance(
                $this->aggregator->getImageTemplateReference(),
                $name,
                $this->aggregator->getImageReference(),
                $variation
            );
        });
    }

    /**
     * Returns true if aggregator has image block with name
     * @param $name
     * @param null $variation
     * @return bool
     */
    public function isImageBlock($name, $variation = null)
    {
        if ($this->aggregator->isNonexistent()) return false;

        if (!$this->aggregator->isAnnotationActive())
            return ImagesBlock::isTemplate($this->aggregator->getImageTemplateReference(), $name, $variation);

        /** @var ImagesBlock $className */
        $className = $this->aggregator->getAnnotationFileNamespace() . '\\' .
            $this->aggregator->getAnnotationFileName() . '\\Images\\' .
            ucfirst(mb_strtolower($name)) . 'ImageBlock';

        if (class_exists($className))
            return $className::isTemplate($this->aggregator->getImageTemplateReference(), $name, $variation);

        return ImagesBlock::isTemplate($this->aggregator->getImageTemplateReference(), $name, $variation);
    }
}
