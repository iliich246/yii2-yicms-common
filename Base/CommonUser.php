<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;
use yii\web\IdentityInterface;
use yii\web\NotFoundHttpException;

/**
 * Class CommonUser
 *
 * Basic yicms user class (define developer and admin roles)
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class CommonUser implements
    IdentityInterface,
    YicmsUserInterface
{
    /** @var bool developer identification */
    private $isDev = false;
    /** @var bool admin identification */
    private $isAdmin = false;

    /**
     * Try to login user as developer
     * @return bool
     * @throws NotFoundHttpException
     */
    public function loginAsDev()
    {
        $rootHash = Yii::$app->request->get('hash');
        $rootKey = Yii::$app->request->get('asDev');

        if (!isset($rootHash)) return false;

        if (!$rootHash || !isset($rootKey)) return false;

        $checkHash = CommonHashForm::DEV_HASH;

        if (!Yii::$app->security->validatePassword($rootHash, $checkHash)) return false;
        $this->isDev = true;

        Yii::$app->user->login($this);
        return true;
    }

    /**
     * Try to login as admin
     * @return bool
     */
    public function loginAsAdmin()
    {
        $adminHash = Yii::$app->request->get('hash');
        $adminKey = Yii::$app->request->get('asAdmin');

        if (!isset($adminHash)) return false;

        if (!$adminHash || !isset($adminKey)) return false;

        $checkHash = CommonHashForm::ADMIN_HASH;

        if (!Yii::$app->security->validatePassword($adminHash, $checkHash)) return false;
        $this->isAdmin = true;

        Yii::$app->user->login($this);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isThisDev()
    {
        return $this->isDev;
    }

    /**
     * @inheritdoc
     */
    public static function isDev()
    {
        /** @var self $user */
        $user = Yii::$app->user->identity;

        if (!$user) return false;

        return $user->isThisDev();
    }

    /**
     * @inheritdoc
     */
    public function isThisAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * @inheritdoc
     */
    public static function isAdmin()
    {
        /** @var self $user */
        $user = Yii::$app->user->identity;

        if (!$user) return false;

        return $user->isAdmin();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        if ($id == -1) {
            $user =  new self();
            $user->isDev = true;

            return $user;
        }

        if ($id == 0) {
            $user =  new self();
            $user->isAdmin = true;

            return $user;
        }

        return new self();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        if ($this->isDev)
            return -1;
        if ($this->isAdmin)
            return 0;

        return null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new CommonException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        throw new CommonException('"getAuthKey" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        throw new CommonException('"validateAuthKey" is not implemented.');
    }
}
