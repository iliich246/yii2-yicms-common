<?php

namespace Iliich246\YicmsCommon\Base;

/**
 * Class AbstractEntityBlock
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractEntityBlock extends AbstractTemplate
{
    public function getIterator()
    {

    }

    public function getEntity()
    {

    }

    public function getEntities()
    {

    }

    public function countEntities()
    {

    }

    public function delete()
    {

    }

    /**
     * @inheritdoc
     */
    public static function generateTemplateReference()
    {
        return parent::generateTemplateReference();
    }
}
