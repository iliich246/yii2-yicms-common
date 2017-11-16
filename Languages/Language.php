<?php

namespace Iliich246\YicmsCommon\Languages;

use Yii;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\CommonModule;

/**
 * Class Language
 *
 * Part of common module for work with system languages
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Language
{
    const COOKIE_TYPE = 1;
    const SESSION_TYPE = 2;

    /**
     * @var LanguageMethodInterface current language method object
     */
    private $languageMethod;

    /**
     * @var LanguagesDb current system language
     */
    private $currentLanguage = null;

    /**
     * @var LanguagesDb[] languages used in system
     */
    private $usedLanguages = null;

    /** @var Language instance for singleton */
    private static $instance = null;

    /**
     * Constructor
     * @throws CommonException
     */
    private function __construct()
    {
        if (!CommonModule::getInstance()) {
            Yii::error('Incorrect configuration of common module.
                Add him it bootstrap part in Yii configuration', __METHOD__);
            throw new CommonException('Incorrect configuration of common module.
                Add him it bootstrap part in Yii configuration');
        }

        switch(CommonModule::getInstance()->languageMethod) {
            case(self::COOKIE_TYPE): {
                $this->languageMethod = new CookieLanguageMethod();
                break;
            }
            case(self::SESSION_TYPE): {
                $this->languageMethod = new SessionLanguageMethod();
                break;
            }
            default: {
                $languageMethod = CommonModule::getInstance()->languageMethod;
                Yii::error("Unknown language method ($languageMethod)", __METHOD__);
                throw new CommonException("Unknown language method ($languageMethod)");
            }
        }
    }

    /**
     * Return instance of singleton
     * @return Language
     * @throws CommonException
     */
    public static function getInstance()
    {
        if (!self::$instance)
            self::$instance = new self();

        return self::$instance;
    }

    /**
     * @inheritdoc
     * @see LanguageMethodInterface
     */
    public function setLanguage(LanguagesDb $language)
    {
        $this->languageMethod->setLanguage($language);
    }

    /**
     * @inheritdoc
     * @see LanguageMethodInterface
     */
    public function findLanguage()
    {
        $this->languageMethod->findLanguage();
    }

    /**
     * Return current system language object
     * @return LanguagesDb
     * @throws CommonException
     */
    public function getCurrentLanguage()
    {
        if ($this->currentLanguage)
            return $this->currentLanguage;

        $language = Yii::$app->language;

        $languageDb = LanguagesDb::find()
            ->where(['code' => $language])
            ->one();

        if (!$languageDb)
            throw new CommonException("Can`t find language in database (language code = $language)");

        $this->currentLanguage = $languageDb;

        return $this->currentLanguage;
    }

    /**
     * Returns array of language objects that used in system
     * @return LanguagesDb[]
     * @throws CommonException
     */
    public function usedLanguages()
    {
        if ($this->usedLanguages)
            return $this->usedLanguages;

        /** @var LanguagesDb[] $languages */
        $languages = LanguagesDb::find()
            ->where(['used' => true])
            ->indexBy('id')
            ->all();

        if (!$languages) {
            Yii::error("Can't fetch data from language table", __METHOD__);
            throw new CommonException("Can't fetch data from language table");
        }

        $this->usedLanguages = $languages;

        return $this->usedLanguages;
    }

    /**
     * Return list of existed language methods with them names
     * @return array
     */
    public function languageMethodsList()
    {
        return [
            self::COOKIE_TYPE => 'COOKIE TYPE',
            self::SESSION_TYPE => 'SESSION TYPE'
        ];
    }
}
