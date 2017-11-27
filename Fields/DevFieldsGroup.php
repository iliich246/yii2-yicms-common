<?php

namespace Iliich246\YicmsCommon\Fields;

use yii\base\Model;
use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Languages\Language;
//use Iliich246\YicmsCommon\Fields\FieldNamesTranslatesForm

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

    public function initialize($fieldTemplateReference = null)
    {
        if (!$fieldTemplateReference) {
            $this->fieldTemplate = new FieldTemplate();
            $this->fieldTemplate->field_template_reference = $this->referenceAble->getTemplateFieldReference();
            $this->fieldTemplate->scenario = FieldTemplate::SCENARIO_CREATE;
        } else {
            $this->fieldTemplate = FieldTemplate::findOne($fieldTemplateReference);
            $this->fieldTemplate->scenario = FieldTemplate::SCENARIO_UPDATE;
        }

        $languages = Language::getInstance()->usedLanguages();

        $this->fieldNameTranslates = [];

        foreach($languages as $key => $language) {

            $fieldNameTranslate = new FieldNamesTranslatesForm();
            $fieldNameTranslate->setLanguage($language);
            $fieldNameTranslate->setFieldTemplate($this->fieldTemplate);
            $fieldNameTranslate->loadFromDb();

            $this->fieldNameTranslates[$key] = $fieldNameTranslate;
        }
    }

    public function validate()
    {
        return ($this->fieldTemplate->validate() && Model::validateMultiple($this->fieldNameTranslates));
    }

    public function load($data)
    {
        return ($this->fieldTemplate->load($data) && Model::loadMultiple($this->fieldNameTranslates, $data));
    }

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
            $this->fieldTemplate->getOldAttribute('visible') != $this->fieldTemplate->visible)
            $needSaveFieldTemplate = true;

        if (!$needSaveFieldTemplate &&
            $this->fieldTemplate->getOldAttribute('editable') != $this->fieldTemplate->editable)
            $needSaveFieldTemplate = true;

        if (!$needSaveFieldTemplate &&
            $this->fieldTemplate->getOldAttribute('is_main') != $this->fieldTemplate->is_main)
            $needSaveFieldTemplate = true;

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
    }

    public function render()
    {

    }
}