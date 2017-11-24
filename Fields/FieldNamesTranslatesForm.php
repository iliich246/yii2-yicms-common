<?php

namespace Iliich246\YicmsCommon\Fields;

use Iliich246\YicmsCommon\Base\AbstractTranslateForm;

/**
 * Class FieldNamesTranslatesForm
 *
 * @property FieldsNamesTranslatesDb $currentTranslateDb
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldNamesTranslatesForm extends AbstractTranslateForm
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
     * @var FieldTemplate associated with this model
     */
    private $fieldTemplate;

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
            ['name', 'string', 'max' => '50', 'tooLong' => 'Name of field must be less than 50 symbols'],
            ['description', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getViewName()
    {
        return '@yicms-pages/Views/translates/field_name_translate';
    }

    /**
     * @inheritdoc
     */
    protected function isCorrectConfigured()
    {
        if (!parent::isCorrectConfigured() || !$this->fieldTemplate) return false;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentTranslateDb()
    {
        if ($this->currentTranslateDb) return $this->currentTranslateDb;

        $this->currentTranslateDb = FieldsNamesTranslatesDb::find()
            ->where([
                'common_language_id' => $this->language->id,
                'common_fields_template_id' => $this->fieldTemplate->id,
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
        $this->currentTranslateDb = new FieldsNamesTranslatesDb();
        $this->currentTranslateDb->common_language_id = $this->language->id;
        $this->currentTranslateDb->common_fields_template_id = $this->fieldTemplate->id;

        return $this->currentTranslateDb->save();
    }
}
