<?php

namespace Iliich246\YicmsCommon\Conditions;

use Iliich246\YicmsCommon\Base\AbstractTranslateForm;

/**
 * Class ConditionValueNamesForm
 *
 * @property ConditionValueNamesDb $currentTranslateDb
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionValueNamesForm extends AbstractTranslateForm
{
    /**
     * @var string name of value in current model language
     */
    public $valueName;
    /**
     * @var string description of value on current model language
     */
    public $valueDescription;
    /**
     * @var ConditionValues associated with this model
     */
    private $conditionValue;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'valueName' => 'Condition value name on language "' . $this->language->name . '"',
            'valueDescription' => 'Condition value description on language "' . $this->language->name . '"',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
             [['valueName', 'valueDescription', ], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getViewName()
    {
        return '@yicms-common/Views/translates/condition_value_name_translate';
    }

    /**
     * Sets ConditionValues associated with this object
     * @param ConditionValues $conditionValues
     */
    public function setConditionValues(ConditionValues $conditionValues)
    {
        $this->conditionValue = $conditionValues;
    }

    public function save()
    {

    }

    /**
     * @inheritdoc
     */
    protected function isCorrectConfigured()
    {
        if (!parent::isCorrectConfigured() || !$this->conditionValue) return false;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentTranslateDb()
    {
        if ($this->currentTranslateDb) return $this->currentTranslateDb;

        $this->currentTranslateDb = ConditionValueNamesDb::find()
            ->where([
                'common_language_id' => $this->language->id,
                'common_condition_template_id' => $this->conditionTemplate->id,
            ])
            ->one();

        if (!$this->currentTranslateDb)
            $this->createTranslateDb();
        else {
            $this->valueName = $this->currentTranslateDb->name;
            $this->valueDescription = $this->currentTranslateDb->description;
        }

        return $this->currentTranslateDb;
    }

    /**
     * @inheritdoc
     */
    protected function createTranslateDb()
    {
        $this->currentTranslateDb = new ConditionValueNamesDb();
        $this->currentTranslateDb->common_language_id = $this->language->id;
        $this->currentTranslateDb->common_condition_value_id = $this->conditionValue->id;

        return $this->currentTranslateDb->save();
    }
}
