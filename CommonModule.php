<?php

namespace Iliich246\YicmsCommon;

use Yii;
use yii\base\BootstrapInterface;
use yii\web\IdentityInterface;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Base\YicmsUserInterface;
use Iliich246\YicmsCommon\Base\YicmsModuleInterface;
use Iliich246\YicmsCommon\Base\AbstractConfigurableModule;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class CommonModule
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class CommonModule extends AbstractConfigurableModule implements
    BootstrapInterface,
    YicmsModuleInterface
{
    /**
     * @var string path to yicms files, changeable by developers; Also in that place code generators and annotators
     * will place generated code
     */
    public $yicmsLocation = '@app/yicms';

    //public $

    /** @var IdentityInterface|YicmsUserInterface  */
    public $user;

    /**
     * @var string default user language, there using language codes like 'ru-RU' or 'en-EU'
     */
    public $defaultLanguage = 'en-EU';
    /**
     * @var int method that used for store information about language between requests
     */
    public $languageMethod = Language::COOKIE_TYPE;

    /** @inheritdoc */
    public $configurable = [
        'defaultLanguage',
        'languageMethod',
    ];

    /** @inheritdoc */
    public $controllerMap = [
        'dev' => 'Iliich246\YicmsCommon\Controllers\DeveloperController',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->controllerMap['admin'] = $this->yicmsLocation . '/Common/Controllers/AdminController';
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $interfaces = class_implements(Yii::$app->user->identityClass);

        if(!$interfaces || !in_array('Iliich246\YicmsCommon\Base\YicmsUserInterface', $interfaces))
            throw new CommonException(
            'For yicms user class must implements interface YicmsUserInterface, you can use this config
                ...
                \'user\' => [
                \'identityClass\' => \'Iliich246\YicmsCommon\Base\CommonUser\',
                \'enableAutoLogin\' => true,
            ],
            ...
            or define yourself user class that will be implements YicmsUserInterface interface
            ');

        Yii::$app->sourceLanguage = $this->defaultLanguage;
        Yii::$app->language = Language::getInstance()->getCurrentLanguage()->code;
    }

    /**
     * Returns true is current user is dev
     * @return bool
     */
    public static function isUnderDev()
    {
        /** @var IdentityInterface|YicmsUserInterface $class */
        $class = Yii::$app->user->identityClass;
        return $class::isDev();
    }

    /**
     * Returns true is current user is admin
     * @return bool
     */
    public static function isUnderAdmin()
    {
        /** @var IdentityInterface|YicmsUserInterface $class */
        $class = Yii::$app->user->identityClass;
        return $class::isAdmin();
    }

    /**
     * @inherited
     */
    public function getNameSpace()
    {
        return __NAMESPACE__;
    }

    /**
     * @inherited
     */
    public static function getModuleName()
    {
        return 'Common';
    }
}
