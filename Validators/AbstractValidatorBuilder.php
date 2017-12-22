<?php

namespace Iliich246\YicmsCommon\Validators;

use Yii;
use yii\base\Component;
use Iliich246\YicmsCommon\Base\CommonException;

/**
 * Class AbstractValidatorBuilder
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractValidatorBuilder extends Component
{
    /**
     * Generates validator reference key
     * @return string
     * @throws CommonException
     */
    public static function generateTemplateReference()
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
