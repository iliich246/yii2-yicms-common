<?php

namespace Iliich246\YicmsCommon\Files;

use Iliich246\YicmsCommon\Base\AbstractTranslateForm;

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
    public $value;
    /**
     * @var FilesBlock associated with this model
     */
    private $fieldBlock;
    /**
     * @var File
     */
    private $file;

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
                'value'
            ],
            self::SCENARIO_UPDATE => [
                'value'
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
            ['value', 'string'],
        ];
    }

    public function setFile(File $file)
    {
        $this->file = $file;
    }

    private function getFile()
    {
        return $this->file;
    }

    public function setFileBlock(FilesBlock $filesBlock)
    {
        $this->fieldBlock = $filesBlock;
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
