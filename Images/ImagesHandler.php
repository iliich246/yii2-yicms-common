<?php

namespace Iliich246\YicmsCommon\Images;

use Iliich246\YicmsCommon\Annotations\AnnotatorFileInterface;
use Iliich246\YicmsCommon\Base\AbstractHandler;
use Iliich246\YicmsCommon\Base\NonexistentInterface;

/**
 * Class ImagesHandler
 *
 * @property ImagesReferenceInterface|NonexistentInterface $aggregator
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
                /** @var ImagesBlock $ss */
                $ss = new $className;
                \Yii::error(print_r($ss::getInstance(                        $this->aggregator->getImageTemplateReference(),
                    $name,
                    $this->aggregator->getImageReference()) ,true));

                if (class_exists($className))
                    $imagesBlock = $className::getInstance(
                        $this->aggregator->getImageTemplateReference(),
                        $name,
                        $this->aggregator->getImageReference()
                    );
//                else
//                    $imagesBlock = ImagesBlock::getInstance(
//                        $this->aggregator->getImageTemplateReference(),
//                        $name,
//                        $this->aggregator->getImageReference()
//                    );

                //throw new \yii\base\Exception('There');

                $imagesBlock->setParentFileAnnotator($this->aggregator);

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

        return ImagesBlock::isTemplate($this->aggregator->getImageTemplateReference(), $name);
    }
}
