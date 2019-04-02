<?php

namespace Iliich246\YicmsCommon\Fields;

use Iliich246\YicmsCommon\Annotations\Annotator;
use Iliich246\YicmsCommon\Annotations\AnnotateInterface;
use Iliich246\YicmsCommon\Annotations\AnnotatorFileInterface;
use Iliich246\YicmsCommon\Annotations\AnnotatorStringInterface;
use Iliich246\YicmsCommon\Base\AbstractTemplate;
use Iliich246\YicmsCommon\Validators\ValidatorDb;
use Iliich246\YicmsCommon\Validators\ValidatorBuilder;
use Iliich246\YicmsCommon\Validators\ValidatorReferenceInterface;

/**
 * Class FieldTemplate
 *
 * @property string $field_template_reference
 * @property string $validator_reference
 * @property integer $type
 * @property integer $language_type
 * @property integer $field_order
 * @property bool $visible
 * @property bool $editable
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldTemplate extends AbstractTemplate implements
    ValidatorReferenceInterface,
    AnnotateInterface,
    AnnotatorFileInterface,
    AnnotatorStringInterface
{
    /**
     * Types of fields
     * Type define style of render of field
     */
    const TYPE_INPUT    = 0;
    const TYPE_TEXT     = 1;
    const TYPE_REDACTOR = 2;

    /**
     * Language types of fields
     * Type define is field have translates or field has one value independent of languages
     */
    const LANGUAGE_TYPE_TRANSLATABLE = 0;
    const LANGUAGE_TYPE_SINGLE       = 1;

    /**
     * @inheritdoc
     */
    protected static $buffer = [];
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
        $this->visible  = true;
        $this->editable = true;
        parent::init();
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
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['type', 'language_type'], 'integer'],
            [['visible', 'editable'], 'boolean'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $prevScenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = array_merge($prevScenarios[self::SCENARIO_CREATE],
            ['type', 'language_type', 'visible', 'editable']);
        $scenarios[self::SCENARIO_UPDATE] = array_merge($prevScenarios[self::SCENARIO_UPDATE],
            ['type','language_type' ,'visible', 'editable']);

        return $scenarios;
    }

    /**
     * Return array of field types
     * @return array|bool
     */
    public static function getTypes()
    {
        static $array = false;

        if ($array) return $array;

        $array = [
            self::TYPE_INPUT    => 'Input type',
            self::TYPE_TEXT     => 'Text area type',
            self::TYPE_REDACTOR => 'Redactor type',
        ];

        return $array;
    }

    /**
     * Return array of field language types
     * @return array|bool
     */
    public static function getLanguageTypes()
    {
        static $array = false;

        if ($array) return $array;

        $array = [
            self::LANGUAGE_TYPE_TRANSLATABLE => 'Translatable type',
            self::LANGUAGE_TYPE_SINGLE       => 'Single type',
        ];

        return $array;
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributes = null)
    {
        if ($this->scenario === self::SCENARIO_CREATE) {
            $this->field_order = $this->maxOrder();
        }

        return parent::save($runValidation, $attributes);
    }

    /**
     * Returns true if this field template has constraints
     * @return bool
     */
    public function isConstraints()
    {
        if (Field::find()->where([
            'common_fields_template_id' => $this->id
        ])->one()) return true;

        return false;
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $fields = Field::find()->where([
            'common_fields_template_id' => $this->id
        ])->all();

        foreach($fields as $field)
            $field->delete();

        $fieldNames = FieldsNamesTranslatesDb::find()->where([
           'common_fields_template_id' => $this->id,
        ])->all();

        foreach($fieldNames as $fieldName)
            $fieldName->delete();

        $validators = ValidatorDb::find()->where([
                        'validator_reference' => $this->validator_reference
                    ])->all();

        foreach($validators as $validator)
            $validator->delete();

        return parent::delete();
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
        $this->scenario = self::SCENARIO_CHANGE_ORDER;
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

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     * @throws \yii\base\Exception
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
        $this->getAnnotator()->finish();
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
        self::$parentFileAnnotator->getAnnotationFileName() . '/Fields';
    }

    /**
     * @inheritdoc
     */
    public function getExtendsUseClass()
    {
        return 'Iliich246\YicmsCommon\Fields\Field';
    }

    /**
     * @inheritdoc
     */
    public function getExtendsClassName()
    {
        return 'Field';
    }

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public static function getAnnotationTemplateFile()
    {
        $class = new \ReflectionClass(self::class);
        return dirname($class->getFileName())  . '/annotations/field.php';
    }

    /**
     * @inheritdoc
     */
    public static function getAnnotationFileNamespace()
    {
        return self::$parentFileAnnotator->getAnnotationFileNamespace() . '\\'
        . self::$parentFileAnnotator->getAnnotationFileName() . '\\'
        . 'Fields';
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
     */
    public static function getAnnotationsStringArray($searchData)
    {
        /** @var self[] $templates */
        $templates = self::find()->where([
            'field_template_reference' => $searchData
        ])->orderBy([
            'field_order' => SORT_ASC
        ])->all();

        if (!$templates) return [];

        $result = [
            ' *' . PHP_EOL,
            ' * FIELDS' . PHP_EOL,
        ];

        foreach ($templates as $template) {
            $result[] = ' * @property string $' . $template->program_name . ' ' . PHP_EOL;
            $result[] = ' * @property string $field_' . $template->program_name . ' ' . PHP_EOL;
            $template->annotate();
        }

        return $result;
    }
}
