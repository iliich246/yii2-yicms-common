<?php

namespace Iliich246\YicmsCommon\Validators;

use Iliich246\YicmsCommon\Base\CommonException;
use Yii;
use yii\base\Model;
use yii\widgets\ActiveForm;

/**
 * Class AbstractValidatorForm
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractValidatorForm extends Model
{
    public static $builtInValidators = [
        'required' => 'Iliich246\YicmsCommon\Validators\RequiredValidatorForm',
        'string' => 'Iliich246\YicmsCommon\Validators\StringValidatorForm',
        'number' => 'Iliich246\YicmsCommon\Validators\NumberValidatorForm',
    ];

    public static function listOfWidgets()
    {
        return self::$builtInValidators;
    }

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

    public static function getValidatorsDb($validatorReference)
    {
        static $validatorsDb;

        if ($validatorsDb) return $validatorsDb;

        /** @var ValidatorDb[] $validatorsDb */
        $validatorsDb = ValidatorDb::find()
            ->where([
                'validator_reference' => $validatorReference
            ])->indexBy('validator')
            ->all();

        return $validatorsDb;
    }


    public $isActivate;

    /**
     * @var string class of yii validator, for which this form
     */
    public $validator;

    public $serializeAble = [];

    /** @var ValidatorDb  */
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
     * Build and return concrete validator by it`s id
     * @param $id
     * @return static|bool
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

        //throw new \Exception(print_r(new $class,true));

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
        return $this->validatorDb->save();
    }

    /**
     * Method checks configurable array and deletes non existent element from him
     * @return array
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
     * returns class of yii validator, for which this form
     * @return string
     */
    protected abstract function getValidatorClass();

    /**
     * Returns view name for concrete widget
     * @return string
     */
    protected abstract function getRenderView();

    /**
     * Return
     * @return mixed
     */
    protected abstract function getValidatorFormName();
}
