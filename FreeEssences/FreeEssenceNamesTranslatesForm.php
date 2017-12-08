<?php

namespace Iliich246\YicmsCommon\FreeEssences;

use Iliich246\YicmsCommon\Base\AbstractTranslateForm;

/**
 * Class FreeEssenceNamesTranslatesForm
 *
 * @property FreeEssenceNamesTranslatesDb $currentTranslateDb
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FreeEssenceNamesTranslatesForm extends AbstractTranslateForm
{
    /**
     * @var string name of page in current model language
     */
    public $name;
    /**
     * @var string description of page on current model language
     */
    public $description;

    /**
     * @var FreeEssences associated with this model
     */
    private $freeEssence;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Field name on language "' . $this->language->name . '"',
            'description' => 'Description of field on language "' . $this->language->name . '"',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string', 'max' => '50', 'tooLong' => 'Name of free essence must be less than 50 symbols'],
            ['description', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getViewName()
    {
        //return '@yicms-common/Views/translates/field_name_translate';
    }

    /**
     * Sets FreeEssences associated with this object
     * @param FreeEssences $freeEssence
     */
    public function setFreeEssence(FreeEssences $freeEssence)
    {
        $this->freeEssence = $freeEssence;
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
        $this->getCurrentTranslateDb()->common_fields_template_id = $this->fieldTemplate->id;

        return $this->getCurrentTranslateDb()->save();
    }

    /**
     * @inheritdoc
     */
    protected function isCorrectConfigured()
    {
        if (!parent::isCorrectConfigured() || !$this->freeEssence) return false;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentTranslateDb()
    {
        if ($this->currentTranslateDb) return $this->currentTranslateDb;

        $this->currentTranslateDb = FreeEssenceNamesTranslatesDb::find()
            ->where([
                'common_language_id' => $this->language->id,
                'common_free_essence_id' => $this->freeEssence->id,
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
        $this->currentTranslateDb = new FreeEssenceNamesTranslatesDb();
        $this->currentTranslateDb->common_language_id = $this->language->id;
        $this->currentTranslateDb->common_language_id = $this->freeEssence->id;

        return $this->currentTranslateDb->save();
    }
}
