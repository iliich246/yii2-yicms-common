<?php

namespace Iliich246\YicmsCommon;

use Yii;
use yii\base\BootstrapInterface;
use yii\web\IdentityInterface;
use Iliich246\YicmsCommon\Base\CommonUser;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Base\YicmsUserInterface;
use Iliich246\YicmsCommon\Base\YicmsModuleInterface;
use Iliich246\YicmsCommon\Base\AbstractConfigurableModule;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;

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
    /** @var IdentityInterface|YicmsUserInterface */
    public $user;
    /** @var string default user language, there using language codes like 'ru-RU' or 'en-EU' */
    public $defaultLanguage = 'en-EU';
    /** @var int method that used for store information about language between requests */
    public $languageMethod = Language::COOKIE_TYPE;

    /**
     * Block of fields with various paths
     */
    public $filesPatch           = DIRECTORY_SEPARATOR .
                                  'web' . DIRECTORY_SEPARATOR .
                                  'files' . DIRECTORY_SEPARATOR;

    public $imagesOriginalsPath  = DIRECTORY_SEPARATOR .
                                  'web' . DIRECTORY_SEPARATOR .
                                  'images' . DIRECTORY_SEPARATOR .
                                  'orig' . DIRECTORY_SEPARATOR;

    public $imagesCropPath       = DIRECTORY_SEPARATOR .
                                  'web' . DIRECTORY_SEPARATOR .
                                  'images' . DIRECTORY_SEPARATOR .
                                  'crop' . DIRECTORY_SEPARATOR;

    public $imagesThumbnailsPath = DIRECTORY_SEPARATOR .
                                   'web' . DIRECTORY_SEPARATOR .
                                   'images' . DIRECTORY_SEPARATOR .
                                   'thumb' . DIRECTORY_SEPARATOR;

    /**
     * Block of variables with images web paths
     */
    public $imagesOriginalsWebPath  = 'images/orig/';

    public $imagesCropWebPath       = 'images/crop/';

    public $imagesThumbnailsWebPath = 'images/thumb/';



    /** @inheritdoc */
    public $configurable = [
        'defaultLanguage',
        'languageMethod',
    ];

    /** @inheritdoc */
    public $controllerMap = [
        'dev'             => 'Iliich246\YicmsCommon\Controllers\DeveloperController',
        'dev-fields'      => 'Iliich246\YicmsCommon\Controllers\DeveloperFieldsController',
        'dev-validators'  => 'Iliich246\YicmsCommon\Controllers\DeveloperValidatorsController',
        'dev-files'       => 'Iliich246\YicmsCommon\Controllers\DeveloperFilesController',
        'dev-images'      => 'Iliich246\YicmsCommon\Controllers\DeveloperImagesController',
        'dev-conditions'  => 'Iliich246\YicmsCommon\Controllers\DeveloperConditionsController',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        //TODO: change namespace to correct $yicmsLocation
        $this->controllerMap['admin'] = 'app\yicms\Common\Controllers\AdminController';
        $this->controllerMap['admin-fields'] = 'app\yicms\Common\Controllers\AdminFieldsController';
        $this->controllerMap['admin-files'] = 'app\yicms\Common\Controllers\AdminFilesController';
        $this->controllerMap['files'] = 'app\yicms\Common\Controllers\AdminFilesController';
        $this->controllerMap['admin-images'] = 'app\yicms\Common\Controllers\AdminImagesController';

        $this->filesPatch           = Yii::$app->basePath . $this->filesPatch;
        $this->imagesOriginalsPath  = Yii::$app->basePath . $this->imagesOriginalsPath;
        $this->imagesCropPath       = Yii::$app->basePath . $this->imagesCropPath;
        $this->imagesThumbnailsPath = Yii::$app->basePath . $this->imagesThumbnailsPath;

        $this->imagesOriginalsWebPath  = Yii::$app->homeUrl . $this->imagesOriginalsWebPath;
        $this->imagesCropWebPath       = Yii::$app->homeUrl . $this->imagesCropWebPath;
        $this->imagesThumbnailsWebPath = Yii::$app->homeUrl . $this->imagesThumbnailsWebPath;

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

        /** TODO: This is debug code; Important to delete this in production */

        $dev = -1;
        $admin = 0;

        $selected = $dev;
//        $selected = $admin;

        $user = CommonUser::findIdentity($selected);
        Yii::$app->user->login($user);

        /** END OF DEBUG CODE */

        Yii::$app->sourceLanguage = $this->defaultLanguage;
        //Language::getInstance()->setLanguage(LanguagesDb::instanceByCode('ru-RU'));
        //Yii::$app->language = LanguagesDb::instanceByCode('en-EU')->code;
        Yii::$app->language = LanguagesDb::instanceByCode('ru-RU')->code;
        //Yii::$app->language = Language::getInstance()->getCurrentLanguage()->code;

    }

    /**
     * Returns true is current user is dev
     * @return bool
     */
    public static function isUnderDev()
    {
        if (Yii::$app->user->isGuest) return false;
        /** @var $user IdentityInterface|YicmsUserInterface */
        $user = Yii::$app->user->identity;
        return $user->isThisDev();
    }

    /**
     * Returns true is current user is admin
     * @return bool
     */
    public static function isUnderAdmin()
    {
        if (Yii::$app->user->isGuest) return false;
        /** @var $user IdentityInterface|YicmsUserInterface */
        $user = Yii::$app->user->identity;
        return $user->isThisAdmin();
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
