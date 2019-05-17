<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;
use yii\base\Model;

/**
 * Class LoginModel
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class LoginModel extends Model
{
    /** @var string name of user  */
    public $userName;
    /** @var string user password  */
    public $password;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userName' => 'User name',
            'password' => 'Password',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userName', 'password'], 'required', 'message' => 'Необходимо заполнить поле'],
            ['password', 'validatePassword']
        ];
    }

    /**
     * Validate password
     * @param $attribute
     * @param $params
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {

            if ($this->userName != 'admin') $this->addError($attribute, 'Wrong user/password');

            $checkHash = CommonHashForm::ADMIN_HASH;

            if (!Yii::$app->security->validatePassword($this->password, $checkHash))
                $this->addError($attribute, 'Wrong user/password');
        }
    }

    /**
     * Set user as admin
     * @return bool
     */
    public function login()
    {
        $user = new CommonUser();
        return $user->setAsAdmin();
    }
}
