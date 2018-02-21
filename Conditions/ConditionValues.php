<?php

namespace Iliich246\YicmsCommon\Conditions;

use yii\db\ActiveRecord;

/**
 * Class ConditionValues
 *
 * @property integer $id
 * @property integer $common_condition_template_id
 * @property string $value_name
 * @property string $is_default
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionValues extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_conditions_values}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['value_name', 'string', 'max' => '255'],
            ['is_default', 'boolean'],
            [
                ['common_condition_template_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ConditionTemplate::className(), 'targetAttribute' => ['common_condition_template_id' => 'id']
            ]
        ];
    }
}
