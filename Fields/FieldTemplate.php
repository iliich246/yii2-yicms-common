<?php

namespace Iliich246\YicmsCommon\Fields;

use Iliich246\YicmsCommon\Base\AbstractTemplate;
use Iliich246\YicmsCommon\Base\SortOrderInterface;
use Iliich246\YicmsCommon\Base\SortOrderTrait;
use yii\base\Exception;

/**
 * Class FieldTemplate
 *
 * @property integer
 * @property string $field_template_reference
 * @property integer $type
 * @property integer $language_type
 * @property integer $field_order
 * @property bool $visible
 * @property bool $editable
 * @property bool $is_main
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldTemplate extends AbstractTemplate implements SortOrderInterface
{
    use SortOrderTrait;

    /**
     * Types of fields
     * Type define style of render of field
     */
    const TYPE_INPUT = 0;
    const TYPE_TEXT = 1;
    const TYPE_REDACTOR = 2;

    /**
     * Language types of fields
     * Type define is field have translates or field has one value independent of languages
     */
    const LANGUAGE_TYPE_TRANSLATABLE = 0;
    const LANGUAGE_TYPE_SINGLE = 1;

    /**
     * @inheritdoc
     */
    protected static $buffer = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->visible = true;
        $this->editable = true;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['type', 'language_type'], 'integer'],
            [['visible', 'editable', 'is_main'], 'boolean'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $prevScenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = array_merge($prevScenarios[self::SCENARIO_CREATE],
            ['type', 'language_type', 'visible', 'editable', 'is_main']);
        $scenarios[self::SCENARIO_UPDATE] = array_merge($prevScenarios[self::SCENARIO_UPDATE],
            ['type','language_type' ,'visible', 'editable', 'is_main']);

        return $scenarios;
    }

    /**
     * Return array of field types
     * @return array
     */
    public static function getTypes()
    {
        static $array = false;

        if ($array) return $array;

        $array = [
            self::TYPE_INPUT => 'Input type',
            self::TYPE_TEXT => 'Text area type',
            self::TYPE_REDACTOR => 'Redactor type',
        ];

        return $array;
    }

    /**
     * Return array of field language types
     * @return array
     */
    public static function getLanguageTypes()
    {
        static $array = false;

        if ($array) return $array;

        $array = [
            self::LANGUAGE_TYPE_TRANSLATABLE => 'Translatable type',
            self::LANGUAGE_TYPE_SINGLE => 'Single type',
        ];

        return $array;
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributes = null)
    {
        if ($this->is_main) {
            /** @var self $other */
            foreach(self::find()->where([
                self::getTemplateReferenceName() => self::getTemplateReference(),
            ])->all() as $other)
            {
                if (!$other->is_main) continue;

                $other->is_main = false;
                $other->save(false);
            }
        }

        if ($this->scenario == self::SCENARIO_CREATE) {
            //throw new Exception(print_r($this, true));
            $this->field_order = $this->maxOrder();
        }

        parent::save($runValidation, $attributes);
    }

    /**
     * Return name of type of concrete field
     * @return mixed
     */
    public function getTypeName()
    {
        return self::getTypes()[$this->type];
    }

    /**
     * Return name of language type of concrete field
     * @return mixed
     */
    public function getLanguageTypeName()
    {
        return self::getLanguageTypes()[$this->language_type];
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
    public static function tableName()
    {
        return '{{%common_fields_templates}}';
    }

    /**
     * @inheritdoc
     */
    public function getOrderQuery()
    {
        return self::find()->where([
            'field_template_reference' => $this->field_template_reference,
            'language_type' => $this->language_type,
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function getOrderFieldName()
    {
        return 'field_order';
    }

    /**
     * @inheritdoc
     */
    public function getOrderValue()
    {
        return $this->field_order;
    }

    /**
     * @inheritdoc
     */
    public function setOrderValue($value)
    {
        $this->field_order = $value;
    }

    /**
     * @inheritdoc
     */
    public function configToChangeOfOrder()
    {
        $this->scenario = self::SCENARIO_UPDATE;
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
        return 'field_template_reference';
    }
}
