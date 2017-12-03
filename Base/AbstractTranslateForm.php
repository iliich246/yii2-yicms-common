<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\Languages\LanguagesDb;

/**
 * Class AbstractTranslateForm model
 *
 * This class must be inherited by all descendants, which realize work with translates.
 *
 * @property integer $key
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractTranslateForm extends Model
{
    /**
     * Scenarios
     */
    const SCENARIO_CREATE = 0;
    const SCENARIO_UPDATE = 1;

    /**
     * @var LanguagesDb instance of language that associated with model
     */
    protected $language;
    /**
     * @var ActiveRecord instance of translate db associated with model
     */
    protected $currentTranslateDb;
    /**
     * @var string keep name of model for id
     */
    private $idName;

    /**
     * Language setter
     * @param LanguagesDb $language
     */
    public function setLanguage(LanguagesDb $language)
    {
        $this->language = $language;
    }

    /**
     * Returns name of language of current model
     * @return string
     */
    public function getLanguageName()
    {
        return $this->language->name;
    }

    /**
     * Returns language id of current model language
     * @return string
     */
    public function getLanguageId()
    {
        return $this->language->id;
    }

    /**
     * Return true, if current language is active for this model
     * @return bool
     */
    public function isActive()
    {
        return $this->language->isActive();
    }

    /**
     * Return Id name of model for tabs
     * @return string
     */
    public function getIdName()
    {
        if ($this->idName) return $this->idName;
        $this->idName = $this->formName() . '_' . $this->language->id;
        return $this->idName;
    }

    /**
     * Return key for input elements
     * Key used there: <?= $form->field($translateModel, "[<key>]param")->textInput() ?>
     * @return int
     */
    public function getKey()
    {
        return $this->language->id;
    }

    /**
     * Returns view name, that`s must be defined in descendants
     * @return string
     */
    public static function getViewName()
    {
        return static::getViewName();
    }

    /**
     * Return true, if object correctly configured
     * @return bool
     */
    protected function isCorrectConfigured()
    {
        if (!$this->language) return false;
        return true;
    }

    /**
     * Loads translate from db, if cant find existed create new correct record in db
     * @throws CommonException
     */
    public function loadFromDb()
    {
        if (!$this->isCorrectConfigured()) {
            Yii::error('Wrong initialization of ' . $this::className() . 'object');
            throw new CommonException('Wrong initialization of ' . $this::className() . 'object');
        }

        $this->getCurrentTranslateDb();
    }

    /**
     * Return translate db object associated with this model
     * @return bool
     */
    abstract function getCurrentTranslateDb();

    /**
     * Creates new translate db object associated with this model
     * @return bool
     */
    abstract protected function createTranslateDb();
}
