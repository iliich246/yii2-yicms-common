<?php

namespace Iliich246\YicmsCommon\Files;

use yii\web\UploadedFile;
use Iliich246\YicmsCommon\Base\AbstractTranslateForm;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Fields\FieldTranslate;

/**
 * Class FileTranslateForm
 *
 * @property FileTranslate $currentTranslateDb
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FileTranslateForm extends AbstractTranslateForm
{
    /**
     * @var string value of translated field
     */
    public $filename;
    /**
     * @var UploadedFile
     */
    public $translatedFile;
    /**
     * @var FilesBlock associated with this model
     */
    private $fieldBlock;
    /**
     * @var File associated instance
     */
    private $fileEntity;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'value' => $this->getFileLabelName()
        ];
    }

    public function getFileLabelName()
    {
        return 'upload file';
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'filename', 'translatedFile'
            ],
            self::SCENARIO_UPDATE => [
                'filename', 'translatedFile'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        //TODO: makes validators
        return [
            ['filename', 'string'],
            [['translatedFile'], 'file'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getViewName()
    {
        return CommonModule::getInstance()->yicmsLocation . '/Common/Views/files/file-translate';
    }

    /**
     * Sets FilesBlock
     * @param FilesBlock $filesBlock
     */
    public function setFileBlock(FilesBlock $filesBlock)
    {
        $this->fieldBlock = $filesBlock;
    }

    /**
     * Sets File
     * @param File $fileEntity
     */
    public function setFileEntity(File $fileEntity)
    {
        $this->fileEntity = $fileEntity;
    }

    /**
     * File getter
     * @return File
     */
    public function getFileEntity()
    {
        return $this->fileEntity;
    }

    /**
     * @inheritdoc
     */
    public function getKey()
    {
        return $this->language->id . '-' . $this->fieldBlock->id;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentTranslateDb()
    {
        if ($this->currentTranslateDb) return $this->currentTranslateDb;

        $this->currentTranslateDb = FileTranslate::find()->where([
            'common_file_id' =>  $this->fileEntity->id,
            'common_language_id' => $this->language->id,
        ])->one();

        if (!$this->currentTranslateDb)
            $this->createTranslateDb();
        else {
            $this->filename = $this->currentTranslateDb->filename;
        }

        return $this->currentTranslateDb;
    }

    /**
     * @inheritdoc
     */
    protected function createTranslateDb()
    {
        $this->currentTranslateDb = new FileTranslate();
        $this->currentTranslateDb->common_language_id = $this->language->id;
        $this->currentTranslateDb->common_file_id = $this->fileEntity->id;
        $this->currentTranslateDb->system_name = null;
        $this->currentTranslateDb->original_name = null;
        $this->currentTranslateDb->filename = null;
        $this->currentTranslateDb->size = null;
        $this->currentTranslateDb->type = null;
        $this->currentTranslateDb->editable = true;
        $this->currentTranslateDb->visible = true;

        return $this->currentTranslateDb->save();
    }

}
