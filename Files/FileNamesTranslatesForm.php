<?php

namespace Iliich246\YicmsCommon\Files;

use Iliich246\YicmsCommon\Base\AbstractTranslateForm;

/**
 * Class FileNamesTranslatesForm
 *
 * @property FilesNamesTranslatesDb $currentTranslateDb
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FileNamesTranslatesForm extends AbstractTranslateForm
{
    /** @var string name of file block template in current model language */
    public $name;
    /** @var string description of file block template on current model language */
    public $description;
    /** @var FilesBlock associated with this model */
    private $fileBlockTemplate;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'File block name on language "' . $this->language->name . '"',
            'description' => 'Description of file block on language "' . $this->language->name . '"',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string', 'max' => '50', 'tooLong' => 'Name of field must be less than 50 symbols'],
            ['description', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getViewName()
    {
        return '@yicms-common/Views/translates/files_names_translate';
    }

    /**
     * Sets fileBlockTemplate associated with this object
     * @param FilesBlock $fileBlockTemplate
     */
    public function setFilesBlockTemplate(FilesBlock $fileBlockTemplate)
    {
        $this->fileBlockTemplate = $fileBlockTemplate;
    }

    /**
     * Saves record in data base
     * @return bool
     */
    public function save()
    {
        $this->getCurrentTranslateDb()->name = $this->name;
        $this->getCurrentTranslateDb()->description = $this->description;
        $this->getCurrentTranslateDb()->common_language_id = $this->language->id;
        $this->getCurrentTranslateDb()->common_files_template_id = $this->fileBlockTemplate->id;

        return $this->getCurrentTranslateDb()->save();
    }

    /**
     * @inheritdoc
     */
    protected function isCorrectConfigured()
    {
        if (!parent::isCorrectConfigured() || !$this->fileBlockTemplate) return false;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentTranslateDb()
    {
        if ($this->currentTranslateDb) return $this->currentTranslateDb;

        $this->currentTranslateDb = FilesNamesTranslatesDb::find()
            ->where([
                'common_language_id' => $this->language->id,
                'common_files_template_id' => $this->fileBlockTemplate->id,
            ])
            ->one();

        if (!$this->currentTranslateDb)
            $this->createTranslateDb();
        else {
            $this->name = $this->currentTranslateDb->name;
            $this->description = $this->currentTranslateDb->description;
        }

        return $this->currentTranslateDb;
    }

    /**
     * @inheritdoc
     */
    protected function createTranslateDb()
    {
        $this->currentTranslateDb = new FilesNamesTranslatesDb();
        $this->currentTranslateDb->common_language_id = $this->language->id;
        $this->currentTranslateDb->common_files_template_id = $this->fileBlockTemplate->id;

        return $this->currentTranslateDb->save();
    }
}
