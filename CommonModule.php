<?php

namespace Iliich246\YicmsCommon;

use Yii;
use yii\base\BootstrapInterface;
use Iliich246\YicmsCommon\Base\YicmsModuleInterface;
use Iliich246\YicmsCommon\Base\AbstractConfigurableModule;
use Iliich246\YicmsCommon\Languages\Language;
use yii\base\Exception;

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
        'dev' => 'Iliich246\YicmsCommon\Controllers\DeveloperController'
    ];

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        //Yii::setAlias('@yicms-common', '@vendor/iliich246/yii2-yicms/common');

        Yii::$app->sourceLanguage = $this->defaultLanguage;
        Yii::$app->language = Language::getInstance()->getCurrentLanguage()->code;
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
