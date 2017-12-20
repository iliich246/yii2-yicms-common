<?php

namespace Iliich246\YicmsCommon\Fields;

use Yii;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class Field
 *
 * @property integer $id
 * @property integer $common_fields_template_id
 * @property integer $field_reference
 * @property string $value
 * @property integer $editable
 * @property integer $visible
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Field extends ActiveRecord implements FieldRenderInterface
{
    /**
     * Modes of field
     *
     * In default mode returns translation only in active language. If can not found translation or its empty, returns false.
     *
     * In the alert mode current object will be to try to find translation on active language or
     * return value for single language mode fields.
     * If field has no translations for current language or it`s empty, or value is empty for single language mode
     * field will render alert message
     */
    const MODE_DEFAULT = 0;
    const MODE_ALERT = 1;

    /**
     * @var int keeps mode of field
     */
    private $mode = self::MODE_DEFAULT;

    /**
     * @var
     */
    private $translation = null;

    /** @var FieldTemplate instance of field template */
    private $template;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_fields_represents}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['value', 'string'],
            ['field_reference', 'string'],
            [
                ['common_fields_template_id'], 'exist', 'skipOnError' => true,
                'targetClass' => FieldTemplate::className(), 'targetAttribute' => ['common_fields_template_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (defined('YICMS_ALERTS')) $this->setAlertMode();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'value' => $this->getFieldName(),
        ];
    }

    /**
     * Sets object in alert mode
     * @return $this
     */
    public function setAlertMode()
    {
        $this->mode = self::MODE_ALERT;
        return $this;
    }

    /**
     * Sets object in default mode
     * @return $this
     */
    public function setDefaultMode()
    {
        $this->mode = self::MODE_DEFAULT;
        return $this;
    }

    /**
     * Returns translate of field on current language or value of field according field language mode
     */
    public function __toString()
    {
        if (!$this->getTemplate()->visible) {

            if ($this->mode == self::MODE_DEFAULT) return false;

            return 'Field template for this field is invisible';
        }

        if (!$this->visible) {
            if ($this->mode == self::MODE_DEFAULT) return false;

            return 'Field is invisible';
        }

        if ($this->getLanguageType() == FieldTemplate::LANGUAGE_TYPE_SINGLE) {
            if ($this->mode == self::MODE_DEFAULT) {
                if (!$this->value) {
                    Yii::warning("Empty field value for field \"" .  $this->getTemplate()->program_name . '"', __METHOD__);
                    return false;
                }

                return $this->value;
            }

            if ($this->value) return $this->value;

            Yii::warning("Empty field value for field \"" .  $this->getTemplate()->program_name . '"', __METHOD__);
            return 'Warning! Empty value';
        }

        return $this->getTranslate();
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $fieldTranslates = FieldTranslate::find()->where([
            'common_fields_represent_id' => $this->id
        ])->all();

        foreach($fieldTranslates as $fieldTranslate)
            $fieldTranslate->delete();

        parent::delete();
    }

    /**
     * Return true is existed translation for param language
     * If no parameter, function work for current language
     *
     * @param LanguagesDb|null $language
     * @return bool
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function isTranslate(LanguagesDb $language = null)
    {
        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        if (!is_null($this->translation[$language->id])) {
            if (trim($this->translation[$language->id]->name) !== '') return true;
            return false;
        }

        $this->translation[$language->id] = FieldTranslate::find()->where([
            'common_fields_represent_id' => $this->id,
            'common_language_id' => $language->id,
        ])->one();

        if ($this->translation[$language->id])
            if (trim($this->translation[$language->id]->value) !== '') return true;

        return false;
    }

    /**
     * Return translate based on internal parameters of object
     *
     * If object in RETURN_MODE_STRING, he will return strings
     * else he return TranslateDb object for concrete $this
     *
     * @param LanguagesDb|null $language if null translation will be returned for current language
     * @return string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getTranslate(LanguagesDb $language = null)
    {
        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        //language buffer empty
        if (is_null($this->translation[$language->id])) {
            $this->translation[$language->id] = FieldTranslate::find()->where([
                'common_fields_represent_id' => $this->id,
                'common_language_id' => $language->id,
            ])->one();
        }

        /** @var FieldTranslate $translate */
        if ($translate = $this->translation[$language->id]) {

            if ($this->mode == self::MODE_DEFAULT) {
                if (!$translate->value) {
                    Yii::warning("Empty text of translation on \"$language->name\" language for field "
                        . $this->getTemplate()->program_name, __METHOD__);
                }

                return false;
            }

            if ($translate->value) return $translate->value;

            Yii::warning("Empty field translate for field \"" .  $this->getTemplate()->program_name . '"', __METHOD__);
            return 'Warning! empty field translate';
        }

        Yii::warning("No translate for field \"" .  $this->getTemplate()->program_name . '"', __METHOD__);

        if ($this->mode == self::MODE_DEFAULT) return false;

        return 'Warning! No field translate';
    }

    /**
     * Return fetched from db instance of field
     * @param $fieldTemplateReference
     * @param $fieldReference
     * @param $programName
     * @return array|null|ActiveRecord
     */
    public static function getInstance($fieldTemplateReference, $fieldReference, $programName)
    {
        //TODO: may be better to return empty field object
        if (is_null($template = FieldTemplate::getInstance($fieldTemplateReference, $programName))) return null;

        /** @var self $field */
        $field = self::find()->where([
            'common_fields_template_id' => $template->id,
            'field_reference' => $fieldReference
        ])->one();

        if (!$field) return null;

        $field->template = $template;

        return $field;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->getTemplate()->type;
    }

    /**
     * @inheritdoc
     */
    public function getLanguageType()
    {
        return $this->getTemplate()->language_type;
    }

    /**
     * @inheritdoc
     */
    public function isEditable()
    {
        return (bool)$this->editable;
    }

    /**
     * @inheritdoc
     */
    public function isVisible()
    {
        return (bool)$this->visible;
    }

    /**
     * @inheritdoc
     */
    public function getKey()
    {
       return '[' . $this->getTemplate()->id . ']value';
    }

    /**
     * Return instance of field template object
     * @return FieldTemplate
     */
    public function getTemplate()
    {
        if ($this->template) return $this->template;

        $this->template = FieldTemplate::findOne($this->common_fields_template_id);

        return $this->template;
    }

    /**
     * @inheritdoc
     */
    public function getFieldId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getFieldName()
    {
        /** @var FieldsNamesTranslatesDb $fieldName */
        $fieldName = FieldsNamesTranslatesDb::find()
            ->where([
                'common_fields_template_id' => $this->getTemplate()->id,
                'common_language_id' => Language::getInstance()->getCurrentLanguage()->id
            ])->one();

        if ($fieldName && trim($fieldName->name) && CommonModule::isUnderAdmin()) return $fieldName->name;

        if ((!$fieldName || !trim($fieldName->name)) && CommonModule::isUnderAdmin())
            return $this->getTemplate()->program_name;

        if ($fieldName && trim($fieldName->name) && CommonModule::isUnderDev())
            return $fieldName->name . ' (' . $this->getTemplate()->program_name .')';

        if ((!$fieldName || !trim($fieldName->name)) && CommonModule::isUnderDev())
            return 'No translate for field \'' . $this->getTemplate()->program_name . '\'';

        return 'Can`t reach this place if all correct';
    }
}
