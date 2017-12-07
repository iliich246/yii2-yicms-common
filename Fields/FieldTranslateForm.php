<?php

namespace Iliich246\YicmsCommon\Fields;

use Iliich246\YicmsCommon\Base\AbstractTranslateForm;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\CommonModule;

/**
 * Class FieldTranslateForm
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldTranslateForm extends AbstractTranslateForm implements FieldRenderInterface
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
     * @var FieldsInterface|FieldReferenceInterface
     */
    private $fieldAble;

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

        $this->field = $field;

        $this->currentTranslateDb = FieldsNamesTranslatesDb::find()
            ->where([
                'common_fields_template_id' => $this->fieldTemplate->id,
                'common_language_id' => $this->language->id
        ])->one();

        return $this->currentTranslateDb;
    }

    /**
     * @inheritdoc
     */
    protected function createTranslateDb()
    {
        throw new CommonException('From there we down`t need to create FieldsNamesTranslatesDb');
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
    public function getKey()
    {
        return '[' . $this->language->id . '-' . $this->fieldTemplate->id . ']value';
    }

    /**
     * @inheritdoc
     */
    public function getFieldName()
    {
        /** @var FieldsNamesTranslatesDb $fieldName */
        $fieldName = $this->getCurrentTranslateDb();

        if ($fieldName && trim($fieldName->name) && CommonModule::isUnderAdmin()) return $fieldName->name;

        if ((!$fieldName || !trim($fieldName->name)) && CommonModule::isUnderAdmin())
            return $this->fieldTemplate->program_name;

        if ($fieldName && trim($fieldName->name) && CommonModule::isUnderDev())
            return $fieldName->name . ' (' . $this->fieldTemplate->program_name .')';

        if ((!$fieldName || !trim($fieldName->name)) && CommonModule::isUnderDev())
            return 'No translate for field \'' . $this->fieldTemplate->program_name . '\'';

        return 'Can`t reach this place if all correct';
    }
}
