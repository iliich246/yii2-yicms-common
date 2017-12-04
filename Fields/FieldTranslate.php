<?php

namespace Iliich246\YicmsCommon\Fields;

use yii\db\ActiveRecord;

/**
 * Class FieldTranslate
 *
 * @
 *
 * @package Iliich246\YicmsCommon\Fields
 */
class FieldTranslate extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_field_translates}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {

    }
}
