<?php

namespace Iliich246\YicmsCommon\Fields;

use yii\db\ActiveRecord;

/**
 * Class FieldsValidatorDb
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldsValidatorDb extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_field_validators}}';
    }
}
