<?php

namespace Iliich246\YicmsCommon\Conditions;

use Iliich246\YicmsCommon\Annotations\AnnotatorFileInterface;
use Iliich246\YicmsCommon\Base\AbstractHandler;
use Iliich246\YicmsCommon\Base\NonexistentInterface;

/**
 * Class ConditionsHandler
 *
 * Object of this class must aggregate any object, that must implement conditions functionality.
 *
 * @property ConditionsReferenceInterface|NonexistentInterface $aggregator
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionsHandler extends AbstractHandler
{
    /**
     * ConditionsHandler constructor.
     * @param ConditionsReferenceInterface|NonexistentInterface $aggregator
     */
    public function __construct(ConditionsReferenceInterface $aggregator)
    {
        $this->aggregator = $aggregator;
    }

    /**
     * Return instance of condition by name
     * @param $name
     * @return Condition|object
     */
    public function getCondition($name)
    {
        if ($this->aggregator->isNonexistent()) {
            $nonexistentImageBlock = new Condition();
            $nonexistentImageBlock->setNonexistent();
            $nonexistentImageBlock->setNonexistentName($name);

            return $nonexistentImageBlock;
        }

        return $this->getOrSet($name, function() use($name) {
            if ($this->aggregator instanceof AnnotatorFileInterface) {

                /** @var Condition $className */
                $className = $this->aggregator->getAnnotationFileNamespace() . '\\' .
                    $this->aggregator->getAnnotationFileName() . '\\Conditions\\' .
                    ucfirst(mb_strtolower($name));

                if (class_exists($className))
                    return $className::getInstance(
                        $this->aggregator->getConditionTemplateReference(),
                        $this->aggregator->getConditionReference(),
                        $name
                    );
            }

            return Condition::getInstance(
                $this->aggregator->getConditionTemplateReference(),
                $this->aggregator->getConditionReference(),
                $name
            );
        });
    }

    /**
     * Returns true if aggregator has condition with name
     * @param $name
     * @return bool
     */
    public function isCondition($name)
    {
        if ($this->aggregator->isNonexistent()) return false;

        return ConditionTemplate::isTemplate($this->aggregator->getConditionTemplateReference(), $name);
    }
}
