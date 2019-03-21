<?php

namespace Iliich246\YicmsCommon\Fields;

use Iliich246\YicmsCommon\Annotations\AnnotatorFileInterface;
use Iliich246\YicmsCommon\Base\AbstractHandler;
use Iliich246\YicmsCommon\Base\NonexistentInterface;

/**
 * Class FieldsHandler
 *
 * Object of this class must aggregate any object, that must implement fields functionality.
 *
 * @property FieldReferenceInterface|NonexistentInterface $aggregator
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldsHandler extends AbstractHandler
{
    /**
     * FieldsHandler constructor.
     * @param FieldReferenceInterface|NonexistentInterface $aggregator
     */
    public function __construct(FieldReferenceInterface $aggregator)
    {
        $this->aggregator = $aggregator;
    }

    /**
     * Return instance of field by name
     * @param $name
     * @return bool|object
     */
    public function getField($name)
    {
        if ($this->aggregator->isNonexistent()) {
            $nonexistentField = new Field();
            $nonexistentField->setNonexistent();
            $nonexistentField->setNonexistentName($name);

            return $nonexistentField;
        }

        return $this->getOrSet($name, function() use($name) {
            if ($this->aggregator instanceof AnnotatorFileInterface) {

                /** @var Field $className */
                $className = $this->aggregator->getAnnotationFileNamespace() . '\\' .
                    $this->aggregator->getAnnotationFileName() . '\\Fields\\' .
                    ucfirst(mb_strtolower($name));

                if (class_exists($className))
                    return $className::getInstance(
                        $this->aggregator->getFieldTemplateReference(),
                        $this->aggregator->getFieldReference(),
                        $name
                    );
            }

            return Field::getInstance(
                $this->aggregator->getFieldTemplateReference(),
                $this->aggregator->getFieldReference(),
                $name
            );
        });
    }

    /**
     * Returns true if aggregator has field with name
     * @param $name
     * @return bool
     */
    public function isField($name)
    {
        if ($this->aggregator->isNonexistent()) return false;

        return FieldTemplate::isTemplate($this->aggregator->getFieldTemplateReference(), $name);
    }
}
