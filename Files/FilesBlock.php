<?php

namespace Iliich246\YicmsCommon\Files;

use yii\db\ActiveQuery;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AbstractEntityBlock;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\FieldReferenceInterface;
use Iliich246\YicmsCommon\Conditions\ConditionTemplate;
use Iliich246\YicmsCommon\Conditions\ConditionsReferenceInterface;
use Iliich246\YicmsCommon\Validators\ValidatorDb;
use Iliich246\YicmsCommon\Validators\ValidatorBuilder;
use Iliich246\YicmsCommon\Validators\ValidatorReferenceInterface;

/**
 * Class FilesBlock
 *
 * @property string $file_template_reference
 * @property string $field_template_reference
 * @property string $condition_template_reference
 * @property string $validator_reference
 * @property integer $type
 * @property integer $language_type
 * @property integer $file_order
 * @property bool $visible
 * @property bool $editable
 * @property bool $max_files
 *
 * @method File getEntity()
 * @method File[] getEntities()
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FilesBlock extends AbstractEntityBlock implements
    FieldReferenceInterface,
    ValidatorReferenceInterface,
    ConditionsReferenceInterface
{
    /**
     * Files types
     */
    const TYPE_MULTIPLICITY = 0;
    const TYPE_ONE_FILE = 1;
    /**
     * Language types of files
     * Type define is file have translates or file has one instance independent of languages
     */
    const LANGUAGE_TYPE_TRANSLATABLE = 0;
    const LANGUAGE_TYPE_SINGLE = 1;

    /** @var FilesNamesTranslatesDb[] buffer */
    private $fileNamesTranslates = [];
    /** @var string fileReference for what files group must be fetched */
    private $currentFileReference;
    /** @inheritdoc */
    protected static $buffer = [];

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
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'createStandardFields' => 'Create standard fields (filename)',
            'max_files'            => 'Maximum files in block'
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_files_templates}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['type', 'language_type'], 'integer'],
            [['visible', 'editable'], 'boolean'],
            ['max_files', 'integer', 'min' => 0],
            ['max_files', 'default', 'value' => 0]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $prevScenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = array_merge($prevScenarios[self::SCENARIO_CREATE],
            ['type', 'language_type', 'visible', 'editable', 'max_files']);
        $scenarios[self::SCENARIO_UPDATE] = array_merge($prevScenarios[self::SCENARIO_UPDATE],
            ['type','language_type' ,'visible', 'editable', 'max_files']);

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
            self::TYPE_ONE_FILE => 'One file',
            self::TYPE_MULTIPLICITY => 'Multiple files',
        ];

        return $array;
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
     * Return array of field language types
     * @return array|bool
     */
    public static function getLanguageTypes()
    {
        static $array = false;

        if ($array) return $array;

        $array = [
            self::LANGUAGE_TYPE_SINGLE       => 'Single type',
            self::LANGUAGE_TYPE_TRANSLATABLE => 'Translatable type',
        ];

        return $array;
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
    public function save($runValidation = true, $attributes = null)
    {
        if ($this->scenario === self::SCENARIO_CREATE) {
            $this->file_order = $this->maxOrder();
        }

        return parent::save($runValidation, $attributes);
    }

    /**
     * @inheritdoc
     */
    public static function getInstance($templateReference, $programName, $currentFileReference = null)
    {
        /** @var FilesBlock $value */
        $value = parent::getInstance($templateReference, $programName);

        if (!$value->currentFileReference) $value->currentFileReference = $currentFileReference;

        return $value;
    }

    /**
     * @return bool
     */
    public function isConstraints()
    {
        return true;
    }

    /**
     * Renames parent method on concrete name
     * @return File
     */
    public function getFile()
    {
        return $this->getEntity();
    }

    /**
     * Renames parent method on concrete name
     * @return File[]
     */
    public function getFiles()
    {
        return $this->getEntities();
    }

    /**
     * Proxy method uploadUrl to first file in block
     * @param LanguagesDb|null $language
     * @param bool $onlyPhysicalExistedFiles
     * @return bool|string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function uploadUrl(LanguagesDb $language= null, $onlyPhysicalExistedFiles = true)
    {
        return $this->getFile()->uploadUrl($language, $onlyPhysicalExistedFiles);
    }

    /**
     * Proxy method getFileName to first file in block
     * @param LanguagesDb|null $language
     * @param bool $addExtension
     * @return bool|string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getFileName(LanguagesDb $language = null, $addExtension = false)
    {
        return $this->getFile()->getFileName($language, $addExtension);
    }

    /**
     * Sets current file reference
     * @param $fileReference
     */
    public function setFileReference($fileReference)
    {
        $this->currentFileReference = $fileReference;
    }

    /**
     * Returns translated name of file block
     * @param LanguagesDb|null $language
     * @return string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getName(LanguagesDb $language = null)
    {
        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        $fileTranslate = $this->getFileNameTranslate($language);

        if ($fileTranslate && trim($fileTranslate->name) && CommonModule::isUnderAdmin())
            return $fileTranslate->name;

        if ((!$fileTranslate || !trim($fileTranslate->name)) && CommonModule::isUnderAdmin())
            return $this->program_name;

        if ($fileTranslate && trim($fileTranslate->name) && CommonModule::isUnderDev())
            return $fileTranslate->name . ' (' . $this->program_name .')';

        if ((!$fileTranslate || !trim($fileTranslate->name)) && CommonModule::isUnderDev())
            return 'No translate for file block \'' . $this->program_name . '\'';

        return 'Can`t reach this place if all correct';
    }

    /**
     * Returns translated description of file block
     * @param LanguagesDb|null $language
     * @return string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getDescription(LanguagesDb $language = null)
    {
        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        $fileTranslate = $this->getFileNameTranslate($language);

        if ($fileTranslate)
            return $fileTranslate->description;

        return false;
    }

    /**
     * Returns buffered name translate db
     * @param LanguagesDb $language
     * @return FilesNamesTranslatesDb
     */
    private function getFileNameTranslate(LanguagesDb $language)
    {
        if (!isset($this->fileNamesTranslates[$language->id])) {

            $data = FilesNamesTranslatesDb::find()->where([
                    'common_files_template_id' => $this->id,
                    'common_language_id'       => $language->id,
                ])->one();

            if (!$data) $this->fileNamesTranslates[$language->id] = null;
            else $this->fileNamesTranslates[$language->id] = $data;
        }

        return $this->fileNamesTranslates[$language->id];
    }

    /**
     * @inheritdoc
     */
    public function getEntityQuery()
    {
        if (CommonModule::isUnderDev() || $this->editable) {
            $fileQuery = File::find()
                ->where([
                    'common_files_template_id' => $this->id,
                ])
                ->indexBy('id')
                ->orderBy(['file_order' => SORT_ASC]);

            if ($this->currentFileReference)
                $fileQuery->andWhere([
                    'file_reference' => $this->currentFileReference]);

            return $fileQuery;
        }

        return new ActiveQuery(File::className());
    }

    /**
     * @inheritdoc
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    protected function deleteSequence()
    {
        foreach(FilesNamesTranslatesDb::find()->where([
            'common_files_template_id' => $this->id,
        ])->all() as $fileName)
            if (!$fileName->delete()) return false;

        $fieldTemplateReferences = FieldTemplate::find()->where([
            'field_template_reference' => $this->getFieldTemplateReference()
        ])->all();

        if ($fieldTemplateReferences)
            foreach($fieldTemplateReferences as $fieldTemplate)
                $fieldTemplate->delete();

        $validators = ValidatorDb::find()->where([
            'validator_reference' => $this->validator_reference
        ])->all();

        if ($validators)
            foreach($validators as $validator)
                $validator->delete();

        //TODO: add deleting of files

        return true;
    }

    /**
     * @inheritdoc
     */
    protected static function getNoExistentEntity()
    {
        $file = new File();
        $file->setNoExistent();

        return $file;
    }

    /**
     * Return true if this block has fields
     * @return bool
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function hasFields()
    {
        return !!FieldTemplate::getListQuery($this->field_template_reference)->one();
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getFieldTemplateReference()
    {
        if (!$this->field_template_reference) {
            $this->field_template_reference = FieldTemplate::generateTemplateReference();
            $this->save(false);
        }

        return $this->field_template_reference;
    }

    /**
     * @inheritdoc
     */
    public function getFieldReference()
    {
        return null;
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getConditionTemplateReference()
    {
        if (!$this->condition_template_reference) {
            $this->condition_template_reference = ConditionTemplate::generateTemplateReference();
            $this->save(false);
        }

        return $this->condition_template_reference;
    }

    /**
     * @inheritdoc
     */
    public function getConditionReference()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getOrderQuery()
    {
        return self::find()->where([
            'file_template_reference' => $this->file_template_reference,
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
        return 'file_template_reference';
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
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
