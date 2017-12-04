<?php

namespace Iliich246\YicmsCommon\Fields;

use yii\base\Model;
use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class DevFieldsGroup
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DevFieldsGroup extends AbstractGroup
{
    /** @var FieldReferenceInterface  */
    protected $referenceAble;

    /**
     * @var FieldTemplate
     */
    public $fieldTemplate;

    /**
     * @var FieldNamesTranslatesForm[]
     */
    public $fieldNameTranslates;

    /**
     * @param FieldReferenceInterface $referenceAble
     */
    public function setFieldsReferenceAble(FieldReferenceInterface $referenceAble)
    {
        $this->referenceAble = $referenceAble;
    }

    /**
     * @inheritdoc
     */
    public function initialize($fieldTemplateReference = null)
    {
        if (!$fieldTemplateReference) {
            $this->fieldTemplate = new FieldTemplate();
            $this->fieldTemplate->field_template_reference = $this->referenceAble->getTemplateFieldReference();
            $this->fieldTemplate->scenario = FieldTemplate::SCENARIO_CREATE;
            $this->scenario = self::SCENARIO_CREATE;

        } else {
            $this->fieldTemplate = FieldTemplate::findOne($fieldTemplateReference);
            $this->fieldTemplate->scenario = FieldTemplate::SCENARIO_UPDATE;
            $this->scenario = self::SCENARIO_UPDATE;
        }

        //throw new Exception(print_r($this->fieldTemplate, true));

        $languages = Language::getInstance()->usedLanguages();

        $this->fieldNameTranslates = [];

        foreach($languages as $key => $language) {

            $fieldNameTranslate = new FieldNamesTranslatesForm();
            $fieldNameTranslate->setLanguage($language);
            $fieldNameTranslate->setFieldTemplate($this->fieldTemplate);

            if ($this->fieldTemplate->id)
                $fieldNameTranslate->loadFromDb();

            $this->fieldNameTranslates[$key] = $fieldNameTranslate;
        }
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return ($this->fieldTemplate->validate() && Model::validateMultiple($this->fieldNameTranslates));
    }

    /**
     * @inheritdoc
     */
    public function load($data)
    {
        return ($this->fieldTemplate->load($data) && Model::loadMultiple($this->fieldNameTranslates, $data));
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        $needSaveFieldTemplate = false;

        if (!$needSaveFieldTemplate &&
            $this->fieldTemplate->getOldAttribute('program_name') != $this->fieldTemplate->program_name)
            $needSaveFieldTemplate = true;

        if (!$needSaveFieldTemplate &&
            $this->fieldTemplate->getOldAttribute('type') != $this->fieldTemplate->type)
            $needSaveFieldTemplate = true;

        if (!$needSaveFieldTemplate &&
            $this->fieldTemplate->getOldAttribute('language_type') != $this->fieldTemplate->language_type)
            $needSaveFieldTemplate = true;

        if (!$needSaveFieldTemplate &&
            $this->fieldTemplate->getOldAttribute('visible') != $this->fieldTemplate->visible)
            $needSaveFieldTemplate = true;

        if (!$needSaveFieldTemplate &&
            $this->fieldTemplate->getOldAttribute('editable') != $this->fieldTemplate->editable)
            $needSaveFieldTemplate = true;

        if (!$needSaveFieldTemplate &&
            $this->fieldTemplate->getOldAttribute('is_main') != $this->fieldTemplate->is_main)
            $needSaveFieldTemplate = true;

        if ($this->fieldTemplate->getOldAttribute('language_type') != $this->fieldTemplate->language_type)
            $this->fieldTemplate->field_order = $this->fieldTemplate->maxOrder();

        if ($needSaveFieldTemplate)
            $this->fieldTemplate->save(false);

        /** @var FieldNamesTranslatesForm $fieldNameTranslate */
        foreach($this->fieldNameTranslates as $fieldNameTranslate) {

            $needSaveFieldTemplateName = false;

            if (!$needSaveFieldTemplateName &&
                $fieldNameTranslate->name != $fieldNameTranslate->getCurrentTranslateDb()->name)
                $needSaveFieldTemplateName = true;

            if (!$needSaveFieldTemplateName &&
                $fieldNameTranslate->description != $fieldNameTranslate->getCurrentTranslateDb()->description)
                $needSaveFieldTemplateName = true;

            if ($needSaveFieldTemplateName)
                $fieldNameTranslate->save();
        }

        //TODO: makes error handling
        return true;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {

    }
}
