<?php

namespace Iliich246\YicmsCommon\Languages;

use Yii;
use yii\web\Cookie;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\CommonException;

/**
 * Class CookieLanguageMethod
 *
 * Implements methods of saving and restore language using cookies
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class CookieLanguageMethod implements LanguageMethodInterface
{
    /** Name used in cookies for keep language */
    const COOKIE_PREFIX = 'yicms_language';

    /**
     * @inheritdoc
     */
    public function setLanguage(LanguagesDb $language)
    {
        $cookies = Yii::$app->response->cookies;

        $cookies->add(new Cookie([
            'name' => self::COOKIE_PREFIX,
            'value' => $language->code,
        ]));
    }

    /**
     * @inheritdoc
     */
    public function findLanguage()
    {
        /** @var $module CommonModule */
        if (!($module = CommonModule::getInstance()))
            throw new CommonException('Wrong initialized common module (check that he exist in bootstrap area)');

        Yii::$app->language =
            Yii::$app->request->cookies->getValue(self::COOKIE_PREFIX, $module->defaultLanguage);
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return Language::COOKIE_TYPE;
    }
}
