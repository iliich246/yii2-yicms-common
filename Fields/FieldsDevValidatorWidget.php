<?php

namespace Iliich246\YicmsCommon\Fields;

use yii\bootstrap\Widget;

/**
 * Class FieldsDevValidatorWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldsDevValidatorWidget extends Widget
{
    public $fieldTemplateId;
    /**
     * @inheritdoc
     */
    public function init()
    {

    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('fields_dev_validator_widget', [
            'widget' => $this
        ]);
    }
}
