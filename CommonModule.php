<?php

namespace Iliich246\YicmsCommon;

use Iliich246\YicmsCommon\Base\CommonConfigDb;
use Yii;
use yii\base\BootstrapInterface;
use yii\web\IdentityInterface;
use Iliich246\YicmsCommon\Base\Generator;
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
    /** @var string namespace of admin part of yicms */
    public $yicmsNamespace = 'app\yicms';
    /** @var IdentityInterface|YicmsUserInterface */
    public $user;
    /** @var string default user language, there using language codes like 'ru-RU' or 'en-EU' */
    public $defaultLanguage = 'en-EU';
    /** @var int method that used for store information about language between requests */
    public $languageMethod = Language::COOKIE_TYPE;
    /** @var string name of directory for annotation files */
    public $annotationsDirectory = 'Models';
    /** @var bool keeps true if for this module was generated changeable admin files */
    public $isGenerated = false;
    /** @var bool if true generator will be generate in strong mode, even existed files will be replaced */
    public $strongGenerating = false;

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
        'yicmsLocation',
        'yicmsNamespace',
        'defaultLanguage',
        'languageMethod',
        'isGenerated',
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
        Yii::setAlias('@yicms-common', Yii::getAlias('@vendor') .
            DIRECTORY_SEPARATOR .
            'iliich246' .
            DIRECTORY_SEPARATOR .
            'yii2-yicms-common');

        parent::init();

        $namespace = $this->yicmsNamespace . '\Common\Controllers\\';

        $this->controllerMap['admin']        = $namespace . 'AdminController';
        $this->controllerMap['admin-fields'] = $namespace . 'AdminFieldsController';
        $this->controllerMap['admin-files']  = $namespace . 'AdminFilesController';
        $this->controllerMap['files']        = $namespace . 'AdminFilesController';
        $this->controllerMap['admin-images'] = $namespace . 'AdminImagesController';

        $this->filesPatch           = Yii::$app->basePath . $this->filesPatch;
        $this->imagesOriginalsPath  = Yii::$app->basePath . $this->imagesOriginalsPath;
        $this->imagesCropPath       = Yii::$app->basePath . $this->imagesCropPath;
        $this->imagesThumbnailsPath = Yii::$app->basePath . $this->imagesThumbnailsPath;

        $this->imagesOriginalsWebPath  = Yii::$app->homeUrl . $this->imagesOriginalsWebPath;
        $this->imagesCropWebPath       = Yii::$app->homeUrl . $this->imagesCropWebPath;
        $this->imagesThumbnailsWebPath = Yii::$app->homeUrl . $this->imagesThumbnailsWebPath;
    }

    /**
     * @inheritdoc
     * @throws CommonException
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
        $selected = $admin;

        //$user = CommonUser::findIdentity($selected);
        //Yii::$app->user->login($user);
        //Yii::$app->user->logout();
        /** END OF DEBUG CODE */

        Yii::$app->sourceLanguage = $this->defaultLanguage;
        //Language::getInstance()->setLanguage(LanguagesDb::instanceByCode('ru-RU'));
        Yii::$app->language = LanguagesDb::instanceByCode('en-EU')->code;
        //Yii::$app->language = LanguagesDb::instanceByCode('ru-RU')->code;
        //Yii::$app->language = Language::getInstance()->getCurrentLanguage()->code;

        $generator = new Generator($this);
        $generator->generate();
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
    public function getModuleDir()
    {
        return __DIR__;
    }

    /**
     * @inherited
     */
    public function isGenerated()
    {
        return !!$this->isGenerated;
    }

    /**
     * @inherited
     */
    public function setAsGenerated()
    {
        $config = CommonConfigDb::getInstance();
        $config->isGenerated = true;

        $config->save(false);
    }

    /**
     * @inherited
     */
    public function isGeneratorInStrongMode()
    {
        return !!$this->strongGenerating;
    }

    /**
     * @inherited
     */
    public static function getModuleName()
    {
        return 'Common';
    }
}
