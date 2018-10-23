<?php

namespace Iliich246\YicmsCommon\Validators;

use Yii;
use yii\base\Component;
use Iliich246\YicmsCommon\Base\CommonException;

/**
 * Class ValidatorBuilder
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ValidatorBuilder extends Component
{
    /** @var ValidatorReferenceInterface instance */
    private $referenceAble;

    /**
     * Constructor
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * Set referenceAble for this validator builder
     * @param ValidatorReferenceInterface $referenceAble
     */
    public function setReferenceAble(ValidatorReferenceInterface $referenceAble)
    {
        $this->referenceAble = $referenceAble;
    }

    /**
     * Builds array of configured yii validators
     * @return array|bool
     * @throws CommonException
     */
    public function build()
    {
        if (!$this->referenceAble) return false;

        $validatorReference = $this->referenceAble->getValidatorReference();

        /** @var ValidatorDb[] $validatorsDb */
        $validatorsDb = ValidatorDb::getInstancesByReference($validatorReference);

        if (!$validatorsDb) return false;

        $result = [];


        foreach($validatorsDb as $validatorDb) {
            $validatorForm = AbstractValidatorForm::getInstanceByValidatorDb($validatorDb);

            $yiiValidator = $validatorForm->buildValidator();
            if (!$yiiValidator) continue;

            $result[] = $yiiValidator;
        }

        return $result;
    }

    /**
     * Generates validator reference key
     * @return string
     * @throws CommonException
     */
    public static function generateValidatorReference()
    {
        $value = strrev(uniqid());

        $coincidence = true;
        $counter = 0;

        while($coincidence) {
            if (!ValidatorDb::find()->where([
                'validator_reference' => $value
            ])->one()) return $value;

            if ($counter++ > 100) {
                Yii::error('Looping', __METHOD__);
                throw new CommonException('Looping in ' . __METHOD__);
            }
        }

        throw new CommonException('Can`t reach there 0_0' . __METHOD__);
    }
}
