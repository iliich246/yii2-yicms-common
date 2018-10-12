<?php

namespace Iliich246\YicmsCommon\Conditions;

use Iliich246\YicmsCommon\Base\AbstractTranslateForm;

/**
 * Class ConditionNamesTranslatesForm
 *
 * @property ConditionsNamesTranslatesDb $currentTranslateDb
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionNamesTranslatesForm extends AbstractTranslateForm
{
    /** @var string name of page in current model language */
    public $name;
    /** @var string description of page on current model language */
    public $description;
    /** @var ConditionTemplate associated with this model */
    private $conditionTemplate;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Condition name on language "' . $this->language->name . '"',
            'description' => 'Description of condition on language "' . $this->language->name . '"',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string', 'max' => '50', 'tooLong' => 'Name of condition must be less than 50 symbols'],
            ['description', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getViewName()
    {
        return '@yicms-common/Views/translates/condition_name_translate';
    }

    /**
     * Sets ConditionTemplate associated with this object
     * @param ConditionTemplate $conditionTemplate
     */
    public function setConditionTemplate(ConditionTemplate $conditionTemplate)
    {
        $this->conditionTemplate = $conditionTemplate;
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
        $this->getCurrentTranslateDb()->common_condition_template_id = $this->conditionTemplate->id;

        return $this->getCurrentTranslateDb()->save();
    }

    /**
     * @inheritdoc
     */
    protected function isCorrectConfigured()
    {
        if (!parent::isCorrectConfigured() || !$this->conditionTemplate) return false;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentTranslateDb()
    {
        if ($this->currentTranslateDb) return $this->currentTranslateDb;

        $this->currentTranslateDb = ConditionsNamesTranslatesDb::find()
            ->where([
                'common_language_id' => $this->language->id,
                'common_condition_template_id' => $this->conditionTemplate->id,
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
        $this->currentTranslateDb = new ConditionsNamesTranslatesDb();
        $this->currentTranslateDb->common_language_id = $this->language->id;
        $this->currentTranslateDb->common_condition_template_id = $this->conditionTemplate->id;

        return $this->currentTranslateDb->save();
    }
}
