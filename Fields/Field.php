<?php

namespace Iliich246\YicmsCommon\Fields;

use Yii;
use yii\db\ActiveRecord;
use yii\validators\SafeValidator;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Base\FictiveInterface;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use Iliich246\YicmsCommon\Validators\ValidatorBuilder;
use Iliich246\YicmsCommon\Validators\ValidatorBuilderInterface;
use Iliich246\YicmsCommon\Validators\ValidatorReferenceInterface;

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
class Field extends ActiveRecord implements
    FieldRenderInterface,
    FictiveInterface,
    ValidatorBuilderInterface,
    ValidatorReferenceInterface
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

    /** @var int keeps mode of field */
    private $mode = self::MODE_DEFAULT;
    /** @var FieldTranslate[] array of field translations */
    private $translation = null;
    /** @var FieldTemplate instance of field template */
    private $template;
    /** @var ValidatorBuilder instance */
    private $validatorBuilder;
    /** @var FieldsNamesTranslatesDb[] buffer for language */
    private $fieldNamesTranslations;
    /** @var bool keeps state of fictive value */
    private $isFictive = false;
    /** @var bool if true field will behaviour as nonexistent   */
    private $isNonexistent = false;
    /** @var string value for keep program name in nonexistent mode */
    private $nonexistentProgramName;

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

//        $this->on(self::EVENT_AFTER_FIND, function() {
//
//            $validators = $this->getValidatorBuilder()->build();
//
//            if (!$validators) return;
//
//            foreach($validators as $validator)
//                $this->validators[] = $validator;
//        });

        parent::init();
    }

    /**
     * @inheritdoc
     * @throws CommonException
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
     * @return bool|string
     * @throws CommonException
     */
    public function __toString()
    {
        if ($this->isNonexistent) {
            if (CommonModule::isUnderDev() && defined('YICMS_ALERTS'))
                return '(DEV) Try to output nonexistent field by name "' . $this->nonexistentProgramName . '"';

            return '';
        };

        if (!$this->getTemplate()->visible) {

            if ($this->mode == self::MODE_DEFAULT) return '';

            if (CommonModule::isUnderDev())
                return 'Field template for this field is invisible (dev)';

            return '';
        }

        if (!$this->visible) {
            if ($this->mode == self::MODE_DEFAULT) return false;

            if (CommonModule::isUnderDev() || CommonModule::isUnderAdmin())
                return 'Field is invisible';

            return '';
        }

        if ($this->getLanguageType() == FieldTemplate::LANGUAGE_TYPE_SINGLE) {
            if ($this->mode == self::MODE_DEFAULT) {
                if (!trim($this->value)) {
                    Yii::warning("Empty field value for field \"" . $this->getTemplate()->program_name . '"', __METHOD__);
                    return false;
                }

                return (string)$this->value;
            }

            if (trim($this->value)) return $this->value;

            Yii::warning("Empty field value for field \"" . $this->getTemplate()->program_name . '"', __METHOD__);

            if ($this->mode == self::MODE_DEFAULT) return '';

            if (CommonModule::isUnderDev() || CommonModule::isUnderAdmin())
                return 'Warning! Empty value';

            return '';
        }

        return $this->getTranslate();
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        if ($this->isNonexistent) return false;

        $fieldTranslates = FieldTranslate::find()->where([
            'common_fields_represent_id' => $this->id
        ])->all();

        foreach ($fieldTranslates as $fieldTranslate)
            $fieldTranslate->delete();

        return parent::delete();
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
        if ($this->isNonexistent) return false;

        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        if (!is_null($this->translation[$language->id])) {
            if (trim($this->translation[$language->id]->name) !== '') return true;
            return false;
        }

        $this->translation[$language->id] = FieldTranslate::find()->where([
            'common_fields_represent_id' => $this->id,
            'common_language_id'         => $language->id,
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
        if ($this->isNonexistent) {
            if (CommonModule::isUnderDev() && defined('YICMS_ALERTS'))
                return '(DEV) Try to output nonexistent field by name "' . $this->nonexistentProgramName . '"';

            return '';
        };

        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        //language buffer empty
        if (is_null($this->translation[$language->id])) {
            $this->translation[$language->id] = FieldTranslate::find()->where([
                'common_fields_represent_id' => $this->id,
                'common_language_id'         => $language->id,
            ])->one();
        }

        /** @var FieldTranslate $translate */
        if ($translate = $this->translation[$language->id]) {
            if ($this->mode == self::MODE_DEFAULT) {
                if (!trim($translate->value)) {
                    Yii::warning("Empty text of translation on \"$language->name\" language for field "
                        . $this->getTemplate()->program_name, __METHOD__);

                    return '';
                }
            }

            if (trim($translate->value)) return $translate->value;

            Yii::warning("Empty field translate for field \"" . $this->getTemplate()->program_name . '"', __METHOD__);

            if (CommonModule::isUnderDev() || CommonModule::isUnderAdmin())
                return 'Warning! empty field translate';

            return '';
        }

        Yii::warning("No translate for field \"" . $this->getTemplate()->program_name . '"', __METHOD__);

        if ($this->mode == self::MODE_DEFAULT) return '';

        if (CommonModule::isUnderDev() || CommonModule::isUnderAdmin())
            return 'Warning! No field translate';

        return '';
    }

    /**
     * Return fetch from db instance of field
     * @param $fieldTemplateReference
     * @param $fieldReference
     * @param $programName
     * @return Field|null
     * @throws CommonException
     */
    public static function getInstance($fieldTemplateReference, $fieldReference, $programName)
    {
        if (is_null($template = FieldTemplate::getInstance($fieldTemplateReference, $programName))) {
            Yii::warning(
                "Can`t fetch for " . static::className() .
                " name = $programName and fieldTemplateReference = $fieldTemplateReference",
                __METHOD__);

            if (defined('YICMS_STRICT')) {
                throw new CommonException(
                    "YICMS_STRICT_MODE:
                Can`t fetch for " . static::className() .
                " name = $programName and fieldTemplateReference = $fieldTemplateReference");
            }

            $nonexistentField                         = new static();
            $nonexistentField->isNonexistent          = true;
            $nonexistentField->nonexistentProgramName = $programName;

            return $nonexistentField;
        };

        /** @var self $field */
        $field = self::find()->where([
            'common_fields_template_id' => $template->id,
            'field_reference'           => $fieldReference
        ])->one();

        if ($field) {
            $field->template = $template;
            return $field;
        }

        Yii::warning(
            "Can`t fetch for " . static::className() . " name = $programName and fieldReference = $fieldReference",
            __METHOD__);

        if (defined('YICMS_STRICT')) {
            throw new CommonException(
                "YICMS_STRICT_MODE:
                Can`t fetch for " . static::className() . " name = $programName and fieldReference = $fieldReference");
        }

        $nonexistentField                         = new self();
        $nonexistentField->isNonexistent          = true;
        $nonexistentField->nonexistentProgramName = $programName;

        return $nonexistentField;
    }

    /**
     * Generates reference key
     * @return string
     * @throws CommonException
     */
    public static function generateReference()
    {
        $value = strrev(uniqid());

        $coincidence = true;
        $counter = 0;

        while($coincidence) {
            if (!self::find()->where([
               'field_reference' => $value
            ])->one()) return $value;

            if ($counter++ > 100) {
                Yii::error('Looping', __METHOD__);
                throw new CommonException('Looping in ' . __METHOD__);
            }
        }

        throw new CommonException('Can`t reach there 0_0' . __METHOD__);
    }

    /**
     * Returns field nonexistent state
     * @return bool
     */
    public function isNonexistent()
    {
        return $this->isNonexistent;
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

        $this->template = FieldTemplate::getInstanceById($this->common_fields_template_id);

        return $this->template;
    }

    /**
     * Sets field template for this field (uses for fictive field)
     * @param FieldTemplate $template
     */
    public function setTemplate(FieldTemplate $template)
    {
        $this->template = $template;
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
     * @throws CommonException
     */
    public function getFieldName()
    {
        //TODO: delete duplicate db requests
        $fieldName = $this->getFieldNameTranslate(Language::getInstance()->getCurrentLanguage());

        if ($fieldName && trim($fieldName->name) && CommonModule::isUnderAdmin()) return $fieldName->name;

        if ((!$fieldName || !trim($fieldName->name)) && CommonModule::isUnderAdmin())
            return $this->getTemplate()->program_name;

        if ($fieldName && trim($fieldName->name) && CommonModule::isUnderDev())
            return $fieldName->name . ' (' . $this->getTemplate()->program_name . ')';

        if ((!$fieldName || !trim($fieldName->name)) && CommonModule::isUnderDev())
            return 'No translate for field \'' . $this->getTemplate()->program_name . '\'';

        return 'Can`t reach this place if all correct';
    }

    /**
     * @inheritdoc
     * @throws CommonException
     */
    public function getFieldDescription()
    {
        $fieldName = $this->getFieldNameTranslate(Language::getInstance()->getCurrentLanguage());

        if ($fieldName)
            return $fieldName->description;

        return false;
    }

    /**
     * Returns buffered name translate db
     * @param LanguagesDb $language
     * @return FieldsNamesTranslatesDb
     */
    public function getFieldNameTranslate(LanguagesDb $language)
    {
        if (!isset($this->fieldNamesTranslations[$language->id])) {
            $this->fieldNamesTranslations[$language->id] = FieldsNamesTranslatesDb::find()->where([
                'common_fields_template_id' => $this->getTemplate()->id,
                'common_language_id'        => $language->id,
            ])->one();
        }

        return $this->fieldNamesTranslations[$language->id];
    }

    /**
     * Method config validators for this model
     * @throws CommonException
     */
    public function prepareValidators()
    {
        $validators = $this->getValidatorBuilder()->build();

        if (!$validators) {

            $safeValidator = new SafeValidator();
            $safeValidator->attributes = ['value'];
            $this->validators[] = $safeValidator;

            return;
        }

        foreach ($validators as $validator)
            $this->validators[] = $validator;
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
     * @throws CommonException
     * @throws \yii\base\Exception
     */
    public function getValidatorReference()
    {
        $fieldTemplate = $this->getTemplate();

        if (!$fieldTemplate->validator_reference) {
            $fieldTemplate->validator_reference = ValidatorBuilder::generateValidatorReference();
            $fieldTemplate->scenario = FieldTemplate::SCENARIO_UPDATE;
            $fieldTemplate->save(false);
        }

        return $fieldTemplate->validator_reference;
    }

    /**
     * @inheritdoc
     */
    public function setFictive()
    {
        $this->isFictive = true;
    }

    /**
     * @inheritdoc
     */
    public function clearFictive()
    {
        $this->isFictive = false;
    }

    /**
     * @inheritdoc
     */
    public function isFictive()
    {
        return $this->isFictive;
    }
}
