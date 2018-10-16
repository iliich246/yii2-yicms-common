<?php

namespace Iliich246\YicmsCommon\Fields;

use yii\base\Model;
use yii\widgets\ActiveForm;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Base\FictiveInterface;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class FieldsGroup
 *
 * This class implements field group for admin part
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldsGroup extends AbstractGroup
{
    /** @var FieldReferenceInterface|FieldsInterface|FictiveInterface object for current group */
    protected $referenceAble;
    /**
     * @var array of fields program names that`s will not rendered by standard render method
     * this field will not be visible or must be rendered manually
     */
    public $renderFieldsExceptions = [];
    /** @var FieldTemplate[] instances that`s must has translates */
    public $translateAbleFieldTemplates;
    /** @var FieldTemplate[] instances without translates */
    public $singleFieldTemplates;
    /**
     * @var FieldTranslateForm[] of FieldTranslateForm that`s can be handled by Yii Model::validate and load methods in format
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
    public $singleFields = [];

    /**
     * Sets reference able
     * @param FieldReferenceInterface $referenceAble
     */
    public function setFieldsReferenceAble(FieldReferenceInterface $referenceAble)
    {
        $this->referenceAble = $referenceAble;
    }

    /**
     * Returns current fields template reference
     * @return string
     */
    public function getCurrentFieldTemplateReference()
    {
        return $this->referenceAble->getFieldTemplateReference();
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function initialize()
    {
        $fieldTemplatesQuery = FieldTemplate::getListQuery($this->referenceAble->getFieldTemplateReference());

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
                $fieldTranslate->prepareValidators();

                $this->translateForms["$languageKey-$fieldTemplateKey"] = $fieldTranslate;
                $this->translateFormsArray[$languageKey][$fieldTemplateKey] = $fieldTranslate;
            }
        }

        foreach($this->singleFieldTemplates as $singleFieldTemplate) {

            if (!$this->referenceAble->isFictive()) {

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

                $singleField->prepareValidators();
            } else {
                $singleField = new Field();
                $singleField->setFictive();
                $singleField->setTemplate($singleFieldTemplate);

                $singleField->prepareValidators();
            }

            $this->singleFields["$singleFieldTemplate->id"] = $singleField;
        }
    }

    /**
     * This method must be used in pjax actions when do not created FieldReferenceInterface object
     * @param $fieldTemplateReference
     * @param Field $field
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function initializePjax($fieldTemplateReference, Field $field)
    {
        $fieldTemplatesQuery = FieldTemplate::getListQuery($fieldTemplateReference);

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
                $fieldTranslate->scenario = FieldTranslateForm::SCENARIO_LOAD_VIA_PJAX;
                $fieldTranslate->setFieldTemplate($fieldTemplate);
                $fieldTranslate->setLanguage($language);
                $fieldTranslate->setFieldReference($field->field_reference);
                $fieldTranslate->loadFromDb();

                $this->translateForms["$languageKey-$fieldTemplateKey"]     = $fieldTranslate;
                $this->translateFormsArray[$languageKey][$fieldTemplateKey] = $fieldTranslate;
            }
        }

        foreach($this->singleFieldTemplates as $singleFieldTemplate) {
            $singleField = Field::find()->where([
                'field_reference'           => $field->field_reference,
                'common_fields_template_id' => $singleFieldTemplate->id
            ])->one();

            $this->singleFields["$singleFieldTemplate->id"] = $singleField;
        }
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        $success = true;

        if ($this->translateForms)
            $success = Model::validateMultiple($this->translateForms);

        if ($success && $this->singleFields)
            $success = Model::validateMultiple($this->singleFields);

        return $success;
    }

    /**
     * @inheritdoc
     */
    public function load($data)
    {
        $success = true;

        if ($this->translateForms)
            $success = Model::loadMultiple($this->translateForms, $data);

        if ($success && $this->singleFields) {
            $success = Model::loadMultiple($this->singleFields, $data);
        }

        return $success;
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function save()
    {
        $success = true;

        if ($this->translateForms)
            foreach($this->translateForms as $translateForm) {
                if (!$success) return false;
                $success = $translateForm->save();
            }

        if ($success && $this->singleFields)
            foreach($this->singleFields as $singleField) {
                if (!$success) return false;
                $success = $singleField->save();
            }

        return true;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function render(ActiveForm $form, $isModal = false)
    {
        $result = '';

        if ($this->translateFormsArray) {
            $result = FieldsRenderWidget::widget([
                'form'        => $form,
                'fieldsArray' => $this->translateFormsArray,
                'isModal'     => $isModal,
            ]);
        }

        if ($this->singleFields) {
            $result .= FieldsRenderWidget::widget([
                'form'        => $form,
                'fieldsArray' => [$this->singleFields],
                'isModal'     => $isModal
            ]);
        }

        return $result;
    }
}
