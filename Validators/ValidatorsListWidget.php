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
     * @var string url for returning to requester of validators block
     */
    public $returnUrl;

    /**
     * @var string name of pjax container that`s contains that widget
     */
    public $ownerPjaxContainerName;

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
