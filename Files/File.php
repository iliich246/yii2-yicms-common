<?php

namespace Iliich246\YicmsCommon\Files;

use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use Iliich246\YicmsCommon\Base\AbstractEntity;
use Iliich246\YicmsCommon\Base\SortOrderInterface;
use Iliich246\YicmsCommon\Base\SortOrderTrait;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;

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
class File extends AbstractEntity implements SortOrderInterface
{
    use SortOrderTrait;

    /**
     * @var UploadedFile loaded file
     */
    public $file;

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

    public function rules()
    {
        return [
            //
            ['file', 'file', 'maxSize' => 10000000, 'uploadRequired' => 'Need epta!', 'skipOnEmpty' => true],
            //['file', 'required'],
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
        return $this->entityBlock;
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
     * @param LanguagesDb|false $language
     * @return int
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getFileName(LanguagesDb $language = null)
    {
        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        $fileTranslate = $this->getFileTranslate($language);

        if ($fileTranslate && trim($fileTranslate->filename))
            return $fileTranslate->filename;

        if ($this->getFileBlock()->language_type == FilesBlock::LANGUAGE_TYPE_SINGLE)
            return $this->original_name;

        if ($fileTranslate)
            return $fileTranslate->original_name;

        return false;
    }

    /**
     * Return buffered file translate db
     * @param LanguagesDb $language
     * @return FileTranslate
     */
    private function getFileTranslate(LanguagesDb $language)
    {
        if ($this->fileTranslates[$language->id]) return $this->fileTranslates[$language->id];

        $this->fileTranslates[$language->id] = FileTranslate::find()->where([
            'common_file_id' => $this->id,
            'common_language_id' => $language->id
        ])->one();

        return $this->fileTranslates[$language->id];
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
