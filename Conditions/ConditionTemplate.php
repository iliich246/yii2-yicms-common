<?php

namespace Iliich246\YicmsCommon\Conditions;

use Iliich246\YicmsCommon\Annotations\Annotator;
use Iliich246\YicmsCommon\Annotations\AnnotateInterface;
use Iliich246\YicmsCommon\Annotations\AnnotatorFileInterface;
use Iliich246\YicmsCommon\Annotations\AnnotatorStringInterface;
use Iliich246\YicmsCommon\Base\AbstractTemplate;

/**
 * Class ConditionTemplate
 *
 * @property string $condition_template_reference
 * @property integer $type
 * @property integer $condition_order
 * @property bool $editable
 * @property bool $checkbox_state_default
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionTemplate extends AbstractTemplate implements
    AnnotateInterface,
    AnnotatorFileInterface,
    AnnotatorStringInterface
{
    const TYPE_CHECKBOX = 0;
    const TYPE_RADIO    = 1;
    const TYPE_SELECT   = 2;

    const DEFAULT_VALUE_TRUE  = 1;
    const DEFAULT_VALUE_FALSE = 0;

    /** @inheritdoc */
    protected static $buffer = [];
    /** @var ConditionValues[] */
    private $values = null;
    /** @var bool state of annotation necessity */
    private $needToAnnotate = true;
    /** @var Annotator instance */
    private $annotator = null;
    /** @var AnnotatorFileInterface instance */
    private static $parentFileAnnotator;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->editable               = true;
        $this->type                   = self::TYPE_CHECKBOX;
        $this->checkbox_state_default = false;

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
            [['checkbox_state_default'], 'boolean']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'checkbox_state_default' => 'Default checkbox value',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $prevScenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = array_merge($prevScenarios[self::SCENARIO_CREATE],
            ['type', 'editable', 'checkbox_state_default']);
        $scenarios[self::SCENARIO_UPDATE] = array_merge($prevScenarios[self::SCENARIO_UPDATE],
            ['type', 'editable', 'checkbox_state_default']);

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
     * Returns array of condition checkbox default values
     * @return array|bool
     */
    public static function getCheckBoxDefaultList()
    {
        static $array = false;

        if ($array) return $array;

        $array = [
            self::DEFAULT_VALUE_FALSE => 'FALSE',
            self::DEFAULT_VALUE_TRUE  => 'TRUE',
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
     * Returns true if condition has any values
     * @return bool
     */
    public function isValues()
    {
        if (!is_null($this->values)) return !!count($this->values);

        return !!count($this->getValuesList());
    }

    /**
     * Returns buffered list of values of template
     * @return ConditionValues[]
     */
    public function getValuesList()
    {
        if (!is_null($this->values)) return $this->values;

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
     * Returns default checkbox value for this template
     * @return bool
     */
    public function defaultCheckboxValue()
    {
        return !!$this->checkbox_state_default;
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
     * Sets parent file annotator
     * @param AnnotatorFileInterface $fileAnnotator
     */
    public static function setParentFileAnnotator(AnnotatorFileInterface $fileAnnotator)
    {
        self::$parentFileAnnotator = $fileAnnotator;
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     * @throws \ReflectionException
     */
    public function annotate()
    {
        $annotationArray = ConditionTemplateAnnotatorString::getAnnotationsStringArray($this);

        $this->getAnnotator()->addAnnotationArray($annotationArray);

        $this->getAnnotator()->finish(false);
    }

    /**
     * @inheritdoc
     */
    public function offAnnotation()
    {
        $this->needToAnnotate = false;
    }

    /**
     * @inheritdoc
     */
    public function onAnnotation()
    {
        $this->needToAnnotate = true;
    }

    /**
     * @inheritdoc
     */
    public function isAnnotationActive()
    {
        return $this->needToAnnotate;
    }

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public function getAnnotator()
    {
        if (!is_null($this->annotator)) return $this->annotator;

        $this->annotator = new Annotator();
        $this->annotator->setAnnotatorFileObject($this);
        $this->annotator->prepare();

        return $this->annotator;
    }

    /**
     * @inheritdoc
     */
    public function getAnnotationFilePath()
    {
        if (!is_dir(self::$parentFileAnnotator->getAnnotationFilePath() . '/' .
            self::$parentFileAnnotator->getAnnotationFileName()))
            mkdir(self::$parentFileAnnotator->getAnnotationFilePath() . '/' .
                self::$parentFileAnnotator->getAnnotationFileName());

        return self::$parentFileAnnotator->getAnnotationFilePath() . '/' .
        self::$parentFileAnnotator->getAnnotationFileName() . '/Conditions';
    }

    /**
     * @inheritdoc
     */
    public function getExtendsUseClass()
    {
        return 'Iliich246\YicmsCommon\Conditions\Condition';
    }

    /**
     * @inheritdoc
     */
    public function getExtendsClassName()
    {
        return 'Condition';
    }

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public static function getAnnotationTemplateFile()
    {
        $class = new \ReflectionClass(self::class);
        return dirname($class->getFileName())  . '/annotations/condition.php';
    }

    /**
     * @inheritdoc
     */
    public static function getAnnotationFileNamespace()
    {
        return self::$parentFileAnnotator->getAnnotationFileNamespace() . '\\'
        . self::$parentFileAnnotator->getAnnotationFileName() . '\\'
        . 'Conditions';
    }

    /**
     * @inheritdoc
     */
    public function getAnnotationFileName()
    {
        return ucfirst(mb_strtolower($this->program_name));
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     * @throws \ReflectionException
     */
    public static function getAnnotationsStringArray($searchData)
    {
        /** @var self[] $templates */
        $templates = self::find()->where([
            'condition_template_reference' => $searchData
        ])->orderBy([
            'condition_order' => SORT_ASC
        ])->all();

        if (!$templates) return [];

        $result = [
            ' *' . PHP_EOL,
            ' * CONDITIONS' . PHP_EOL,
        ];

        foreach ($templates as $template) {
            $result[] = ' * @property ' . '\\' .
                $template->getAnnotationFileNamespace() . '\\' .
                $template->getAnnotationFileName() .
                ' $' . $template->program_name . ' ' . PHP_EOL;
            $result[] = ' * @property ' . '\\' .
                $template->getAnnotationFileNamespace() . '\\' .
                $template->getAnnotationFileName() .
                ' $condition_' . $template->program_name . ' ' . PHP_EOL;
            $template->annotate();
        }

        return $result;
    }
}
