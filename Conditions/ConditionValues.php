<?php

namespace Iliich246\YicmsCommon\Conditions;

use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\Base\SortOrderTrait;
use Iliich246\YicmsCommon\Base\SortOrderInterface;

/**
 * Class ConditionValues
 *
 * @property integer $id
 * @property integer $common_condition_template_id
 * @property string $value_name
 * @property integer $condition_value_order
 * @property string $is_default
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionValues extends ActiveRecord implements SortOrderInterface
{
    use SortOrderTrait;

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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'value_name' => 'Value name (will be converted in upper case as constant)'
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'value_name', 'is_default',
            ],
            self::SCENARIO_UPDATE => [
                'value_name', 'is_default',
            ]
        ];
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
            ],
            ['value_name', 'validateConditionValueName'],
        ];
    }

    /**
     * Validates the condition value name.
     * This method checks, that for group of condition value name is unique.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateConditionValueName($attribute, $params)
    {
        if (!$this->hasErrors()) {

            $query = self::find()->where([
                'common_condition_template_id' => $this->common_condition_template_id,
                'value_name'                   => $this->value_name,
            ]);

            if ($this->scenario == self::SCENARIO_UPDATE)
                $query->andWhere(['not in', 'value_name', $this->getOldAttribute('value_name')]);

            $count = $query->all();

            if ($count)$this->addError($attribute, 'Field with same name already existed');
        }
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->scenario == self::SCENARIO_CREATE) {
            $this->common_condition_template_id = $this->conditionTemplate->id;
            $this->condition_value_order = $this->maxOrder();
        }

        $this->value_name = strtoupper($this->value_name);

        return parent::save($runValidation, $attributeNames);
    }

    /**
     * @inheritdoc
     */
    public function getOrderQuery()
    {
        return self::find()->where([
            'common_condition_template_id' => $this->common_condition_template_id,
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function getOrderFieldName()
    {
        return 'condition_value_order';
    }

    /**
     * @inheritdoc
     */
    public function getOrderValue()
    {
        return $this->condition_value_order;
    }

    /**
     * @inheritdoc
     */
    public function setOrderValue($value)
    {
        $this->condition_value_order = $value;
    }

    /**
     * @inheritdoc
     */
    public function configToChangeOfOrder()
    {

    }

    /**
     * @inheritdoc
     */
    public function getOrderAble()
    {
        return $this;
    }
}
