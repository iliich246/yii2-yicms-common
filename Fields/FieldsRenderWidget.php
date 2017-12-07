<?php

namespace Iliich246\YicmsCommon\Fields;

use yii\bootstrap\Widget;

/**
 * Class FieldsRenderWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldsRenderWidget extends Widget
{
    /**
     * @var \yii\bootstrap\ActiveForm form, for render control elements in tabs
     */
    public $form;

    /**
     * @var array of translate models
     */
    public $fieldsArray;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!$this->fieldsArray) return false;
        //TODO: makes correct handle of this error

        if (count($this->fieldsArray) == 1)
            return $this->render('single_fields',[
                'widget' => $this,
                'fieldsArray' => current($this->fieldsArray)
            ]);

        return $this->render('tabs',[
            'widget' => $this,
        ]);
    }

    /**
     * Proxy isActive method from PagesFieldTranslateModel
     * @param array $modelsList
     * @return bool
     */
    public function isActive(array $modelsList)
    {
        /** @var $translateModel FieldTranslateForm */
        $translateModel = current($modelsList);

        return $translateModel->isActive();
    }

    /**
     * Proxy getIdName method from PagesFieldTranslateModel
     * @param array $modelsList
     * @return string
     */
    public function getIdName(array $modelsList)
    {
        /** @var $translateModel FieldTranslateForm */
        $translateModel = current($modelsList);

        return $translateModel->getIdName();
    }

    /**
     * Proxy getLanguageName method from PagesFieldTranslateModel
     * @param array $modelsList
     * @return string
     */
    public function getLanguageName(array $modelsList)
    {
        /** @var $translateModel FieldTranslateForm */
        $translateModel = current($modelsList);

        return $translateModel->getLanguageName();
    }
}
