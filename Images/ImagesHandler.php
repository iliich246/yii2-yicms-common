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
     * @return ImagesBlock|bool
     */
    public function getImageBlock($name)
    {
        if ($this->aggregator->isNonexistent()) {
            $nonexistentImageBlock = new ImagesBlock();
            $nonexistentImageBlock->setNonexistent();
            $nonexistentImageBlock->setNonexistentName($name);
            return $nonexistentImageBlock;
        }

        return $this->getOrSet($name, function() use($name) {
            if ($this->aggregator instanceof AnnotatorFileInterface) {
                if (!$this->aggregator->isAnnotationActive())
                    return ImagesBlock::getInstance(
                        $this->aggregator->getImageTemplateReference(),
                        $name,
                        $this->aggregator->getImageReference()
                    );

                /** @var ImagesBlock $className */
                $className = $this->aggregator->getAnnotationFileNamespace() . '\\' .
                    $this->aggregator->getAnnotationFileName() . '\\Images\\' .
                    ucfirst(mb_strtolower($name)) . 'ImageBlock';

                if (class_exists($className))
                    $imagesBlock = $className::getInstance(
                        $this->aggregator->getImageTemplateReference(),
                        $name,
                        $this->aggregator->getImageReference()
                    );
                else
                    $imagesBlock = ImagesBlock::getInstance(
                        $this->aggregator->getImageTemplateReference(),
                        $name,
                        $this->aggregator->getImageReference()
                    );

                $imagesBlock->setParentFileAnnotator($this->aggregator);

                \Yii::error(print_r([
                    'name' => $name,
                    'imageTemplateReference' => $this->aggregator->getImageTemplateReference(),
                    'imageTemplate' => $this->aggregator->getImageReference(),
                    'imageUnic' => $imagesBlock->unic,
                    'image' => $imagesBlock->image_template_reference
                ], true));

                return $imagesBlock;
            }

            return ImagesBlock::getInstance(
                $this->aggregator->getImageTemplateReference(),
                $name,
                $this->aggregator->getImageReference()
            );
        });
    }

    /**
     * Returns true if aggregator has image block with name
     * @param $name
     * @return bool
     */
    public function isImageBlock($name)
    {
        if ($this->aggregator->isNonexistent()) return false;

        if (!$this->aggregator->isAnnotationActive())
            return ImagesBlock::isTemplate($this->aggregator->getImageTemplateReference(), $name);

        /** @var ImagesBlock $className */
        $className = $this->aggregator->getAnnotationFileNamespace() . '\\' .
            $this->aggregator->getAnnotationFileName() . '\\Images\\' .
            ucfirst(mb_strtolower($name)) . 'ImageBlock';

        if (class_exists($className))
            return $className::isTemplate($this->aggregator->getImageTemplateReference(), $name);

        return ImagesBlock::isTemplate($this->aggregator->getImageTemplateReference(), $name);
    }
}
