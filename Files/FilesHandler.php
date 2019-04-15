<?php

namespace Iliich246\YicmsCommon\Files;

use Iliich246\YicmsCommon\Annotations\AnnotatorFileInterface;
use Iliich246\YicmsCommon\Base\AbstractHandler;
use Iliich246\YicmsCommon\Base\NonexistentInterface;

/**
 * Class FilesHandler
 *
 * @property FilesReferenceInterface|NonexistentInterface|AnnotatorFileInterface $aggregator
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
     * @param null $variation
     * @return bool|FilesBlock|object
     */
    public function getFileBlock($name, $variation = null)
    {
        if ($this->aggregator->isNonexistent()) {
            $nonexistentFileBlock = new FilesBlock();
            $nonexistentFileBlock->setNonexistent();
            $nonexistentFileBlock->setNonexistentName($name);
            return $nonexistentFileBlock;
        }

        return $this->getOrSet($name, function() use($name, $variation) {
            if ($this->aggregator instanceof AnnotatorFileInterface) {
                if (!$this->aggregator->isAnnotationActive())
                    return FilesBlock::getInstance(
                        $this->aggregator->getFileTemplateReference(),
                        $name,
                        $this->aggregator->getFileReference(),
                        $variation
                    );

                /** @var FilesBlock $className */
                $className = $this->aggregator->getAnnotationFileNamespace() . '\\' .
                    $this->aggregator->getAnnotationFileName() . '\\Files\\' .
                    ucfirst(mb_strtolower($name)) . 'FileBlock';

                if (class_exists($className))
                    $filesBlock = $className::getInstance(
                        $this->aggregator->getFileTemplateReference(),
                        $name,
                        $this->aggregator->getFileReference(),
                        $variation
                    );
                else
                    $filesBlock = FilesBlock::getInstance(
                        $this->aggregator->getFileTemplateReference(),
                        $name,
                        $this->aggregator->getFileReference(),
                        $variation
                    );

                $filesBlock->setParentFileAnnotator($this->aggregator);

                return $filesBlock;
            }

            return FilesBlock::getInstance(
                $this->aggregator->getFileTemplateReference(),
                $name,
                $this->aggregator->getFileReference(),
                $variation
            );
        });
    }

    /**
     * Returns true if aggregator has file block with name
     * @param $name
     * @return bool
     */
    public function isFileBlock($name)
    {
        if ($this->aggregator->isNonexistent()) return false;

        if (!$this->aggregator->isAnnotationActive())
            return FilesBlock::isTemplate($this->aggregator->getFileTemplateReference(), $name);

        /** @var FilesBlock $className */
        $className = $this->aggregator->getAnnotationFileNamespace() . '\\' .
            $this->aggregator->getAnnotationFileName() . '\\Files\\' .
            ucfirst(mb_strtolower($name)) . 'FileBlock';

        if (class_exists($className))
            return $className::isTemplate($this->aggregator->getFileTemplateReference(), $name);

        return FilesBlock::isTemplate($this->aggregator->getFileTemplateReference(), $name);
    }
}
