<?php

namespace Iliich246\YicmsCommon\Conditions;

use Iliich246\YicmsCommon\Base\AbstractTemplate;

/**
 * Class ConditionTemplate
 *
 * @property string $condition_template_reference
 * @property integer $type
 * @property integer $condition_order
 * @property bool $editable
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionTemplate extends AbstractTemplate
{
    const TYPE_CHECKBOX = 0;
    const TYPE_RADIO    = 1;
    const TYPE_SELECT   = 2;

    /** @inheritdoc */
    protected static $buffer = [];
    /** @var ConditionValues[] */
    private $values;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->editable = true;
        $this->type     = self::TYPE_CHECKBOX;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_conditions_templates}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['type'], 'integer'],
            [['editable'], 'boolean'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $prevScenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = array_merge($prevScenarios[self::SCENARIO_CREATE],
            ['type', 'editable']);
        $scenarios[self::SCENARIO_UPDATE] = array_merge($prevScenarios[self::SCENARIO_UPDATE],
            ['type', 'editable']);

        return $scenarios;
    }

    /**
     * Returns array of condition types
     * @return array|bool
     */
    public static function getTypes()
    {
        static $array = false;

        if ($array) return $array;

        $array = [
            self::TYPE_CHECKBOX => 'Check box type',
            self::TYPE_RADIO    => 'Radio group type',
            self::TYPE_SELECT   => 'Select dropdown type',
        ];

        return $array;
    }

    /**
     * Return name of condition type
     * @return string
     */
    public function getTypeName()
    {
        if (!isset(self::getTypes()[$this->type])) return 'Undefined';

        return self::getTypes()[$this->type];
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributes = null)
    {
        if ($this->scenario === self::SCENARIO_CREATE) {
            $this->condition_order = $this->maxOrder();
        }

        return parent::save($runValidation, $attributes);
    }

    /**
     * Returns true if this condition template has constraints
     * @return bool
     */
    public function isConstraints()
    {
        if (Condition::find()->where([
            'common_condition_template_id' => $this->id,
        ])->one()) return true;

        return false;
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $templateNames = ConditionsNamesTranslatesDb::find()->where([
            'common_condition_template_id' => $this->id,
        ])->all();

        foreach($templateNames as $templateName)
            $templateName->delete();

        $conditions = Condition::find()->where([
            'common_condition_template_id' => $this->id
        ])->all();

        foreach($conditions as $condition)
            $condition->delete();

        $conditionValues = ConditionValues::find()->where([
            'common_condition_template_id' => $this->id
        ])->all();

        foreach($conditionValues as $conditionValue)
            $conditionValue->delete();

        return parent::delete();
    }

    /**
     * Returns buffered list of values of template
     * @return ConditionValues[]
     */
    public function getValuesList()
    {
        if ($this->values) return $this->values;

        $this->values = ConditionValues::find()->where([
            'common_condition_template_id' => $this->id,
        ])->orderBy(['condition_value_order' =>SORT_ASC])
          ->indexBy('id')
          ->all();

        return $this->values;
    }

    /**
     * Returns id of default value
     * @return int|null
     */
    public function defaultValueId()
    {
        foreach($this->getValuesList() as $value) {
            if ($value->is_default) return $value->id;
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public static function generateTemplateReference()
    {
        return parent::generateTemplateReference();
    }

    /**
     * @inheritdoc
     */
    public function getOrderQuery()
    {
        return self::find()->where([
            'condition_template_reference' => $this->condition_template_reference,
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function getOrderFieldName()
    {
        return 'condition_order';
    }

    /**
     * @inheritdoc
     */
    public function getOrderValue()
    {
        return $this->condition_order;
    }

    /**
     * @inheritdoc
     */
    public function setOrderValue($value)
    {
        $this->condition_order = $value;
    }

    /**
     * @inheritdoc
     */
    public function configToChangeOfOrder()
    {
        //$this->scenario = self::SCENARIO_CHANGE_ORDER;
    }

    /**
     * @inheritdoc
     */
    public function getOrderAble()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected static function getTemplateReferenceName()
    {
        return 'condition_template_reference';
    }
}
