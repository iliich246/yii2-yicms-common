<?php

namespace Iliich246\YicmsCommon\Languages;

use Iliich246\YicmsCommon\CommonModule;

/**
 * Interface ILanguage
 *
 * This interface define methods, that must implement classes that realizes ways work with languages
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface LanguageMethodInterface
{
    /**
     * Set language to store
     * @param LanguagesDb $language
     * @return mixed
     */
    public function setLanguage(LanguagesDb $language);

    /**
     * Gets language from the store and sets in yii config
     * @return mixed
     */
    public function findLanguage();

    /**
     * Returns type language method method
     * @return integer
     */
    public function getType();
}
