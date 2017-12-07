<?php

namespace Iliich246\YicmsCommon\Fields;

use Iliich246\YicmsCommon\Base\AbstractTranslateForm;

/**
 * Class FieldTranslateForm
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldTranslateForm extends AbstractTranslateForm
{
    /**
     * @var string value of translated field
     */
    public $value;

    /**
     * @var FieldTemplate associated with this model
     */
    private $fieldTemplate;

    /**
     * @var Field
     */
    private $field;

    /**
     * @var FieldTranslate
     */
    private $fieldTranslate;

    /**
     * @var FieldsInterface|FieldReferenceInterface
     */
    private $fieldAble;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'value' => 'Makes correct translate'
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        //TODO: makes validators
        return [
            ['fieldValue', 'string'],
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
     * @param FieldsInterface $fieldAble
     */
    public function setFieldAble(FieldsInterface $fieldAble)
    {
        $this->fieldAble = $fieldAble;
    }

    /**
     * Saves record in data base
     * @return bool
     */
    public function save()
    {

    }

    /**
     * @inheritdoc
     */
    protected function isCorrectConfigured()
    {
        if (!parent::isCorrectConfigured() || !$this->fieldTemplate || !$this->fieldAble) return false;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentTranslateDb()
    {
        if ($this->currentTranslateDb) return $this->currentTranslateDb;

        if (!($field = $this->fieldAble->getField($this->fieldTemplate->program_name))) {

            $field = new Field();
            $field->common_fields_template_id = $this->fieldTemplate->id;
            $field->field_reference = $this->fieldAble->getFieldReference();
            $field->visible = true;
            $field->editable = true;

            $field->save(false);
        }
    }

    /**
     * @inheritdoc
     */
    protected function createTranslateDb()
    {

    }
}