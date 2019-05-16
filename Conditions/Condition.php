<?php

namespace Iliich246\YicmsCommon\Conditions;

use Yii;
use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\HookEvent;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Base\NonexistentInterface;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;

/**
 * Class Condition
 *
 * @property integer $id
 * @property integer $common_condition_template_id
 * @property string $condition_reference
 * @property integer $common_value_id
 * @property integer $editable
 * @property integer $checkbox_state
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Condition extends ActiveRecord implements NonexistentInterface
{
    /**
     * @event Event that is triggered before return condition value
     */
    const EVENT_BEFORE_OUTPUT = 'beforeOutput';

    /** @var string value of condition */
    public $value;
    /** @var ConditionTemplate instance of condition template */
    private $template = null;
    /** @var ConditionsNamesTranslatesDb[] instances */
    private $translation = null;
    /** @var string name of condition value */
    private $valueName = null;
    /** @var bool if true image block will behaviour as nonexistent   */
    protected $isNonexistent = false;
    /** @var string value for keep program name in nonexistent mode */
    protected $nonexistentProgramName;
    /** @var array buffer of fields for reduce duplicated requests to db */
    private static $buffer = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->on(self::EVENT_AFTER_FIND, function() {

            if ($this->getTemplate()->type == ConditionTemplate::TYPE_CHECKBOX) {
                $this->value = !!$this->checkbox_state;
                return;
            };

            if (is_null($this->common_value_id)) {
                $valueId = $this->getTemplate()->defaultValueId();

                if (!is_null($valueId)) {

                    $this->common_value_id = $valueId;
                    $this->simpleSave();
                }
            }

            $this->value = $this->common_value_id;
        });
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_conditions}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['value', 'string'],
            ['value', 'validateValue'],
            ['condition_reference', 'string', 'max' => '255'],
            [['editable', 'checkbox_state'], 'boolean'],
            [
                ['common_value_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ConditionValues::className(), 'targetAttribute' => ['common_value_id' => 'id']
            ],
            [
                ['common_condition_template_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ConditionTemplate::className(), 'targetAttribute' => ['common_condition_template_id' => 'id']
            ],
        ];
    }

    /**
     * Validates value.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateValue($attribute, $params)
    {
        if (!$this->hasErrors()) {

            if ($this->getTemplate()->type == ConditionTemplate::TYPE_CHECKBOX) return;

            $conditionValue = ConditionValues::findOne($this->value);

            if (!$conditionValue)
                $this->addError($attribute, 'Wrong value');
        }
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->getTemplate()->type == ConditionTemplate::TYPE_CHECKBOX) {
            if (!$this->value)
                $this->checkbox_state = false;
            else
                $this->checkbox_state = true;
        }else {
            $this->common_value_id = $this->value;
        }

        return parent::save($runValidation, $attributeNames);
    }

    /**
     * Proxy value() method to magical __toString()
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value();
    }

    /**
     * Return string name of condition value
     * @return string
     */
    public function value()
    {
        if ($this->isNonexistent) {
            if (CommonModule::isUnderDev() && defined('YICMS_ALERTS'))
                return '(DEV) Try to output nonexistent condition by name "' . $this->nonexistentProgramName . '"';

            return null;
        }

        $hookEvent = new HookEvent();

        if ($this->getTemplate()->type == ConditionTemplate::TYPE_CHECKBOX) {
            $hookEvent->setHook(!!$this->checkbox_state);

            $this->trigger(self::EVENT_BEFORE_OUTPUT, $hookEvent);

            return $hookEvent->getHook();
        }

        if (!is_null($this->valueName)) {

            $hookEvent->setHook( $this->valueName);

            $this->trigger(self::EVENT_BEFORE_OUTPUT, $hookEvent);

            return $hookEvent->getHook();
        }

        $hookEvent->setHook($this->valueName = ConditionValues::findOne($this->common_value_id)->value_name);

        $this->trigger(self::EVENT_BEFORE_OUTPUT, $hookEvent);

        return $hookEvent->getHook();
    }

    /**
     * Return true if conditions sets to true in checkbox mode
     * @return bool
     * @throws CommonException
     */
    public function isTrue()
    {
        if ($this->isNonexistent) {
            if (CommonModule::isUnderDev() && defined('YICMS_ALERTS'))
                return '(DEV) Try to output nonexistent condition by name "' . $this->nonexistentProgramName . '"';

            return null;
        }

        if ($this->getTemplate()->type == ConditionTemplate::TYPE_CHECKBOX) {
            if (!!$this->checkbox_state) return true;
            return false;
        }

        Yii::warning(
            "Try to use true/false methods of condition on none checkbox type",
            __METHOD__);

        if (defined('YICMS_STRICT')) {
            throw new CommonException(
                "Try to use true/false methods of condition on none checkbox type");
        }

        if (!!$this->checkbox_state) return true;
        return false;
    }

    /**
     * Return true if conditions sets to false in checkbox mode
     * @return bool
     * @throws CommonException
     */
    public function isFalse()
    {
        if ($this->isNonexistent) {
            if (CommonModule::isUnderDev() && defined('YICMS_ALERTS'))
                return '(DEV) Try to output nonexistent condition by name "' . $this->nonexistentProgramName . '"';

            return null;
        }

        if ($this->getTemplate()->type == ConditionTemplate::TYPE_CHECKBOX) {
            if (!!$this->checkbox_state) return false;
            return true;
        }

        Yii::warning(
            "Try to use true/false methods of condition on none checkbox type",
            __METHOD__);

        if (defined('YICMS_STRICT')) {
            throw new CommonException(
                "Try to use true/false methods of condition on none checkbox type");
        }

        if (!!$this->checkbox_state) return false;
        return true;
    }

    /**
     * Save action that just proxy parent method
     * @return bool
     */
    public function simpleSave()
    {
        return parent::save(false);
    }

    /**
     * Returns type of condition  essence
     * @return integer
     */
    public function getType()
    {
        return $this->getTemplate()->type;
    }

    /**
     * @return ConditionTemplate
     */
    public function getTemplate()
    {
        if ($this->template) return $this->template;

        $this->template = ConditionTemplate::getInstanceById($this->common_condition_template_id);

        return $this->template;
    }

    /**
     * Returns form key of field
     * example $form->field($model, <key>)->...
     * @return string
     */
    public function getKey()
    {
        return '[' . $this->getTemplate()->id . ']value';
    }

    /**
     * Return translated name of condition
     * @param LanguagesDb|false $language
     * @return string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getName(LanguagesDb $language = null)
    {
        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        if (!is_null($this->translation[$language->id])) {
            if (trim($this->translation[$language->id]->name) == '') return $this->getTemplate()->program_name;
            return trim($this->translation[$language->id]->name);
        }

        $this->translation[$language->id] = ConditionsNamesTranslatesDb::find()->where([
            'common_condition_template_id' => $this->getTemplate()->id,
            'common_language_id'           => $language->id,
        ])->one();

        if ($this->translation[$language->id])
            if (trim($this->translation[$language->id]->name) !== '')
                return trim($this->translation[$language->id]->name);

        return $this->getTemplate()->program_name;
    }

    /**
     * Returns translated description of condition
     * @param LanguagesDb|false $language
     * @return bool|string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getDescription(LanguagesDb $language = null)
    {
        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        if (!is_null($this->translation[$language->id])) {
            if (trim($this->translation[$language->id]->description) == '') return false;
            return trim($this->translation[$language->id]->description);
        }

        $this->translation[$language->id] = ConditionsNamesTranslatesDb::find()->where([
            'common_condition_template_id' => $this->getTemplate()->id,
            'common_language_id'           => $language->id,
        ])->one();

        if ($this->translation[$language->id])
            if (trim($this->translation[$language->id]->description) !== '')
                return trim($this->translation[$language->id]->description);

        return false;
    }

    /**
     * Returns fetch from db instance of condition
     * @param $conditionTemplateReference
     * @param $conditionReference
     * @param $programName
     * @return null
     * @throws CommonException
     */
    public static function getInstance($conditionTemplateReference, $conditionReference, $programName)
    {
        if (is_null($template = ConditionTemplate::getInstance($conditionTemplateReference, $programName))) {
            Yii::warning(
                "Can`t fetch for " . static::className() .
                " name = $programName and conditionTemplateReference = $conditionTemplateReference",
                __METHOD__);

            if (defined('YICMS_STRICT')) {
                throw new CommonException(
                    "YICMS_STRICT_MODE:
                Can`t fetch for " . static::className() .
                    " name = $programName and conditionTemplateReference = $conditionTemplateReference");
            }

            $nonexistentCondition = new static();
            $nonexistentCondition->setNonexistent();
            $nonexistentCondition->setNonexistentName($programName);

            return $nonexistentCondition;
        };

        if (self::isInBuffer($template->id, $conditionReference))
            $condition = self::$buffer[$template->id][$conditionReference];
        else {
            /** @var self $condition */
            $condition = self::find()->where([
                'common_condition_template_id' => $template->id,
                'condition_reference'          => $conditionReference,
            ])->one();

            if ($condition)
                self::$buffer[$template->id][$conditionReference] = $condition;
        }

        if ($condition) {
            $condition->template = $template;
            return $condition;
        }

        return null;
    }

    /**
     * Check isset condition in buffer
     * @param $commonConditionTemplateId
     * @param $fieldReference
     * @return bool
     */
    private static function isInBuffer($commonConditionTemplateId, $fieldReference)
    {
        if (isset(self::$buffer[$commonConditionTemplateId][$fieldReference]))
            return true;

        return false;
    }

    /**
     * Returns true if condition has any values
     * @return bool
     */
    public function isValues()
    {
        if ($this->isNonexistent)
            return false;

        return $this->getTemplate()->isValues();
    }

    /**
     * Returns list of values for drop down lists
     * @param LanguagesDb|null $language
     * @return array
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getValuesTranslatedArray(LanguagesDb $language = null)
    {
        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        $conditionValues = $this->getTemplate()->getValuesList();

        $array = [];

        foreach($conditionValues as $index => $value)
            $array[$index] = $value->getName($language);

        return $array;
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
                'condition_reference' => $value
            ])->one()) return $value;

            if ($counter++ > 100) {
                Yii::error('Looping', __METHOD__);
                throw new CommonException('Looping in ' . __METHOD__);
            }
        }

        throw new CommonException('Can`t reach there 0_0' . __METHOD__);
    }

    /**
     * @inheritdoc
     */
    public function isNonexistent()
    {
        return $this->isNonexistent;
    }

    /**
     * @inheritdoc
     */
    public function setNonexistent()
    {
        $this->isNonexistent = true;
    }

    /**
     * @inheritdoc
     */
    public function getNonexistentName()
    {
        return $this->nonexistentProgramName;
    }

    /**
     * @inheritdoc
     */
    public function setNonexistentName($name)
    {
        $this->nonexistentProgramName = $name;
    }
}
