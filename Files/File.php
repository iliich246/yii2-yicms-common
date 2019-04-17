<?php

namespace Iliich246\YicmsCommon\Files;

use Iliich246\YicmsCommon\Base\CommonException;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use yii\validators\SafeValidator;
use yii\validators\RequiredValidator;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Annotations\Annotator;
use Iliich246\YicmsCommon\Annotations\AnnotateInterface;
use Iliich246\YicmsCommon\Annotations\AnnotatorFileInterface;
use Iliich246\YicmsCommon\Annotations\AnnotatorStringInterface;
use Iliich246\YicmsCommon\Base\AbstractEntity;
use Iliich246\YicmsCommon\Base\FictiveInterface;
use Iliich246\YicmsCommon\Base\SortOrderInterface;
use Iliich246\YicmsCommon\Base\SortOrderTrait;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use Iliich246\YicmsCommon\Fields\Field;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\FieldsHandler;
use Iliich246\YicmsCommon\Fields\FieldsInterface;
use Iliich246\YicmsCommon\Fields\FieldReferenceInterface;
use Iliich246\YicmsCommon\Conditions\Condition;
use Iliich246\YicmsCommon\Conditions\ConditionTemplate;
use Iliich246\YicmsCommon\Conditions\ConditionsHandler;
use Iliich246\YicmsCommon\Conditions\ConditionsInterface;
use Iliich246\YicmsCommon\Conditions\ConditionsReferenceInterface;
use Iliich246\YicmsCommon\Validators\ValidatorBuilder;
use Iliich246\YicmsCommon\Validators\ValidatorBuilderInterface;
use Iliich246\YicmsCommon\Validators\ValidatorReferenceInterface;

/**
 * Class File
 *
 * @property integer $id
 * @property integer $common_files_template_id
 * @property string $file_reference
 * @property string $field_reference
 * @property string $condition_reference
 * @property string $system_name
 * @property string $original_name
 * @property integer $file_order
 * @property integer $size
 * @property string $type
 * @property bool $editable
 * @property bool $visible
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class File extends AbstractEntity implements
    SortOrderInterface,
    FieldsInterface,
    FieldReferenceInterface,
    ConditionsInterface,
    ConditionsReferenceInterface,
    FictiveInterface,
    ValidatorBuilderInterface,
    ValidatorReferenceInterface,
    AnnotateInterface,
    AnnotatorFileInterface,
    AnnotatorStringInterface
{
    use SortOrderTrait;

    /** @var UploadedFile loaded file */
    public $file;
    /** @var FieldsHandler instance of field handler object */
    private $fieldHandler;
    /** @var ConditionsHandler instance of condition handler object  */
    private $conditionHandler;
    /** @var ValidatorBuilder instance */
    private $validatorBuilder;
    /** @var FileTranslate[] array of buffered translates */
    public $fileTranslates;
    /** @var bool state of annotation necessity */
    private $needToAnnotate = true;
    /** @var Annotator instance */
    private $annotator = null;
    /** @var AnnotatorFileInterface instance */
    private static $parentFileAnnotator;
    /** @var array of exception words for magical getter/setter */
    protected static $annotationExceptionWords = [
        'id',
        'common_files_template_id',
        'file_reference',
        'field_reference',
        'condition_reference',
        'system_name',
        'original_name',
        'file_order',
        'size',
        'type',
        'editable',
        'visible',
        'created_at',
        'updated_at'
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_files}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'editable' => 'Editable(dev)'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['editable', 'visible'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * Magical get method for use object annotations
     * @param string $name
     * @return bool|Condition|mixed|object|string
     * @throws CommonException
     */
    public function __get($name)
    {
        if (in_array($name, self::$annotationExceptionWords))
            return parent::__get($name);

        if (strpos($name, 'field_') === 0) {
            if ($this->isNonexistent()) {
                $nonexistentField = new Field();
                $nonexistentField->setNonexistent();
                $nonexistentField->setNonexistentName($name);

                return $nonexistentField;
            }

            if ($this->isField(substr($name, 6)))
                return $this->getFieldHandler()->getField(substr($name, 6));

            return parent::__get($name);
        }

        if (strpos($name, 'condition_') === 0) {
            if ($this->isNonexistent()) {
                $nonexistentCondition = new Condition();
                $nonexistentCondition->setNonexistent();
                $nonexistentCondition->setNonexistentName($name);

                return $nonexistentCondition;
            }

            if ($this->isCondition(substr($name, 10)))
                return $this->getConditionsHandler()->getCondition(substr($name, 10));

            return parent::__get($name);
        }

        if ($this->isNonexistent()) {
            if (FieldTemplate::isTemplate($this->getFieldTemplateReference(), $name)) {
                $nonexistentField = new Field();
                $nonexistentField->setNonexistent();
                $nonexistentField->setNonexistentName($name);

                return $nonexistentField;
            }

            if (ConditionTemplate::isTemplate($this->getConditionTemplateReference(), $name)) {
                $nonexistentCondition = new Condition();
                $nonexistentCondition->setNonexistent();
                $nonexistentCondition->setNonexistentName($name);

                return $nonexistentCondition;
            }

            if (defined('YICMS_STRICT'))
                throw new CommonException('Can`t find any field for annotated Files Block');

            if (defined('YICMS_ALERTS') && CommonModule::isUnderDev())
                return 'Can`t find any field for annotated Files Block';

            return '';
        }

        if ($this->getFieldHandler()->isField($name))
            return $this->getFieldHandler()->getField($name);

        if ($this->getConditionsHandler()->isCondition($name))
            return $this->getConditionsHandler()->getCondition($name);

        return parent::__get($name);
    }

    /**
     * Return FilesBlock associated with this file entity
     * @return FilesBlock|array|\Iliich246\YicmsCommon\Base\AbstractEntityBlock|null|\yii\db\ActiveRecord
     */
    public function getFileBlock()
    {
        return $this->getEntityBlock();
    }

    /**
     * Proxy parent method with concrete method name
     * @param FilesBlock $filesBlock
     */
    public function setFileBlock(FilesBlock $filesBlock)
    {
        $this->setEntityBlock($filesBlock);
    }

    /**
     * @inheritdoc
     */
    protected static function getReferenceName()
    {
        return 'file_reference';
    }

    /**
     * Proxy getFileName() method to magical __toString()
     * @return bool|string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function __toString()
    {
        return $this->getFileName();
    }

    /**
     * Return translated file name
     * @param LanguagesDb|null $language
     * @param bool|false $addExtension
     * @return bool|string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getFileName(LanguagesDb $language = null, $addExtension = false)
    {
        if ($this->isNonexistent()) {
            if (CommonModule::isUnderDev()) return 'None existent file';
            return false;
        }

        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        $fileTranslate = $this->getFileTranslate($language);

        if ($this->getFileBlock()->language_type == FilesBlock::LANGUAGE_TYPE_SINGLE)
            $extension = strrchr($this->system_name, '.');
        else
            $extension = strrchr($fileTranslate->system_name, '.');

        if ($fileTranslate && trim($fileTranslate->filename))
            return !$addExtension ? $fileTranslate->filename :
                                   $fileTranslate->filename . $extension;

        if ($this->getFileBlock()->language_type == FilesBlock::LANGUAGE_TYPE_SINGLE)
            return !$addExtension ? $this->original_name :
                                   $this->original_name . $extension;

        if ($fileTranslate)
            return !$addExtension ? $fileTranslate->original_name :
                                   $fileTranslate->original_name . $extension;

        return false;
    }

    /**
     * Returns path of file in correct translate
     * @param LanguagesDb|null $language
     * @return bool|string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getPath(LanguagesDb $language = null)
    {
        if ($this->isNonexistent) return false;

        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        $filesBlock = $this->getFileBlock();
        $fileTranslate = $this->getFileTranslate($language);

        if ($filesBlock->language_type == FilesBlock::LANGUAGE_TYPE_SINGLE)
            $systemName = $this->system_name;
        else {
            if (!$fileTranslate) return false;

            $systemName = $fileTranslate->system_name;
        }

        $path = CommonModule::getInstance()->filesPatch . $systemName;

        if (!file_exists($path) || is_dir($path)) return false;

        return $path;
    }

    /**
     * Returns link for upload this file entity
     * @param LanguagesDb|null $language
     * @param bool|true $onlyPhysicalExistedFiles
     * @return bool|string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function uploadUrl(LanguagesDb $language= null, $onlyPhysicalExistedFiles = true)
    {
        if ($this->isNonexistent) return false;

        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        if ($onlyPhysicalExistedFiles && !$this->getPath()) return false;

        return Url::toRoute([
            '/common/files/upload-file',
            'fileBlockId' => $this->getFileBlock()->id,
            'fileId'      => $this->id,
            'language'    => $language->id
        ]);
    }

    /**
     * Return buffered file translate db
     * @param LanguagesDb $language
     * @return FileTranslate
     */
    private function getFileTranslate(LanguagesDb $language)
    {
        if (!isset($this->fileTranslates[$language->id])
        ) {
            $this->fileTranslates[$language->id] = FileTranslate::find()->where([
                'common_file_id'     => $this->id,
                'common_language_id' => $language->id
            ])->one();
        }

        return $this->fileTranslates[$language->id];
    }

    /**
     * @inheritdoc
     */
    public function entityBlockQuery()
    {
        return FilesBlock::find()->where([
            'id' => $this->common_files_template_id
        ]);
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $this->deleteSequence();
        return parent::delete();
    }

    /**
     * @inheritdoc
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    protected function deleteSequence()
    {
        $fileTranslates = FileTranslate::find()->where([
            'common_file_id' => $this->id,
        ])->all();

        if ($fileTranslates)
            foreach($fileTranslates as $fileTranslate)
                $fileTranslate->delete();

        $path = CommonModule::getInstance()->filesPatch . $this->system_name;

        if (file_exists($path) && !is_dir($path))
            unlink($path);

        /** @var FilesBlock $fileBlock */
        $fileBlock = $this->getEntityBlock();

        /** @var FieldTemplate $fieldTemplates */
        $fieldTemplates = FieldTemplate::find()->where([
            'field_template_reference' => $fileBlock->getFieldTemplateReference(),
        ])->all();

        foreach($fieldTemplates as $fieldTemplate) {
            /** @var Field $field */
            $field = Field::find()->where([
                'common_fields_template_id' => $fieldTemplate->id,
                'field_reference'           => $this->field_reference,
            ])->one();

            if ($field) $field->delete();
        }

        /** @var ConditionTemplate $conditionTemplates */
        $conditionTemplates = ConditionTemplate::find()->where([
            'condition_template_reference' => $fileBlock->getConditionTemplateReference(),
        ])->all();

        foreach($conditionTemplates as $conditionTemplate) {
            /** @var Condition $condition */
            $condition = Condition::find()->where([
                'common_condition_template_id' => $conditionTemplate->id,
                'condition_reference'          => $this->condition_reference
            ])->one();

            if ($condition) $condition->delete();
        }

        return true;
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
     * @return int|string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getFieldTemplateReference()
    {
        return $this->getFileBlock()->getFieldTemplateReference();
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getFieldReference()
    {
        if (!$this->field_reference) {
            $this->field_reference = FieldTemplate::generateTemplateReference();
            $this->save(false);
        }

        return $this->field_reference;
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
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getConditionTemplateReference()
    {
        return $this->getFileBlock()->getConditionTemplateReference();
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getConditionReference()
    {
        if (!$this->condition_reference) {
            $this->condition_reference = ConditionTemplate::generateTemplateReference();
            $this->save(false);
        }

        return $this->condition_reference;
    }

    /**
     * Method config validators for this model
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function prepareValidators()
    {
        $validators = $this->getValidatorBuilder()->build();

        if (!$validators) {

            $safeValidator = new SafeValidator();
            $safeValidator->attributes = ['file'];
            $this->validators[] = $safeValidator;

            return;
        }

        foreach ($validators as $validator) {

            if ($validator instanceof RequiredValidator && !$this->isNewRecord) continue;

            $validator->attributes = ['file'];
            $this->validators[] = $validator;
        }
    }

    /**
     * @inheritdoc
     */
    public function getValidatorBuilder()
    {
        if ($this->validatorBuilder) return $this->validatorBuilder;

        $this->validatorBuilder = new ValidatorBuilder();
        $this->validatorBuilder->setReferenceAble($this);

        return $this->validatorBuilder;
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getValidatorReference()
    {
        $fileBlock = $this->getFileBlock();

        if (!$fileBlock->validator_reference) {
            $fileBlock->validator_reference = ValidatorBuilder::generateValidatorReference();
            $fileBlock->scenario = FilesBlock::SCENARIO_UPDATE;
            $fileBlock->save(false);
        }

        return $fileBlock->validator_reference;
    }

    /**
     * @inheritdoc
     */
    public function getOrderQuery()
    {
        return self::find()->where([
            'common_files_template_id' => $this->common_files_template_id,
            'file_reference' => $this->file_reference,
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function getOrderFieldName()
    {
        return 'file_order';
    }

    /**
     * @inheritdoc
     */
    public function getOrderValue()
    {
        return $this->file_order;
    }

    /**
     * @inheritdoc
     */
    public function setOrderValue($value)
    {
        $this->file_order = $value;
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
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     * @throws \ReflectionException
     */
    public function annotate()
    {
        FieldTemplate::setParentFileAnnotator($this);

        $this->getAnnotator()->addAnnotationArray(
            FieldTemplate::getAnnotationsStringArray($this->getFieldTemplateReference())
        );

        ConditionTemplate::setParentFileAnnotator($this);

        $this->getAnnotator()->addAnnotationArray(
            ConditionTemplate::getAnnotationsStringArray($this->getConditionTemplateReference())
        );
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
     * Sets parent file annotator
     * @param AnnotatorFileInterface $fileAnnotator
     */
    public static function setParentFileAnnotator(AnnotatorFileInterface $fileAnnotator)
    {
        self::$parentFileAnnotator = $fileAnnotator;
    }

    /**
     * @inheritdoc
     */
    public function getAnnotationFileName()
    {
        return ucfirst(mb_strtolower($this->getFileBlock()->program_name)) . 'File';
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
        self::$parentFileAnnotator->getAnnotationFileName() . '/Files';
    }

    /**
     * @inheritdoc
     */
    public function getExtendsUseClass()
    {
        return 'Iliich246\YicmsCommon\Files\File';
    }

    /**
     * @inheritdoc
     */
    public function getExtendsClassName()
    {
        return 'File';
    }

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public static function getAnnotationTemplateFile()
    {
        $class = new \ReflectionClass(self::class);
        return dirname($class->getFileName())  . '/annotations/file.php';
    }

    /**
     * @inheritdoc
     */
    public static function getAnnotationFileNamespace()
    {
        return self::$parentFileAnnotator->getAnnotationFileNamespace() . '\\'
        . self::$parentFileAnnotator->getAnnotationFileName() . '\\'
        . 'Files';
    }

    /**
     * @inheritdoc
     */
    public static function getAnnotationsStringArray($data)
    {

    }
}
