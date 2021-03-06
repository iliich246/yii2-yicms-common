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
    /** @var string name of page in current model language */
    public $name;
    /** @var string description of page on current model language */
    public $description;
    /** @var FieldTemplate associated with this model */
    private $fieldTemplate;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'        => 'Field name on language "' . $this->language->name . '"',
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
        return '@yicms-common/Views/translates/field_name_translate';
    }

    /**
     * Sets FieldTemplate associated with this object
     * @param FieldTemplate $fieldTemplate
     */
    public function setFieldTemplate(FieldTemplate $fieldTemplate)
    {
        $this->fieldTemplate = $fieldTemplate;
    }

    /**
     * Saves record in data base
     * @return bool
     */
    public function save()
    {
        $this->getCurrentTranslateDb()->name                      = $this->name;
        $this->getCurrentTranslateDb()->description               = $this->description;
        $this->getCurrentTranslateDb()->common_language_id        = $this->language->id;
        $this->getCurrentTranslateDb()->common_fields_template_id = $this->fieldTemplate->id;

        return $this->getCurrentTranslateDb()->save();
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
                'common_language_id'        => $this->language->id,
                'common_fields_template_id' => $this->fieldTemplate->id,
            ])
            ->one();

        if (!$this->currentTranslateDb)
            $this->createTranslateDb();
        else {
            $this->name        = $this->currentTranslateDb->name;
            $this->description = $this->currentTranslateDb->description;
        }

        return $this->currentTranslateDb;
    }

    /**
     * @inheritdoc
     */
    protected function createTranslateDb()
    {
        $this->currentTranslateDb                            = new FieldsNamesTranslatesDb();
        $this->currentTranslateDb->common_language_id        = $this->language->id;
        $this->currentTranslateDb->common_fields_template_id = $this->fieldTemplate->id;

        return $this->currentTranslateDb->save();
    }
}
