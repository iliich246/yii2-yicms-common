<?php

namespace Iliich246\YicmsCommon\Fields;

use yii\base\Model;
use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Languages\Language;
use yii\widgets\ActiveForm;

/**
 * Class DevFieldsGroup
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DevFieldsGroup extends AbstractGroup
{
    /**
     * @var integer fieldTemplateReference value for current group
     */
    protected $fieldTemplateReference;

    /**
     * @var FieldTemplate current field template with group is working (create or update)
     */
    public $fieldTemplate;

    /**
     * @var FieldNamesTranslatesForm[]
     */
    public $fieldNameTranslates;

    /**
     * @var FieldTemplate[] array associated with object with current $fieldTemplateReference
     */
    public $fieldTemplatesTranslatable;

    /**
     * @var FieldTemplate[] array associated with object with current $fieldTemplateReference
     */
    public $fieldTemplatesSingle;

    /**
     * @param integer $fieldTemplateReference
     */
    public function setFieldTemplateReference($fieldTemplateReference)
    {
        $this->fieldTemplateReference = $fieldTemplateReference;
    }

    /**
     * Sets update scenario
     */
    public function setUpdateScenario()
    {
        $this->scenario = self::SCENARIO_UPDATE;
        $this->fieldTemplate->scenario = FieldTemplate::SCENARIO_UPDATE;
    }

    /**
     * @inheritdoc
     */
    public function initialize($fieldTemplateId = null)
    {
        if (!$fieldTemplateId) {
            $this->fieldTemplate = new FieldTemplate();
            $this->fieldTemplate->field_template_reference = $this->fieldTemplateReference;
            $this->fieldTemplate->scenario = FieldTemplate::SCENARIO_CREATE;
            $this->scenario = self::SCENARIO_CREATE;
        } else {
            $this->fieldTemplate = FieldTemplate::findOne($fieldTemplateId);

            if (!$this->fieldTemplate) throw new CommonException("Wrong fieldTemplateId = $fieldTemplateId");

            $this->fieldTemplate->scenario = FieldTemplate::SCENARIO_UPDATE;
            $this->scenario = self::SCENARIO_UPDATE;
        }

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

//        $this->fieldTemplatesTranslatable = FieldTemplate::getListQuery($this->fieldTemplateReference)
//                            ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE])
//                            ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
//                            ->all();
//
//        $fieldTemplatesSingle = FieldTemplate::getListQuery($this->fieldTemplateReference)
//                            ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE])
//                            ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
//                            ->all();
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
        return $this->fieldTemplate->load($data) && Model::loadMultiple($this->fieldNameTranslates, $data);
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
    public function render(ActiveForm $form)
    {
        throw new CommonException('Not implemented for developer fields group (not necessary)');
    }
}
