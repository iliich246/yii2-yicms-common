<?php

namespace Iliich246\YicmsCommon\Fields;

use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\FictiveInterface;
use Iliich246\YicmsCommon\Base\AbstractTranslateForm;
use Iliich246\YicmsCommon\Validators\ValidatorBuilder;
use Iliich246\YicmsCommon\Validators\ValidatorBuilderInterface;
use Iliich246\YicmsCommon\Validators\ValidatorReferenceInterface;

/**
 * Class FieldTranslateForm
 *
 * @property FieldTranslate $currentTranslateDb
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldTranslateForm extends AbstractTranslateForm implements
    FictiveInterface,
    FieldRenderInterface,
    ValidatorBuilderInterface,
    ValidatorReferenceInterface
{
    const SCENARIO_LOAD_VIA_PJAX = 0x02;

    /** @var string value of translated field */
    public $value;
    /** @var FieldTemplate associated with this model */
    private $fieldTemplate;
    /** @var Field instance */
    private $field;
    /** @var FieldsInterface|FieldReferenceInterface|FictiveInterface */
    private $fieldAble;
    /** @var string value of field reference */
    private $fieldReference;
    /** @var ValidatorBuilder instance */
    private $validatorBuilder;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'value' => $this->getFieldName()
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'value'
            ],
            self::SCENARIO_UPDATE => [
                'value'
            ],
            self::SCENARIO_LOAD_VIA_PJAX => [
                'value'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        //TODO: makes validators
        return [
            ['value', 'string'],
        ];
    }


    /**
     * @inheritdoc
     */
    public static function getViewName()
    {
        //return '@yicms-common/Views/translates/field_name_translate';
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
     * Sets FieldsInterface object
     * @param FieldReferenceInterface $fieldAble
     */
    public function setFieldAble(FieldReferenceInterface $fieldAble)
    {
        $this->fieldAble = $fieldAble;
    }

    /**
     * Sets field (used only for pjax actions)
     * @param string $fieldReference
     */
    public function setFieldReference($fieldReference)
    {
        $this->fieldReference = $fieldReference;
    }

    /**
     * Saves record in data base
     * @return bool
     */
    public function save()
    {
        /** @var FieldTranslate $translate */
        $translate = $this->getCurrentTranslateDb();
        $translate->value = $this->value;

        return $translate->save(false);

    }

    /**
     * @inheritdoc
     */
    protected function isCorrectConfigured()
    {
        if (!parent::isCorrectConfigured() || !$this->fieldTemplate || !($this->fieldAble || $this->fieldReference)) return false;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentTranslateDb()
    {
        if ($this->currentTranslateDb) return $this->currentTranslateDb;

        if ($this->scenario == self::SCENARIO_LOAD_VIA_PJAX) return $this->getCurrentTranslatePjax();

        if ($this->fieldAble->isFictive()) return [];

        $this->currentTranslateDb = FieldTranslate::find()
            ->where([
                'common_fields_represent_id' => $this->getField()->id,
                'common_language_id' => $this->language->id
        ])->one();

        if (!$this->currentTranslateDb)
            $this->createTranslateDb();
        else {
            $this->value = $this->currentTranslateDb->value;
        }

        return $this->currentTranslateDb;
    }

    /**
     * Variant of translate loader for pjax using of this class
     * @return array|FieldTranslate|null|\yii\db\ActiveRecord
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getCurrentTranslatePjax()
    {
        $this->field = Field::getInstance($this->fieldTemplate->field_template_reference,
            $this->fieldReference, $this->fieldTemplate->program_name);

        $this->currentTranslateDb = FieldTranslate::find()
            ->where([
                'common_fields_represent_id' => $this->field->id,
                'common_language_id' => $this->language->id
            ])->one();

        $this->value = $this->currentTranslateDb->value;

        return  $this->currentTranslateDb;
    }

    /**
     * Returns Field associated with this form
     * @return Field
     */
    private function getField()
    {
        if ($this->field) return $this->field;

        if (!($field = $this->fieldAble->getField($this->fieldTemplate->program_name))) {

            $field = new Field();
            $field->common_fields_template_id = $this->fieldTemplate->id;
            $field->field_reference = $this->fieldAble->getFieldReference();
            $field->visible = true;
            $field->editable = true;

            $field->save(false);
        }

        $this->field = $field;

        return $this->field;
    }

    /**
     * @inheritdoc
     */
    protected function createTranslateDb()
    {
        $this->currentTranslateDb = new FieldTranslate();
        $this->currentTranslateDb->common_language_id = $this->language->id;
        $this->currentTranslateDb->common_fields_represent_id = $this->getField()->id;
        $this->currentTranslateDb->value = null;

        return $this->currentTranslateDb->save();
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->fieldTemplate->type;
    }

    /**
     * @inheritdoc
     */
    public function getLanguageType()
    {
        return $this->fieldTemplate->language_type;
    }

    /**
     * @inheritdoc
     */
    public function isEditable()
    {
        return (bool)$this->getField()->editable;
    }

    /**
     * @inheritdoc
     */
    public function isVisible()
    {
        return (bool)$this->getField()->visible;
    }

    /**
     * @inheritdoc
     */
    public function getKey()
    {
        return '[' . $this->language->id . '-' . $this->fieldTemplate->id . ']value';
    }

    /**
     * @inheritdoc
     */
    public function getTemplate()
    {
        return $this->fieldTemplate;
    }

    /**
     * @inheritdoc
     */
    public function getFieldId()
    {
        return $this->getField()->id;
    }

    /**
     * Method config validators for this model
     */
    public function prepareValidators()
    {
        $validators = $this->getValidatorBuilder()->build();

        if (!$validators) return;

        foreach($validators as $validator)
            $this->validators[] = $validator;
    }

    /**
     * @inheritdoc
     */
    public function getFieldName()
    {
        $fieldName = $this->getField()->getFieldNameTranslate($this->language);

        if ($fieldName && trim($fieldName->name) && CommonModule::isUnderAdmin()) return $fieldName->name;

        if ((!$fieldName || !trim($fieldName->name)) && CommonModule::isUnderAdmin())
            return $this->fieldTemplate->program_name;

        if ($fieldName && trim($fieldName->name) && CommonModule::isUnderDev())
            return $fieldName->name . ' (' . $this->fieldTemplate->program_name .')';

        if ((!$fieldName || !trim($fieldName->name)) && CommonModule::isUnderDev())
            return 'No translate for field \'' . $this->fieldTemplate->program_name . '\'';

        return 'Can`t reach this place if all correct';
    }

    /**
     * @inheritdoc
     */
    public function getFieldDescription()
    {
        $fieldName = $this->getField()->getFieldNameTranslate($this->language);

        if ($fieldName)
            return $fieldName->description;

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getValidatorBuilder()
    {
        if ($this->validatorBuilder) return $this->validatorBuilder;

        $this->validatorBuilder = new ValidatorBuilder();
        $this->validatorBuilder->setReferenceAble($this);

        return $this->validatorBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getValidatorReference()
    {
        $fieldTemplate = $this->getTemplate();

        if (!$fieldTemplate->validator_reference) {
            $fieldTemplate->validator_reference = ValidatorBuilder::generateValidatorReference();
            $fieldTemplate->scenario = FieldTemplate::SCENARIO_UPDATE;
            $fieldTemplate->save(false);
        }

        return  $fieldTemplate->validator_reference;
    }

    /**
     * @inheritdoc
     */
    public function setFictive()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function clearFictive()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isFictive()
    {
        if (!$this->fieldAble) return false;

        return $this->fieldAble->isFictive();
    }
}
