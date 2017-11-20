<?php

namespace Iliich246\YicmsCommon;

use Yii;
use yii\base\BootstrapInterface;
use Iliich246\YicmsCommon\Base\AbstractConfigurableModule;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class CommonModule
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class CommonModule extends AbstractConfigurableModule implements BootstrapInterface
{
    /**
     * @var string default user language, there using language codes like 'ru-RU' or 'en-EU'
     */
    public $defaultLanguage = 'en-EU';

    /**
     * @var int method that used for store information about language between requests
     */
    public $languageMethod = Language::COOKIE_TYPE;

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
    public function getModuleName()
    {
        return 'Common';
    }
}
