<?php

namespace Iliich246\YicmsCommon\Tests\_testEssences\abstractTemplate;

use Iliich246\YicmsCommon\Base\AbstractTemplate;

/**
 * Class TestTemplate
 *
 * This class is only for test AbstractHandler
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class TestTemplate extends AbstractTemplate
{
    private static $buffer = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%test_templates}}';
    }

    /**
     * @inheritdoc
     */
    protected static function getBuffer()
    {
        return self::$buffer;
    }

    protected static function setBuffer($buffer)
    {
        self::$buffer = $buffer;
    }

    /**
     * @inheritdoc
     */
    protected static function getTemplateReferenceName()
    {
        return 'test_template_reference';
    }
}
