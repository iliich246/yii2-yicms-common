<?php

namespace Iliich246\YicmsCommon\Fields;

use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Languages\Language;
use yii\base\Model;
use yii\widgets\ActiveForm;

/**
 * Class FieldsGroup
 *
 * @package Iliich246\YicmsCommon\Fields
 */
class FieldsGroup extends AbstractGroup
{
    /** @var FieldReferenceInterface|FieldsInterface  */
    protected $referenceAble;

    /**
     * @var array of fields program names that`s will not rendered by standard render method
     * this field will not be visible or must be rendered manually
     */
    public $renderFieldsExceptions = [];

    /** @var FieldTemplate[] instances that`s must has translates  */
    public $translateAbleFieldTemplates;
    /** @var FieldTemplate[] instances without translates  */
    public $singleFieldTemplates;

    /** @var FieldTranslateForm[] of FieldTranslateForm that`s can be handled by Yii Model::validate and load methods in format
     * [key1] => FieldTranslateForm1
     * ...
     * [keyN] => FieldTranslateFormN
     *
     * Where key is value that`s used for associate models with data of form in POST array
     */
    public $translateForms = [];

    /**
     * @var array of FieldTranslateForms comfortable for traversable by yicms methods in format
     * [languageId1] => [
     *      [fieldTemplateId1] => FieldTranslateForm1_1
     *      [fieldTemplateId2] => FieldTranslateForm1_2
     *      ...
     * ]
     * [languageId2] => [
     *      [fieldTemplateId1] => FieldTranslateForm2_1
     *      [fieldTemplateId2] => FieldTranslateForm2_2
     *      ...
     * ]
     * ...
     */
    public $translateFormsArray = [];

    /** @var Field[] array of fields without translates */
    public $singleFields;

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
        $fieldTemplatesQuery = FieldTemplate::getListQuery($this->referenceAble->getTemplateFieldReference());

        if (!CommonModule::isUnderDev()) $fieldTemplatesQuery->andWhere([
            'editable' => true,
        ]);

        $fieldTemplatesQuery->orderBy([
            FieldTemplate::getOrderFieldName() => SORT_ASC
        ])->indexBy('id');

        $templateQuery = clone($fieldTemplatesQuery);

        /** @var FieldTemplate $fieldTemplates */
        $this->translateAbleFieldTemplates = $fieldTemplatesQuery->andWhere([
            'language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE
        ])->all();

        $this->singleFieldTemplates = $templateQuery->andWhere([
            'language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE
        ])->all();

        $languages = Language::getInstance()->usedLanguages();

        foreach($languages as $languageKey => $language) {
            foreach($this->translateAbleFieldTemplates as $fieldTemplateKey=>$fieldTemplate) {

                $fieldTranslate = new FieldTranslateForm();
                $fieldTranslate->scenario = FieldTranslateForm::SCENARIO_UPDATE;
                $fieldTranslate->setFieldTemplate($fieldTemplate);
                $fieldTranslate->setLanguage($language);
                $fieldTranslate->setFieldAble($this->referenceAble);
                $fieldTranslate->loadFromDb();

                $this->translateForms["$languageKey-$fieldTemplateKey"] = $fieldTranslate;
                $this->translateFormsArray[$languageKey][$fieldTemplateKey] = $fieldTranslate;
            }
        }

        foreach($this->singleFieldTemplates as $singleFieldTemplate) {
            $singleField = Field::find()->where([
                'field_reference' => $this->referenceAble->getFieldReference(),
                'common_fields_template_id' => $singleFieldTemplate->id
            ])->one();

            if (!$singleField) {
                $singleField = new Field();
                $singleField->field_reference = $this->referenceAble->getFieldReference();
                $singleField->common_fields_template_id = $singleFieldTemplate->id;
                $singleField->value = null;
                $singleField->visible = true;
                $singleField->editable = true;

                $singleField->save();
            }

            $this->singleFields[$singleFieldTemplate->id] = $singleField;
        }
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return (Model::validateMultiple($this->translateForms) && Model::validateMultiple($this->singleFields));
    }

    /**
     * @inheritdoc
     */
    public function load($data)
    {
        return (Model::loadMultiple($this->translateForms, $data) && Model::loadMultiple($this->singleFields, $data));
    }

    /**
     * @inheritdoc
     */
    public function save()
    {

        foreach($this->translateForms as $translateForm) {
            $translateForm->save();
        }

        foreach($this->singleFields as $singleField) {
            $singleField->save(false);
        }
    }

    /**
     * @inheritdoc
     */
    public function render(ActiveForm $form)
    {
        $result = FieldsRenderWidget::widget([
            'form' => $form,
            'fieldsArray' => $this->translateFormsArray
        ]);

        $result .= FieldsRenderWidget::widget([
            'form' => $form,
            'fieldsArray' => [$this->singleFields]
        ]);

        return $result;
    }
}
