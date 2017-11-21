<?php

namespace Iliich246\YicmsCommon\Base;

use yii\base\Controller;
use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\Languages\LanguagesDb;

/**
 * Class AbstractTranslate
 *
 * This class must be inherited by all descendants, which realize work with translates.
 *
 * @property integer $key
 *
 * @author iliich246 <iliich246@gmail.com>
 */
abstract class AbstractTranslate extends ActiveRecord
{
    /**
     * @var LanguagesDb instance of language that associated with model
     */
    protected $language;
    /**
     * @var string keep name of model for id
     */
    private $idName;
    /**
     * @var Controller needed for correct search view files of translates
     */
    private $controller;

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
        $this->idName = $this->formName() . $this->language->id;
        return $this->idName;
    }

    /**
     * Controller setter
     * @param Controller $controller
     */
    public function setController(Controller $controller)
    {
        $this->controller = $controller;
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
        if (!$this->language || !$this->controller) return false;
        return true;
    }
}
