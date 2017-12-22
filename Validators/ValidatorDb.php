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
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_validators}}';
    }
}
