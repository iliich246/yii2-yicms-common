<?php

namespace Iliich246\YicmsCommon\Validators;

use Yii;
use yii\base\Model;
use Iliich246\YicmsCommon\Base\CommonException;

/**
 * Class AbstractValidatorForm
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractValidatorForm extends Model
{
    /** @var array of build in validators forms */
    public static $builtInValidators = [
        'required' => 'Iliich246\YicmsCommon\Validators\RequiredValidatorForm',
        'string'   => 'Iliich246\YicmsCommon\Validators\StringValidatorForm',
        'number'   => 'Iliich246\YicmsCommon\Validators\NumberValidatorForm',
        'file'     => 'Iliich246\YicmsCommon\Validators\FileValidatorForm',
        'image'    => 'Iliich246\YicmsCommon\Validators\ImageValidatorForm',
        'compare'  => 'Iliich246\YicmsCommon\Validators\CompareValidatorForm',
        'boolean'  => 'Iliich246\YicmsCommon\Validators\BooleanValidatorForm',

    ];
    /** @var array buffer of validator db with index by validatorReference */
    private static $validatorsDbBuffer;
    /** @var boolean is this validator is activated */
    public $isActivate;
    /** @var string class of yii validator, for which this form */
    public $validator;
    /** @var array of fields of class that must be saved in database */
    public $serializeAble = [];
    /** @var ValidatorDb instance associated with this validator form */
    private $validatorDb;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'isActivate' => 'Is activate',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['isActivate', 'boolean'],
        ];
    }

    /**
     * Returns list of build in validators forms
     * @return array
     */
    public static function listOfWidgets()
    {
        return self::$builtInValidators;
    }

    /**
     * Return subtracted list of validators
     * @param $validatorReference
     * @return array
     */
    public static function subtractListOfValidators($validatorReference)
    {
        $validatorsDb = self::getValidatorsDb($validatorReference);

        $allValidatorsForms = array_flip(self::$builtInValidators);

        if (!$validatorsDb) return $allValidatorsForms;

        foreach($validatorsDb as $validatorDb) {
            if ($allValidatorsForms[$validatorDb->validator])
                unset($allValidatorsForms[$validatorDb->validator]);
        }

        return $allValidatorsForms;
    }

    /**
     * Returns validator name by it`s class name
     * @param $className
     * @return bool|mixed
     */
    public static function validatorNameByClass($className)
    {
        if (!class_exists($className)) return false;

        /** @var $className self */
        return $className::getValidatorFormName();
    }

    /**
     * Returns true, if for current validatorReference you can add more validators
     * @param $validatorReference
     * @return bool
     */
    public static function canAddNewValidator($validatorReference)
    {
        $validatorsDb = self::getValidatorsDb($validatorReference);

        if (count($validatorsDb) < count(self::$builtInValidators)) return true;
        return false;
    }


    /**
     * Return list of all validators for current $validatorReference
     * @param $validatorReference
     * @return ValidatorDb[]
     */
    public static function getValidatorsDb($validatorReference)
    {
        if (self::$validatorsDbBuffer[$validatorReference]) return
            self::$validatorsDbBuffer[$validatorReference];

        /** @var ValidatorDb[] $validatorsDb */
        $validatorsDb = ValidatorDb::find()
            ->where([
                'validator_reference' => $validatorReference
            ])
            ->indexBy('validator')
            ->all();

        self::$validatorsDbBuffer[$validatorReference] = $validatorsDb;

        return self::$validatorsDbBuffer[$validatorReference];
    }

    /**
     * Build and return concrete validator by it`s id
     * @param $id
     * @return self|bool
     * @throws CommonException
     */
    public static function getConcreteInstance($id)
    {
        /** @var ValidatorDb $validatorDb */
        $validatorDb = ValidatorDb::findOne($id);

        if (!$validatorDb && !defined(YICMS_STRICT)) {
            Yii::warning('No validator db with id = ' . $id, __METHOD__);
            return false;
        }

        if (!$validatorDb && defined(YICMS_STRICT)) {
            Yii::warning('No validator db with id = ' . $id, __METHOD__);
            throw new CommonException('No validator db with id = ' . $id);
        }

        $class = $validatorDb->validator;

        if (!class_exists($class) && !defined(YICMS_STRICT)) {
            Yii::warning('Wrong validator class ' . $class, __METHOD__);
            return false;
        }

        if (!class_exists($class) && defined(YICMS_STRICT)) {
            Yii::warning('Wrong validator class ' . $class, __METHOD__);
            throw new CommonException('Wrong validator class ' . $class);
        }

        /** @var self $validator */
        $validator = new $class();
        $validator->validatorDb = $validatorDb;
        $validator->isActivate = $validatorDb->is_active;

        $validator->unSerializeData();

        return $validator;
    }

    /**
     * Return concrete instance of validator form by validator db in parameter
     * @param ValidatorDb $validatorDb
     * @return bool|AbstractValidatorForm
     * @throws CommonException
     */
    public static function getInstanceByValidatorDb(ValidatorDb $validatorDb)
    {
        $class = $validatorDb->validator;

        if (!class_exists($class) && !defined(YICMS_STRICT)) {
            Yii::warning('Wrong validator class ' . $class, __METHOD__);
            return false;
        }

        if (!class_exists($class) && defined(YICMS_STRICT)) {
            Yii::warning('Wrong validator class ' . $class, __METHOD__);
            throw new CommonException('Wrong validator class ' . $class);
        }

        /** @var self $validator */
        $validator = new $class();
        $validator->validatorDb = $validatorDb;
        $validator->isActivate = $validatorDb->is_active;

        $validator->unSerializeData();

        return $validator;
    }

    /**
     *
     * @return bool
     */
    public function save()
    {
        $this->checkSerializeAbleArray();

        $this->validatorDb->params = $this->serializeData();
        $this->validatorDb->is_active = $this->isActivate;
        return $this->validatorDb->save(false);
    }

    /**
     * Method checks configurable array and deletes non existent element from him     *
     */
    private function checkSerializeAbleArray()
    {
        foreach($this->serializeAble as $key => $item) {
            if (!property_exists($this, $item)) {
                Yii::warning("Try to config nonexistent property '$item' in validator form'", __METHOD__);
                unset($this->serializeAble[$key]);
            }
        }
    }

    /**
     * Method serialize data, needed to keep in db
     * @return string
     */
    private function serializeData()
    {
        $attributes = $this->attributes;
        $result = [];

        foreach($this->serializeAble as $configItem) {
            if (array_key_exists($configItem, $attributes)) {
                $result[$configItem] = $this->$configItem;
                 unset($attributes[$configItem]);
            } else
                Yii::warning("In serializeAble array of validator form existed field '$configItem',
                    that provider not give, it will not be configured", __METHOD__);
        }

        return serialize($result);
    }

    /**
     * Method un serialize data from db and insert in this form
     */
    private function unSerializeData()
    {
        $attributes = $this->attributes;
        $source = unserialize($this->validatorDb->params);

        foreach($this->serializeAble as $configItem) {
            if (array_key_exists($configItem, $attributes)) {
                if (isset($source[$configItem]) && property_exists($this, $configItem))
                    $this->$configItem = $source[$configItem];
            }
        }
    }

    /**
     * ValidatorDb getter
     * @return ValidatorDb
     */
    public function getValidatorDb()
    {
        return $this->validatorDb;
    }

    /**
     * Return instance of correct configured yii validator
     * @return \yii\validators\Validator
     */
    public abstract function buildValidator();

    /**
     * returns class of yii validator, for which this form
     * @return string
     */
    protected abstract function getValidatorClass();

    /**
     * Returns view name for concrete widget
     * @return string
     */
    public abstract function getRenderView();

    /**
     * Return
     * @return mixed
     */
    protected abstract function getValidatorFormName();
}
