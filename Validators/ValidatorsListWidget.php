<?php

namespace Iliich246\YicmsCommon\Validators;

use yii\bootstrap\Widget;
use Iliich246\YicmsCommon\Validators\ValidatorReferenceInterface;

/**
 * Class FieldsDevValidatorWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ValidatorsListWidget extends Widget
{
    /**
     * @var ValidatorReferenceInterface instance of associated field template
     */
    public $validatorReference;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('validators_list_widget', [
            'widget' => $this
        ]);
    }
}
