<?php

namespace Iliich246\YicmsCommon\Conditions;

use Yii;
use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\Base\CommonException;
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
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Condition extends ActiveRecord
{

    public $value;
    /**
     * @var ConditionTemplate instance of condition template
     */
    private $template = null;
    /**
     * @var ConditionsNamesTranslatesDb[]
     */
    private $translation;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->on(self::EVENT_AFTER_FIND, function() {

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
            ['condition_reference', 'string', 'max' => '255'],
            ['editable', 'boolean'],
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
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->getTemplate()->type == ConditionTemplate::TYPE_CHECKBOX) {
            if ($this->value == 0) $this->common_value_id = null;
            else {
                $values = $this->getTemplate()->getValuesList();
                reset($values);
                /** @var ConditionValues $value */
                $value = current($values);
                $this->common_value_id = $value->id;
            }
        }else {
            $this->common_value_id = $this->value;
        }

        return parent::save($runValidation, $attributeNames);
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

        $this->template = ConditionTemplate::findOne($this->common_condition_template_id);

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
            if (trim($this->translation[$language->id]->name) !== '') return $this->getTemplate()->program_name;
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
            if (trim($this->translation[$language->id]->description) !== '') return false;
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
     * Returns list of values for dropdown lists
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



}
