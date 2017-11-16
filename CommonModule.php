<?php

namespace Iliich246\YicmsCommon;

use Iliich246\YicmsCommon\Base\AbstractConfigurableModule;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class CommonModule
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class CommonModule extends AbstractConfigurableModule
{
    /**
     * @var string default user language, there using language codes like 'ru-RU' or 'en-EU'
     */
    public $defaultLanguage = 'en-EU';

    /**
     * @var int method that used for store information about language between requests
     */
    public $languageMethod = Language::COOKIE_TYPE;

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
