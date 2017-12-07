<?php

namespace Iliich246\YicmsCommon\Fields;

use yii\bootstrap\Widget;

/**
 * Class FieldTypeWidget
 *
 * Return
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldTypeWidget extends Widget
{
    /** @var \yii\bootstrap\ActiveForm form, for render control elements in tabs  */
    public $form;
    /** @var  integer language key for forming form keys */
    public $languageKey;
    /** @var integer field key for forming form keys  */
    public $fieldKey;
    /** @var array instance of model, that`s widget will be render */
    public $fieldsArray;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return 'PENIS ';
    }
}
