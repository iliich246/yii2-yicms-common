<?php

namespace Iliich246\YicmsCommon\Conditions;

use yii\base\Model;
use yii\widgets\ActiveForm;
use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class DevConditionsGroup
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DevConditionsGroup extends AbstractGroup
{
    /** @var string conditionTemplateReference value for current group */
    protected $conditionTemplateReference;
    /** @var ConditionTemplate current condition block template with group is working (create or update) */
    public $conditionTemplate;
    /** @var ConditionNamesTranslatesForm[] */
    public $conditionNameTranslates;
    /** @var bool indicate that data in this group was saved in this action */
    public $justSaved = false;

    /**
     * Sets conditionTemplateReference
     * @param $conditionTemplateReference
     */
    public function setConditionsTemplateReference($conditionTemplateReference)
    {
        $this->conditionTemplateReference = $conditionTemplateReference;
    }

    /**
     * @inheritdoc
     * @throws CommonException
     */
    public function initialize($conditionsBlockId = null)
    {
        if (!$conditionsBlockId) {
            $this->conditionTemplate                               = new ConditionTemplate();
            $this->conditionTemplate->condition_template_reference = $this->conditionTemplateReference;
            $this->conditionTemplate->scenario                     = ConditionTemplate::SCENARIO_CREATE;
            $this->scenario                                        = self::SCENARIO_CREATE;
        } else {
            $this->conditionTemplate = ConditionTemplate::findOne($conditionsBlockId);

            if (!$this->conditionTemplate) throw new CommonException("Wrong conditionsBlockId = $conditionsBlockId");

            $this->conditionTemplate->scenario = ConditionTemplate::SCENARIO_UPDATE;
            $this->scenario                    = self::SCENARIO_UPDATE;
        }

        $languages = Language::getInstance()->usedLanguages();

        $this->conditionNameTranslates = [];

        foreach($languages as $key => $language) {

            $conditionNameTranslates = new ConditionNamesTranslatesForm();
            $conditionNameTranslates->setLanguage($language);
            $conditionNameTranslates->setConditionTemplate($this->conditionTemplate);

            if (!$this->conditionTemplate->isNewRecord)
                $conditionNameTranslates->loadFromDb();

            $this->conditionNameTranslates[$key] = $conditionNameTranslates;
        }
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return ($this->conditionTemplate->validate() && Model::validateMultiple($this->conditionNameTranslates));
    }

    /**
     * @inheritdoc
     */
    public function load($data)
    {
        return $this->conditionTemplate->load($data) && Model::loadMultiple($this->conditionNameTranslates, $data);
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        $needSaveConditionBlock = false;

        if (!$needSaveConditionBlock &&
            $this->conditionTemplate->getOldAttribute('program_name') != $this->conditionTemplate->program_name)
            $needSaveConditionBlock = true;

        if (!$needSaveConditionBlock &&
            $this->conditionTemplate->getOldAttribute('editable') != $this->conditionTemplate->editable)
            $needSaveConditionBlock = true;

        if (!$needSaveConditionBlock &&
            $this->conditionTemplate->getOldAttribute('type') != $this->conditionTemplate->type)
            $needSaveConditionBlock = true;

        $success = true;

        if ($needSaveConditionBlock)
            $success = $this->conditionTemplate->save(false);

        if (!$success) return false;

        /** @var ConditionNamesTranslatesForm $fieldNameTranslate */
        foreach($this->conditionNameTranslates as $conditionNameTranslate) {

            $needSaveConditionTemplateName = false;

            if (!$needSaveConditionTemplateName &&
                $conditionNameTranslate->name != $conditionNameTranslate->getCurrentTranslateDb()->name)
                $needSaveConditionTemplateName = true;

            if (!$needSaveConditionTemplateName &&
                $conditionNameTranslate->description != $conditionNameTranslate->getCurrentTranslateDb()->description)
                $needSaveConditionTemplateName = true;

            if ($needSaveConditionTemplateName)
                $conditionNameTranslate->save();
        }

        $this->justSaved = true;

        //TODO: makes error handling
        return true;
        
    }

    /**
     * @inheritdoc
     * @throws CommonException
     */
    public function render(ActiveForm $form)
    {
        throw new CommonException('Not implemented for developer conditions (not necessary)');
    }
}
