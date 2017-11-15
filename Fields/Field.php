<?php

namespace Iliich246\YicmsCommon\Fields;

use yii\db\ActiveRecord;

/**
 * Class Field
 * @property integer $id
 * @property integer $common_fields_template_id
 * @property integer $field_reference
 * @property integer $editable
 * @property integer $visible
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Field extends ActiveRecord
{
    const TYPE_INPUT = 0;
    const TYPE_TEXT = 1;
    const TYPE_REDACTOR = 2;

    /** @var FieldTemplate  */
    private $template;

    private $fieldReference;

    private $fieldTemplateReference;



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_fields_represents}}';
    }

    
}
