<?php

namespace Iliich246\YicmsCommon\Files;

use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use yii\validators\SafeValidator;
use yii\validators\RequiredValidator;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AbstractEntity;
use Iliich246\YicmsCommon\Base\SortOrderInterface;
use Iliich246\YicmsCommon\Base\SortOrderTrait;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\FieldsHandler;
use Iliich246\YicmsCommon\Fields\FieldsInterface;
use Iliich246\YicmsCommon\Fields\FieldReferenceInterface;
use Iliich246\YicmsCommon\Validators\ValidatorBuilder;
use Iliich246\YicmsCommon\Validators\ValidatorBuilderInterface;
use Iliich246\YicmsCommon\Validators\ValidatorReferenceInterface;

/**
 * Class File
 *
 * @property integer $id
 * @property string $common_files_template_id
 * @property string $file_reference
 * @property string $field_reference
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
    ValidatorBuilderInterface,
    ValidatorReferenceInterface
{
    use SortOrderTrait;

    /**
     * @var UploadedFile loaded file
     */
    public $file;
    /**
     * @var FieldsHandler instance of field handler object
     */
    private $fieldHandler;
    /**
     * @var ValidatorBuilder instance
     */
    private $validatorBuilder;
    /**
     * @var FileTranslate[] array of buffered translates
     */
    public $fileTranslates;

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
     * Return FilesBlock associated with this file entity
     * @return FilesBlock
     */
    public function getFileBlock()
    {
        return $this->getEntityBlock();
    }

    /**
     * @inheritdoc
     */
    protected static function getReferenceName()
    {
        return 'file_reference';
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
        if ($this->isNonexistent) {
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

        $fileBlock = $this->getFileBlock();
        $fileTranslate = $this->getFileTranslate($language);

        if ($fileBlock->language_type == FilesBlock::LANGUAGE_TYPE_SINGLE)
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
            'fileId' => $this->id,
            'language' => $language->id
        ]);
    }

    /**
     * Return buffered file translate db
     * @param LanguagesDb $language
     * @return FileTranslate
     */
    private function getFileTranslate(LanguagesDb $language)
    {
        if (isset($this->fileTranslates[$language->id])) return $this->fileTranslates[$language->id];

        $this->fileTranslates[$language->id] = FileTranslate::find()->where([
            'common_file_id' => $this->id,
            'common_language_id' => $language->id
        ])->one();

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
    public function getFieldTemplateReference()
    {
        return $this->getFileBlock()->getFieldTemplateReference();
    }

    /**
     * @inheritdoc
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
     * Method config validators for this model
     * @return void
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
}
