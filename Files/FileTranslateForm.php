<?php

namespace Iliich246\YicmsCommon\Files;

use Iliich246\YicmsCommon\Base\AbstractTranslateForm;
use Iliich246\YicmsCommon\CommonModule;

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

    public $translatedFile;
    /**
     * @var FilesBlock associated with this model
     */
    private $fieldBlock;
    /**
     * @var File
     */
    private $file_;

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
        //CommonModule::getInstance()->yicmsLocation . '/Common/Views/files/file-load.php
        return CommonModule::getInstance()->yicmsLocation . '/Common/Views/files/file-translate';
    }
//
//    public function setFile(File $file)
//    {
//        $this->file = $file;
//    }
//
//    private function getFile()
//    {
//        return $this->file;
//    }

    public function setFileBlock(FilesBlock $filesBlock)
    {
        $this->fieldBlock = $filesBlock;
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

    }

    /**
     * @inheritdoc
     */
    protected function createTranslateDb()
    {
        $this->currentTranslateDb = new FileTranslate();
        $this->currentTranslateDb->common_language_id = $this->language->id;
        $this->currentTranslateDb->common_file_id = $this->getFile()->id;
        $this->currentTranslateDb->system_name = null;
        $this->currentTranslateDb->original_name = null;
        $this->currentTranslateDb->filename = null;
        $this->currentTranslateDb->size = null;
        $this->currentTranslateDb->type = null;

        return $this->currentTranslateDb->save();
    }

}
