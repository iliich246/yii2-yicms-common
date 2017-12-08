<?php

namespace Iliich246\YicmsCommon\Fields;

use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\Languages\LanguagesDb;

/**
 * Class FieldTranslate
 *
 * @property integer $id
 * @property integer $common_fields_represent_id
 * @property integer $common_language_id
 * @property string $value
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
        return [
            ['value', 'string'],
            [
                ['common_language_id'], 'exist', 'skipOnError' => true,
                'targetClass' => LanguagesDb::className(), 'targetAttribute' => ['common_language_id' => 'id']
            ],
            [
                ['common_fields_represent_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Field::className(), 'targetAttribute' => ['common_fields_represent_id' => 'id']
            ],
        ];
    }
}
