<?php

namespace Iliich246\YicmsCommon\Fields;

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
            return Field::getInstance(
                $this->aggregator->getFieldTemplateReference(),
                $this->aggregator->getFieldReference(),
                $name
            );
        });
    }
}
