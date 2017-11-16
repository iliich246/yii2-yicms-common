<?php

namespace Iliich246\YicmsCommon\Languages;

use Yii;
use Iliich246\YicmsCommon\Base\CommonException;

/**
 * Class SessionLanguageMethod
 *
 * Implements methods of saving and restore language using sessions
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class SessionLanguageMethod implements LanguageMethodInterface
{
    /**
     * @inheritdoc
     * @param LanguagesDb $language
     */
    public function setLanguage(LanguagesDb $language)
    {
        throw new CommonException('No implementation of session language methods');
    }

    /**
     * @inheritdoc
     */
    public function findLanguage()
    {
        throw new CommonException('No implementation of session language methods');
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return Language::SESSION_TYPE;
    }
}
