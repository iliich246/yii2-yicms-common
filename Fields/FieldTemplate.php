<?php

namespace Iliich246\YicmsCommon\Fields;

use Iliich246\YicmsCommon\Base\AbstractTemplate;

/**
 * Class FieldTemplate
 *
 * @property integer
 * @property integer type
 * @property bool is_main
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldTemplate extends AbstractTemplate
{
    /**
     * @inheritdoc
     */
    private static $buffer = [];

    /**
     * @inheritdoc
     */
    public static function getTemplateReference()
    {
        return parent::getTemplateReference();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_fields_templates}}';
    }

    /**
     * @inheritdoc
     */
    protected static function getBuffer()
    {
        return self::$buffer;
    }

    /**
     * @inheritdoc
     */
    protected static function setBuffer($buffer)
    {
        self::$buffer = $buffer;
    }

    /**
     * @inheritdoc
     */
    protected static function getTemplateReferenceName()
    {
        return 'field_template_reference';
    }
}
