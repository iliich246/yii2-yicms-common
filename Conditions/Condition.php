<?php

namespace Iliich246\YicmsCommon\Conditions;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class Condition
 *
 * @property integer $id
 * @property integer $common_condition_template_id
 * @property string $condition_reference
 * @property integer $common_value_id
 * @property integer $editable
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Condition extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_conditions}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['condition_reference', 'string', 'max' => '255'],
            ['editable', 'boolean'],
            [
                ['common_value_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ConditionValues::className(), 'targetAttribute' => ['common_value_id' => 'id']
            ],
            [
                ['common_condition_template_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ConditionTemplate::className(), 'targetAttribute' => ['common_condition_template_id' => 'id']
            ],
        ];
    }
}
