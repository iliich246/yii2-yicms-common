<?php

namespace Iliich246\YicmsCommon\Fields;

use Iliich246\YicmsCommon\Base\AbstractTemplate;

/**
 * Class FieldTemplate
 *
 * @property integer
 * @property string $field_template_reference
 * @property integer $type
 * @property bool $visible
 * @property bool $editable
 * @property bool $is_main
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldTemplate extends AbstractTemplate
{
    /**
     * Types of fields
     *
     * Type define style of render of field
     */
    const TYPE_INPUT = 0;
    const TYPE_TEXT = 1;
    const TYPE_REDACTOR = 2;

    /**
     * @inheritdoc
     */
    private static $buffer = [];

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
            ['type', 'integer'],
            [['visible', 'editable', 'is_main'], 'boolean'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $prevScenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = array_merge($prevScenarios[self::SCENARIO_CREATE],['type', 'visible', 'editable', 'is_main']);
        $scenarios[self::SCENARIO_UPDATE] = array_merge($prevScenarios[self::SCENARIO_UPDATE],['type', 'visible', 'editable', 'is_main']);

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
    protected static function getBuffer()
    {
        return self::$buffer;
    }

    /**
     * @inheritdoc
     */
    protected static function setBuffer($buffer)
    {
        self::$buffer = $buffer;
    }

    /**
     * @inheritdoc
     */
    protected static function getTemplateReferenceName()
    {
        return 'field_template_reference';
    }
}
