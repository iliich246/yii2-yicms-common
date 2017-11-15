<?php

namespace Iliich246\YicmsCommon\Fields;

use Iliich246\YicmsCommon\Base\AbstractTemplate;

/**
 * Class FieldTemplate
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldTemplate extends AbstractTemplate
{
    private static $buffer = [];

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
    protected static function getTemplateReferenceName()
    {
        return 'field_template_reference';
    }
}
