<?php

namespace Iliich246\YicmsCommon\Conditions;

use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\Languages\LanguagesDb;

/**
 * Class ConditionValueNames
 *
 * @property integer $id
 * @property integer $common_condition_value_id
 * @property integer $common_language_id
 * @property string $name
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionValueNames extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_condition_value_names}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string', 'max' => '255'],
            [
                ['common_language_id'], 'exist', 'skipOnError' => true,
                'targetClass' => LanguagesDb::className(), 'targetAttribute' => ['common_language_id' => 'id']
            ],
            [
                ['common_condition_value_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ConditionValues::className(), 'targetAttribute' => ['common_condition_value_id' => 'id']
            ],
        ];
    }
}
