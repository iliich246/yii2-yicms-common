<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;
use yii\base\Model;
use SplFileObject;

/**
 * Class CommonHashModel
 *
 * This class implements basic work with dev and admin user without data base.
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class CommonHashForm extends Model
{
    const SCENARIO_CHANGE_DEV = 0;
    const SCENARIO_CHANGE_ADMIN = 1;

    const DEV_HASH = '$2y$13$TxAy2Ks7oUWSO/X3HrDlIOImmDecfICXRelK0Ys/D6g3Abvk4lCvq';
    const ADMIN_HASH = '$2y$13$u.vOLHBfi5H8GsQm29EODOHIEU2nKS2uY4/Fp/c6WdCxE/7fgys8W';

    /** @var string new hash */
    public $hash;
    /** @var string confirm hash  */
    public $confirmHash;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        if ($this->scenario == self::SCENARIO_CHANGE_DEV) {
            return [
                'hash' => 'Developer Hash',
                'confirmHash' => 'Confirm developer hash'
            ];
        }

        if ($this->scenario == self::SCENARIO_CHANGE_ADMIN) {
            return [
                'hash' => 'Admin Hash',
                'confirmHash' => 'Confirm admin hash'
            ];
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CHANGE_DEV => [
                'hash', 'confirmHash'
            ],
            self::SCENARIO_CHANGE_ADMIN => [
                'hash', 'confirmHash'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // required
            [
                ['hash', 'confirmHash'], 'required',
                'message' => 'Can\'t be blank',
            ],
            // password
            [
                ['hash', 'confirmHash'], 'string', 'min' => 6,
                'tooShort' => 'Hash too short, less than 6 symbols',
            ],
            //confirm password
            [
                'confirmHash', 'compare', 'compareAttribute' => 'hash', 'operator' => '===',
                'message' => 'Hash not same',
            ],
        ];
    }

    /**
     * Change of dev password
     * @return bool
     * @throws \yii\base\Exception
     */
    public function changeDev()
    {
        $fileText = htmlspecialchars(file_get_contents(__FILE__));

        $hash = '\'';
        $hash .= \Yii::$app->getSecurity()->generatePasswordHash($this->hash);
        $hash .= '\'';

        $pos1 = stripos($fileText, 'const DEV_HASH');
        $pos1 = stripos($fileText, '\'', $pos1);
        $pos2 = stripos($fileText, ';', $pos1);

        $str = substr_replace($fileText, $hash, $pos1, $pos2-$pos1);

        $file = new SplFileObject(__FILE__, 'w+');
        $file->fwrite(htmlspecialchars_decode($str));

        return true;
    }

    /**
     * Change of admin password
     * @return bool
     * @throws \yii\base\Exception
     */
    public function changeAdmin()
    {
        $fileText = htmlspecialchars(file_get_contents(__FILE__));

        $hash = '\'';
        $hash .= \Yii::$app->getSecurity()->generatePasswordHash($this->hash);
        $hash .= '\'';

        $pos1 = stripos($fileText, 'const ADMIN_HASH');
        $pos1 = stripos($fileText, '\'', $pos1);
        $pos2 = stripos($fileText, ';', $pos1);

        $str = substr_replace($fileText, $hash, $pos1, $pos2-$pos1);

        $file = new SplFileObject(__FILE__, 'w+');
        $file->fwrite(htmlspecialchars_decode($str));

        return true;
    }
}
