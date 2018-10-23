<?php

namespace Iliich246\YicmsCommon\Validators;

use yii\db\ActiveRecord;

/**
 * Class ValidatorDb
 *
 * @property integer $id
 * @property string $validator_reference
 * @property string $validator
 * @property bool $is_active
 * @property string $params
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ValidatorDb extends ActiveRecord
{
    /** @var array of validators for buffering */
    private static $buffer = [];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_validators}}';
    }

    /**
     * Return buffered sets of validatorDb fetched by validator reference
     * @param $validatorReference
     * @return array|mixed|ActiveRecord[]|self[]
     */
    public static function getInstancesByReference($validatorReference)
    {
        if (isset(self::$buffer[$validatorReference]))
            return self::$buffer[$validatorReference];

        return self::$buffer[$validatorReference] = self::find()->where([
            'validator_reference' => $validatorReference
        ])->all();
    }
}
