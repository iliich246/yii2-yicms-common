<?php

namespace Iliich246\YicmsCommon\FreeEssences;

use Iliich246\YicmsCommon\CommonModule;
use Yii;
use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\Annotations\Annotator;
use Iliich246\YicmsCommon\Annotations\AnnotateInterface;
use Iliich246\YicmsCommon\Annotations\AnnotatorFileInterface;
use Iliich246\YicmsCommon\Base\SortOrderTrait;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Base\FictiveInterface;
use Iliich246\YicmsCommon\Base\SortOrderInterface;
use Iliich246\YicmsCommon\Base\NonexistentInterface;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use Iliich246\YicmsCommon\Fields\FieldsHandler;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\FieldsInterface;
use Iliich246\YicmsCommon\Fields\FieldReferenceInterface;
use Iliich246\YicmsCommon\Files\FilesBlock;
use Iliich246\YicmsCommon\Files\FilesHandler;
use Iliich246\YicmsCommon\Files\FilesInterface;
use Iliich246\YicmsCommon\Files\FilesReferenceInterface;
use Iliich246\YicmsCommon\Images\ImagesBlock;
use Iliich246\YicmsCommon\Images\ImagesHandler;
use Iliich246\YicmsCommon\Images\ImagesInterface;
use Iliich246\YicmsCommon\Images\ImagesReferenceInterface;
use Iliich246\YicmsCommon\Conditions\ConditionTemplate;
use Iliich246\YicmsCommon\Conditions\ConditionsHandler;
use Iliich246\YicmsCommon\Conditions\ConditionsInterface;
use Iliich246\YicmsCommon\Conditions\ConditionsReferenceInterface;

/**
 * Class FreeEssences
 *
 * @property integer $id
 * @property string $program_name
 * @property boolean $editable
 * @property boolean $visible
 * @property integer $free_essences_order
 * @property string $field_template_reference
 * @property string $field_reference
 * @property string $file_template_reference
 * @property string $file_reference
 * @property string $image_template_reference
 * @property string $image_reference
 * @property string $condition_template_reference
 * @property string $condition_reference
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FreeEssences extends ActiveRecord implements
    FieldsInterface,
    FieldReferenceInterface,
    FilesInterface,
    FilesReferenceInterface,
    ImagesInterface,
    ImagesReferenceInterface,
    ConditionsReferenceInterface,
    ConditionsInterface,
    FictiveInterface,
    SortOrderInterface,
    NonexistentInterface,
    AnnotateInterface,
    AnnotatorFileInterface
{
    use SortOrderTrait;

    const SCENARIO_CREATE = 0;
    const SCENARIO_UPDATE = 1;

    /** @var FieldsHandler instance of field handler object */
    private $fieldHandler;
    /** @var FilesHandler */
    private $fileHandler;
    /** @var ImagesHandler */
    private $imageHandler;
    /** @var ConditionsHandler */
    private $conditionHandler;
    /** @var bool keep nonexistent state of page */
    private $isNonexistent = false;
    /** @var string keeps name of nonexistent page */
    private $nonexistentName;
    /** @var bool state of annotation necessity */
    private $needToAnnotate = true;
    /** @var Annotator instance */
    private $annotator = null;
    /** @var array of exception words for magical getter/setter */
    protected static $annotationExceptionWords = [
        'scenario',
        'isNewRecord',
        'id',
        'program_name',
        'editable',
        'visible',
        'free_essences_order',
        'field_template_reference',
        'field_reference',
        'file_template_reference',
        'file_reference',
        'image_template_reference',
        'image_reference',
        'condition_template_reference',
        'condition_reference'
    ];

    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->visible = true;
        $this->editable = true;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_free_essences}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'program_name' => 'Program name',
            'editable' => 'Editable',
            'visible' => 'Visible',
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'program_name', 'editable', 'visible',
            ],
            self::SCENARIO_UPDATE => [
                'program_name', 'editable', 'visible',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['program_name', 'required', 'message' => 'Obligatory input field'],
            ['program_name', 'string', 'max' => '50', 'tooLong' => 'Program name must be less than 50 symbols'],
            ['program_name', 'validateProgramName'],
        ];
    }

    /**
     * Magical get method for use object annotations
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (in_array($name, self::$annotationExceptionWords))
            return parent::__get($name);

        if ($this->scenario === self::SCENARIO_CREATE)
            return parent::__get($name);

        if (strpos($name, 'field_') === 0) {
            if ($this->isField(substr($name, 6)))
                return $this->getFieldHandler()->getField(substr($name, 6));

            return parent::__get($name);
        }

        if (strpos($name, 'file_') === 0) {
            if ($this->isFileBlock(substr($name, 5)))
                return $this->getFileHandler()->getFileBlock(substr($name, 5));

            return parent::__get($name);
        }

        if (strpos($name, 'image_') === 0) {
            if ($this->isImageBlock(substr($name, 6)))
                return $this->getImagesHandler()->getImageBlock(substr($name, 6));

            return parent::__get($name);
        }

        if (strpos($name, 'condition_') === 0) {
            if ($this->isCondition(substr($name, 10)))
                return $this->getConditionsHandler()->getCondition(substr($name, 10));

            return parent::__get($name);
        }

        if ($this->getFieldHandler()->isField($name))
            return $this->getFieldHandler()->getField($name);

        if ($this->getFileHandler()->isFileBlock($name))
            return $this->getFileHandler()->getFileBlock($name);

        if ($this->getImagesHandler()->isImageBlock($name))
            return $this->getImagesHandler()->getImageBlock($name);

        if ($this->getConditionsHandler()->isCondition($name))
            return $this->getConditionsHandler()->getCondition($name);

        return parent::__get($name);
    }

    /**
     * Return instance of page by her name
     * @param $programName
     * @return self
     * @throws CommonException
     */
    public static function getByName($programName)
    {
        /** @var self $freeEssence */
        $freeEssence = static::find()
            ->where(['program_name' => $programName])
            ->one();

        if ($freeEssence) return $freeEssence;

        Yii::error("Сan not find free essence with name " . $programName, __METHOD__);

        if (defined('YICMS_STRICT')) {
            throw new CommonException('Сan not find free essence with name ' . $programName);
        }

        $nonexistentFreeEssence = new self();
        $nonexistentFreeEssence->setNonexistent();
        $nonexistentFreeEssence->nonexistentName = $programName;

        return $nonexistentFreeEssence;
    }

    /**
     * Validates the program name.
     * This method serves as the inline validation for page program name.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateProgramName($attribute, $params)
    {
        if (!$this->hasErrors()) {

            $pagesQuery = self::find()->where(['program_name' => $this->program_name]);

            if ($this->scenario == self::SCENARIO_UPDATE)
                $pagesQuery->andWhere(['not in', 'program_name', $this->getOldAttribute('program_name')]);

            $pages = $pagesQuery->all();
            if ($pages)$this->addError($attribute, 'Free essence with same name already exist in system');
        }
    }

    /**
     * @inheritdoc
     * @throws CommonException
     */
    public function afterValidate()
    {
        if ($this->hasErrors()) return;

        if ($this->scenario == self::SCENARIO_CREATE) {
            $this->field_template_reference = FieldTemplate::generateTemplateReference();
            $this->field_reference = $this->field_template_reference;

            $this->file_template_reference = FilesBlock::generateTemplateReference();
            $this->file_reference = $this->file_template_reference;

            $this->image_template_reference = ImagesBlock::generateTemplateReference();
            $this->image_reference = $this->image_template_reference;

            $this->condition_template_reference = ConditionTemplate::generateTemplateReference();
            $this->condition_reference = $this->condition_template_reference;
        }
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->isNonexistent) return false;

        if ($this->scenario === self::SCENARIO_CREATE) {
            $this->free_essences_order = $this->maxOrder();
        }

        return parent::save($runValidation = true, $attributeNames = null);
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        if ($this->isNonexistent) return false;

        /** @var FieldTemplate[] $fieldTemplates */
        $fieldTemplates = FieldTemplate::find()->where([
            'field_template_reference' => $this->getFieldTemplateReference(),
        ])->all();

        foreach($fieldTemplates as $fieldTemplate)
            $fieldTemplate->delete();

        /** @var FilesBlock[] $filesBlocks */
        $filesBlocks = FilesBlock::find()->where([
            'file_template_reference' => $this->getFileTemplateReference(),
        ])->all();

        foreach($filesBlocks as $fileBlock)
            $fileBlock->delete();

        /** @var ImagesBlock[] $imageBlocks */
        $imageBlocks = ImagesBlock::find()->where([
            'image_template_reference' => $this->getImageTemplateReference(),
        ])->all();

        foreach($imageBlocks as $imageBlock)
            $imageBlock->delete();

        /** @var ConditionTemplate[] $conditionTemplates */
        $conditionTemplates = ConditionTemplate::find()->where([
            'condition_template_reference' => $this->getConditionTemplateReference(),
        ])->all();

        foreach($conditionTemplates as $conditionTemplate)
            $conditionTemplate->delete();

        /** @var FreeEssenceNamesTranslatesDb[] $freeEssenceNames */
        $freeEssenceNames = FreeEssenceNamesTranslatesDb::find()->where([
            'common_free_essence_id' => $this->id,
        ])->all();

        foreach($freeEssenceNames as $freeEssenceName)
            $freeEssenceName->delete();

        return parent::delete();
    }

    /**
     * Return true if free essence has any constraints
     * @return bool
     */
    public function isConstraints()
    {
        if ($this->isNonexistent) return false;

        /** @var FieldTemplate[] $fieldTemplates */
        $fieldTemplates = FieldTemplate::find()->where([
            'field_template_reference' => $this->getFieldTemplateReference(),
        ])->all();

        foreach($fieldTemplates as $fieldTemplate)
            if ($fieldTemplate->isConstraints()) return true;

        /** @var FilesBlock[] $filesBlocks */
        $filesBlocks = FilesBlock::find()->where([
            'file_template_reference' => $this->getFileTemplateReference(),
        ])->all();

        foreach($filesBlocks as $fileBlock)
            if ($fileBlock->isConstraints()) return true;

        /** @var ImagesBlock[] $imageBlocks */
        $imageBlocks = ImagesBlock::find()->where([
            'image_template_reference' => $this->getImageTemplateReference(),
        ])->all();

        foreach($imageBlocks as $imageBlock)
            if ($imageBlock->isConstraints()) return true;

        /** @var ConditionTemplate[] $conditionTemplates */
        $conditionTemplates = ConditionTemplate::find()->where([
            'condition_template_reference' => $this->getConditionTemplateReference(),
        ])->all();

        foreach($conditionTemplates as $conditionTemplate)
            if ($conditionTemplate->isConstraints()) return true;

        return false;
    }

    /**
     * Returns name of free essence
     * @param LanguagesDb|null $language
     * @return string
     * @throws CommonException
     */
    public function name(LanguagesDb $language = null)
    {
        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        if (!FreeEssenceNamesTranslatesDb::getTranslate($this->id, $language->id)) return $this->program_name;

        return FreeEssenceNamesTranslatesDb::getTranslate($this->id, $language->id)->name;
    }

    /**
     * Returns description of free essence
     * @param LanguagesDb|null $language
     * @return string
     * @throws CommonException
     */
    public function description(LanguagesDb $language = null)
    {
        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        if (!FreeEssenceNamesTranslatesDb::getTranslate($this->id, $language->id)) return $this->program_name;

        return FreeEssenceNamesTranslatesDb::getTranslate($this->id, $language->id)->description;
    }

    /**
     * @inheritdoc
     */
    public function getFieldHandler()
    {
        if (!$this->fieldHandler)
            $this->fieldHandler = new FieldsHandler($this);

        return $this->fieldHandler;
    }

    /**
     * @inheritdoc
     */
    public function getField($name)
    {
        return $this->getFieldHandler()->getField($name);
    }

    /**
     * @inheritdoc
     */
    public function isField($name)
    {
        return $this->getFieldHandler()->isField($name);
    }

    /**
     * @inheritdoc
     */
    public function getFieldTemplateReference()
    {
        return $this->field_template_reference;
    }

    /**
     * @inheritdoc
     */
    public function getFieldReference()
    {
        return $this->field_reference;
    }

    /**
     * @inheritdoc
     */
    public function getFileHandler()
    {
        if (!$this->fileHandler)
            $this->fileHandler = new FilesHandler($this);

        return $this->fileHandler;
    }

    /**
     * @inheritdoc
     */
    public function getFileBlock($name)
    {
        return $this->getFileHandler()->getFileBlock($name);
    }

    /**
     * @inheritdoc
     */
    public function isFileBlock($name)
    {
        $this->getFileHandler()->isFileBlock($name);
    }

    /**
     * @inheritdoc
     */
    public function getFileTemplateReference()
    {
        return $this->file_template_reference;
    }

    /**
     * @inheritdoc
     */
    public function getFileReference()
    {
        return $this->file_reference;
    }

    /**
     * @inheritdoc
     */
    public function getImagesHandler()
    {
        if (!$this->imageHandler)
            $this->imageHandler = new ImagesHandler($this);

        return $this->imageHandler;
    }

    /**
     * @inheritdoc
     */
    public function getImageBlock($name)
    {
        return $this->getImagesHandler()->getImageBlock($name);
    }

    /**
     * @inheritdoc
     */
    public function isImageBlock($name)
    {
        return $this->getImagesHandler()->isImageBlock($name);
    }

    /**
     * @inheritdoc
     */
    public function getImageTemplateReference()
    {
        return $this->image_template_reference;
    }

    /**
     * @inheritdoc
     */
    public function getImageReference()
    {
        return $this->image_reference;
    }

    /**
     * @inheritdoc
     */
    public function getConditionsHandler()
    {
        if (!$this->conditionHandler)
            $this->conditionHandler = new ConditionsHandler($this);

        return $this->conditionHandler;
    }

    /**
     * @inheritdoc
     */
    public function getCondition($name)
    {
        return $this->getConditionsHandler()->getCondition($name);
    }

    /**
     * @inheritdoc
     */
    public function isCondition($name)
    {
        return $this->getConditionsHandler()->isCondition($name);
    }

    /**
     * @inheritdoc
     */
    public function getConditionTemplateReference()
    {
        return $this->condition_template_reference;
    }

    /**
     * @inheritdoc
     */
    public function getConditionReference()
    {
        return $this->condition_reference;
    }

    /**
     * @inheritdoc
     */
    public function getOrderQuery()
    {
        return self::find();
    }

    /**
     * @inheritdoc
     */
    public static function getOrderFieldName()
    {
        return 'free_essences_order';
    }

    /**
     * @inheritdoc
     */
    public function getOrderValue()
    {
        return $this->free_essences_order;
    }

    /**
     * @inheritdoc
     */
    public function setOrderValue($value)
    {
        $this->free_essences_order = $value;
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
    public function setFictive()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function clearFictive()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isFictive()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isNonexistent()
    {
        return $this->isNonexistent;
    }

    /**
     * @inheritdoc
     */
    public function setNonexistent()
    {
        $this->isNonexistent = true;
    }

    /**
     * @inheritdoc
     */
    public function getNonexistentName()
    {
        return $this->nonexistentName;
    }

    /**
     * @inheritdoc
     */
    public function setNonexistentName($name)
    {
        $this->nonexistentName = $name;
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     * @throws \ReflectionException
     */
    public function annotate()
    {
        FieldTemplate::setParentFileAnnotator($this);

        $this->getAnnotator()->addAnnotationArray(
            FieldTemplate::getAnnotationsStringArray($this->getFieldTemplateReference())
        );

        FilesBlock::setParentFileAnnotator($this);

        $this->getAnnotator()->addAnnotationArray(
            FilesBlock::getAnnotationsStringArray($this->getFileTemplateReference())
        );

        ImagesBlock::setParentFileAnnotator($this);

        $this->getAnnotator()->addAnnotationArray(
            ImagesBlock::getAnnotationsStringArray($this->getImageTemplateReference())
        );

        ConditionTemplate::setParentFileAnnotator($this);

        $this->getAnnotator()->addAnnotationArray(
            ConditionTemplate::getAnnotationsStringArray($this->getConditionTemplateReference())
        );

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
    public function getAnnotationFileName()
    {
        return ucfirst(mb_strtolower($this->program_name));
    }

    /**
     * @inheritdoc
     */
    public function getAnnotationFilePath()
    {
        $path = Yii::getAlias(CommonModule::getInstance()->yicmsLocation);
        $path .= '/' . CommonModule::getInstance()->getModuleName();
        $path .= '/' . CommonModule::getInstance()->annotationsDirectory;

        return $path;
    }

    /**
     * @inheritdoc
     */
    public function getExtendsUseClass()
    {
        return 'Iliich246\YicmsCommon\FreeEssences\FreeEssences';
    }

    /**
     * @inheritdoc
     */
    public function getExtendsClassName()
    {
        return 'FreeEssences';
    }

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public static function getAnnotationTemplateFile()
    {
        $class = new \ReflectionClass(self::class);
        return dirname($class->getFileName())  . '/annotations/free_essence.php';
    }

    /**
     * @inheritdoc
     */
    public static function getAnnotationFileNamespace()
    {
        return CommonModule::getInstance()->yicmsNamespace . '\\' .
        CommonModule::getInstance()->getModuleName() . '\\' .
        CommonModule::getInstance()->annotationsDirectory;
    }
}
