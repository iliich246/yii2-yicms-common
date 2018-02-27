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
    const SCENARIO_CREATE = 0x01;
    const SCENARIO_UPDATE = 0x02;

    /**
     * @var ConditionTemplate instance associated with this object
     */
    private $conditionTemplate;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_conditions_values}}';
    }

    /**
     * ConditionTemplate setter
     * @param ConditionTemplate $conditionTemplate
     */
    public function setConditionTemplate(ConditionTemplate $conditionTemplate)
    {
        $this->conditionTemplate = $conditionTemplate;
    }


    /**
     * Fetch ConditionTemplate from db
     * @return ConditionTemplate
     */
    public function getConditionTemplate()
    {
        return ConditionTemplate::findOne($this->common_condition_template_id);
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
