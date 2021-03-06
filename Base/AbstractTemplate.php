<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;
use yii\base\Event;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class AbstractTemplate
 *
 * All templates must inherit this class. This class implements methods for buffering and basic methods.
 *
 * @property integer $id
 * @property string $program_name
 * $buffer variable that must be declared as static property in child classes;
 * It`s used for buffering templates and reduce database accesses
 * self::$buffer = [
 *     'templateReference1' => [
 *         'programName1' => <instance of child of AbstractTemplate with data fetched from data base>
 *         'programName2' => <object>
 *          ...
 *         'programNameN' => <object>
 *     ]
 *     ...
 *     'templateReferenceN' => [
 *       'programName1' => <object>
 *       'programName2' => <object>
 *        ...
 *       'programNameN' => <object>
 *     ],
 * ]
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractTemplate extends ActiveRecord implements SortOrderInterface
{
    use SortOrderTrait;
    /** @var null|array description of field in description of class  */
    private static $buffer = null;

    const EVENT_BEFORE_FETCH = 0x99;

    const SCENARIO_CREATE = 0x00;
    const SCENARIO_UPDATE = 0x01;
    const SCENARIO_CHANGE_ORDER = 0x02;

    /**
     * @inheritdoc
     * @throws CommonException
     */
    public function init()
    {
        //TODO: delete this method in production
        if (!isset(static::$buffer))
            throw new CommonException('public static variable $buffer must be declared in child class');

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'program_name' => 'Program name',
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'program_name'
            ],
            self::SCENARIO_UPDATE => [
                'program_name'
            ],
            self::SCENARIO_CHANGE_ORDER => [
                static::getOrderFieldName()
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['program_name', 'required', 'message' => 'Obligatory input field'],
            ['program_name', 'string', 'max' => '50', 'tooLong' => 'Program name must be less than 50 symbols'],
            ['program_name', 'validateProgramName'],
        ];
    }

    /**
     * Validates the program name.
     * This method checks, that for group of "template reference" program name is unique.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateProgramName($attribute, $params)
    {
        if (!$this->hasErrors()) {

            $query = static::find()->where([
                static::getTemplateReferenceName() => $this->getTemplateReference(),
                'program_name' => $this->program_name
            ]);

            if ($this->scenario == self::SCENARIO_UPDATE)
                $query->andWhere(['not in', 'program_name', $this->getOldAttribute('program_name')]);

            $count = $query->all();

            if ($count)$this->addError($attribute, 'Field with same name already existed');
        }
    }

    /**
     * Returns instance of template object with data fetched from database;
     * @param $templateReference
     * @param $programName
     * @param null $variation
     * @return AbstractTemplate
     */
    public static function getInstance($templateReference, $programName, $variation = null)
    {
        if (($value = self::getFromCache($templateReference, $programName, $variation)) !== false) {
            return $value;
        }

        if ($variation)
            if ($variationValue = static::findTemplateWithVariation($templateReference, $programName)) {
                $value = clone($variationValue);
            } else {
                /** @var AbstractTemplate $value */
                $value = static::fetchTemplate($templateReference, $programName);
            }
        else {
            /** @var AbstractTemplate $value */
            $value = static::fetchTemplate($templateReference, $programName);
        }

        if ($value || is_null($value))
            self::setToCache($templateReference, $programName, $value);

        return $value;
    }

    /**
     * Find template for create clone of variation
     * @param $templateReference
     * @param $programName
     * @return static|bool
     */
    private static function findTemplateWithVariation($templateReference, $programName)
    {
        foreach (static::$buffer as $bufferTemplateReference => $element) {
            if (strripos($bufferTemplateReference, $templateReference) === false) continue;

            if (isset(static::$buffer[$bufferTemplateReference][$programName]))
                return static::$buffer[$bufferTemplateReference][$programName];

            return false;
        }

        return false;
    }

    /**
     * Return true if template with this name existed
     * @param $templateReference
     * @param $programName
     * @param null $variation
     * @return bool
     */
    public static function isTemplate($templateReference, $programName, $variation = null)
    {
        return !!self::getInstance($templateReference, $programName, $variation);
    }

    /**
     * Returns instance of template object with data fetched from database by id;
     * @param $id
     * @return AbstractTemplate|static
     */
    public static function getInstanceById($id)
    {
        /** @var array $templateReferenceBlock */
        foreach (static::$buffer as $templateReferenceBlock) {
            /** @var AbstractTemplate $abstractTemplateInstance */
            foreach($templateReferenceBlock as $abstractTemplateInstance) {
                
                if (is_null($abstractTemplateInstance)) continue;

                if ($abstractTemplateInstance->id == $id) return $abstractTemplateInstance;
            }
        }

        /** @var static $abstractTemplateInstance */
        $abstractTemplateInstance = static::findOne($id);
        $templateReference = $abstractTemplateInstance->getTemplateReference();
        $programName = $abstractTemplateInstance->program_name;

        static::setToCache($templateReference, $programName, $abstractTemplateInstance);

        return $abstractTemplateInstance;
    }

    /**
     * Generates template reference key
     * @return string
     * @throws CommonException
     */
    protected static function generateTemplateReference()
    {
        $value = strrev(uniqid());

        $coincidence = true;
        $counter = 0;

        while($coincidence) {
            if (!static::find()->where([
                static::getTemplateReferenceName() => $value
            ])->one()) return $value;

            if ($counter++ > 100) {
                Yii::error('Looping', __METHOD__);
                throw new CommonException('Looping in ' . __METHOD__);
            }
        }

        throw new CommonException('Can`t reach there 0_0' . __METHOD__);
    }

    /**
     * Return list of templates
     * @param $templateReference
     * @return ActiveQuery
     */
    public static function getListQuery($templateReference)
    {
        return static::find()->where([
            static::getTemplateReferenceName() => $templateReference,
        ]);
    }

    /**
     * Returns data form cache by keys
     * @param $templateReference
     * @param $programName
     * @param null $variation
     * @return static|bool
     */
    private static function getFromCache($templateReference, $programName, $variation = null)
    {
        if (!$variation) {
            if (isset(static::$buffer[$templateReference]) && array_key_exists($programName, static::$buffer[$templateReference]))
                return static::$buffer[$templateReference][$programName];
        } else {
            if (isset(static::$buffer[$templateReference . '_' .$variation]) &&
                array_key_exists($programName, static::$buffer[$templateReference . '_' .$variation]))
                return static::$buffer[$templateReference . '_' .$variation][$programName];
        }

        return false;
    }

    /**
     * Checks data in cache by keys
     * @param $templateReference
     * @param $programName
     * @return bool
     */
    private static function isInCache($templateReference, $programName)
    {
        if (isset(static::$buffer[$templateReference]) && array_key_exists($programName, static::$buffer[$templateReference]))
            return true;

        return false;
    }

    /**
     * Stores a value identified by a key into cache.
     * @param $templateReference
     * @param $programName
     * @param $value
     * @param null $variation
     */
    protected static function setToCache($templateReference, $programName, $value, $variation = null)
    {
        if (!$variation)
            static::$buffer[$templateReference][$programName] = $value;
        else
            static::$buffer[$templateReference . '_' .$variation][$programName] = $value;
    }

    /**
     * Fetch from data base template object
     * @param $templateReference
     * @param $programName
     * @return array|null|ActiveRecord
     */
    private static function fetchTemplate($templateReference, $programName)
    {
        Event::trigger(static::className(), self::EVENT_BEFORE_FETCH);
        //\Yii::warning(print_r(static::class,true));
        return static::find()->where([
            static::getTemplateReferenceName() => $templateReference,
            'program_name'                     => $programName
        ])->one();
    }

    /**
     * This method must be overridden in child and return name of db field with template reference
     * (abstract static methods violates the PHP strict standards)
     * @return string
     */
    protected static function getTemplateReferenceName()
    {
        return '';
    }

    /**
     * Return template reference associated with this object
     * @return string
     */
    public function getTemplateReference()
    {
        $attribute = static::getTemplateReferenceName();
        return $this->$attribute;
    }
}
