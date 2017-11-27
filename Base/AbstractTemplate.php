<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;
use yii\base\Event;
use yii\db\ActiveRecord;

/**
 * Class AbstractTemplate
 *
 * All templates must inherit this class. This class implements methods for buffering and basic CRUD methods.
 *
 * @property integer $id
 * @property string $program_name
 * @property array $buffer variable that must be declared as static property in child classes;
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
abstract class AbstractTemplate extends ActiveRecord
{
    const EVENT_BEFORE_FETCH = 0x99;

    //const SCENARIO_CREATE = 0x00;
    //const SCENARIO_UPDATE = 0x01;

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
//    public function scenarios()
//    {
//        return [
//            self::SCENARIO_CREATE => [
//                'programName'
//            ],
//            self::SCENARIO_UPDATE => [
//                'programName'
//            ],
//        ];
//    }

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



            $count = $query->count();

            if ($count)$this->addError($attribute, 'Field with same name already existed');

//            $fieldQuery = PagesFieldsDb::find()->where([
//                'page_id' => $this->_pagesDb->id,
//                'program_name' => $this->programName,
//            ]);
//
//            if ($this->scenario == self::SCENARIO_UPDATE)
//                $fieldQuery->andWhere(['not in', 'program_name', $this->_fieldDb->program_name]);
//
//            $fields = $fieldQuery->all();
//
//            if ($fields)$this->addError($attribute, 'Field with same name already exist for current page ('.
//                $this->_pagesDb->program_name . ')'
//            );
        }
    }

    /**
     * Returns instance of template object with data fetched from database;
     * @param $templateReference
     * @param $programName
     * @return static|null
     */
    public static function getInstance($templateReference, $programName)
    {
        if (($value = self::getFromCache($templateReference, $programName)) !== false) {
            return $value;
        }

        $value = self::fetchTemplate($templateReference, $programName);

        self::setToCache($templateReference, $programName, $value);

        return $value;
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
     * @return $this
     * @throws CommonException
     */
    public static function getList($templateReference)
    {
        return static::find()->where([
            static::getTemplateReferenceName() => $templateReference,
        ])->all();
    }

    /**
     * This method must be overridden in child and return him static buffer
     * (abstract static methods violates the PHP strict standards)
     * @return array
     */
    protected static function getBuffer()
    {
        return [];
    }

    /**
     * This method must be overridden in child and return him static buffer
     * @param $buffer
     */
    protected static function setBuffer($buffer)
    {
        static::setBuffer($buffer);
    }

    /**
     * @param $templateReference
     * @param $programName
     * @return static|bool
     */
    private static function getFromCache($templateReference, $programName)
    {
        $buffer = static::getBuffer();

        if (isset($buffer[$templateReference]) && array_key_exists($programName, $buffer[$templateReference]))
            return $buffer[$templateReference][$programName];

        return false;
    }

    /**
     * Stores a value identified by a key into cache.
     * @param integer $templateReference
     * @param string $programName
     * @param self $value
     * @return void
     */
    private static function setToCache($templateReference, $programName, $value)
    {
        $buffer = static::getBuffer();
        $buffer[$templateReference][$programName] = $value;

        self::setBuffer($buffer);
    }

    /**
     * Fetch from data base template object
     * @param $templateReference
     * @param $programName
     * @return static
     */
    private static function fetchTemplate($templateReference, $programName)
    {
        Event::trigger(static::className(), self::EVENT_BEFORE_FETCH);
        //\Yii::warning('FETCHING DATA = ');
        return static::find()->where([
            static::getTemplateReferenceName() => $templateReference,
            'program_name' => $programName
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
