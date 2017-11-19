<?php

namespace Iliich246\YicmsCommon\Languages;

use Yii;
use yii\base\Model;
use Iliich246\YicmsCommon\Base\CommonConfigDb;
use Iliich246\YicmsCommon\Base\CommonException;

/**
 * Class DefaultLanguageForm
 *
 * Used for configure languages in developer panel
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DefaultLanguageForm extends Model
{
    /** @var integer selected default language */
    public $defaultLanguage;

    /** @var integer method for save data about selected language  */
    public $languageMethod;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'defaultLanguage' => 'Default language',
            'languageMethod' => 'Language method'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['defaultLanguage', 'languageMethod'], 'integer'],
            ['defaultLanguage', 'validateProgramName'],


        ];
    }

    /**
     * Validates the program name.
     * This method serves as the inline validation for page program name.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateProgramName($attribute, $params)
    {
        //if (!$this->hasErrors()) {

            //$this->addError($attribute, 'Error sample at' . $attribute);
        //}
    }

    /**
     * Constructor
     * @param array $config
     * @throws CommonException
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->loadConfig();
    }

    /**
     * Load module
     * @throws CommonException
     */
    private function loadConfig()
    {
        $config = CommonConfigDb::getInstance();

        /** @var LanguagesDb $defaultLanguage */
        $defaultLanguage = LanguagesDb::find()
                        ->where(['code' => $config->defaultLanguage])
                        ->one();

        if (!$defaultLanguage)
            throw new CommonException('Can not load default language from db');

        $this->defaultLanguage = $defaultLanguage->id;
        $this->languageMethod = $config->languageMethod;
    }

    /**
     * Save language config parameters to db
     * @return bool
     * @throws CommonException
     */
    public function save()
    {
        $config = CommonConfigDb::getInstance();

        $needToSave = false;

        /** @var LanguagesDb $defaultLanguageDb */
        $defaultLanguageDb = LanguagesDb::findOne($this->defaultLanguage);

        if (!$defaultLanguageDb)
            throw new CommonException('Cant fetch language with id = ' . $this->defaultLanguage . ' from DB');

        if ($defaultLanguageDb->code != $config->defaultLanguage) {
            $config->defaultLanguage = $defaultLanguageDb->code;
            $needToSave = true;
        }

        if ($this->languageMethod != $config->languageMethod) {
            $config->languageMethod = $this->languageMethod;
            $needToSave = true;
        }

        if (!$needToSave) return true;
        return $config->save();
    }

    /**
     * Return array of used languages for dropDown
     * @return array
     * @throws CommonException
     */
    public function getLanguagesList()
    {
        $langArray = Language::getInstance()->usedLanguages();
        $result = [];

        foreach($langArray as $lang)
            $result[$lang->id] = $lang->name . '(' . $lang->code . ')';

        return $result;
    }

    /**
     * Return list of language methods for dropDown
     * @return array
     * @throws CommonException
     */
    public function getLanguagesMethodList()
    {
        return Language::getInstance()->languageMethodsList();
    }
}
