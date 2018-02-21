<?php

namespace Iliich246\YicmsCommon\Conditions;

use Iliich246\YicmsCommon\Base\AbstractTemplate;
use Iliich246\YicmsCommon\Validators\ValidatorBuilder;

/**
 * Class ConditionTemplate
 *
 * @property string $condition_template_reference
 * @property string $program_name,
 * @property integer $type
 * @property integer $condition_order
 * @property bool $editable
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionTemplate extends AbstractTemplate
{
    /**
     * @inheritdoc
     */
    protected static $buffer = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->editable = true;
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
     * @return bool
     */
    public function isConstraints()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        return true;
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

    /**
     * @inheritdoc
     */
    public function getValidatorReference()
    {
        if (!$this->validator_reference) {
            $this->validator_reference = ValidatorBuilder::generateValidatorReference();
            $this->scenario = self::SCENARIO_UPDATE;
            $this->save(false);
        }

        return $this->validator_reference;
    }
}
