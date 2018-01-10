<?php

namespace Iliich246\YicmsCommon\Conditions;

use yii\base\Model;
use yii\widgets\ActiveForm;
use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class DevConditionsGroup
 *
 *  @author iliich246 <iliich246@gmail.com>
 */
class DevConditionsGroup extends AbstractGroup
{
    /**
     * @var string conditionTemplateReference value for current group
     */
    protected $conditionTemplateReference;
    /**
     * @var ConditionTemplate current condition block template with group is working (create or update)
     */
    public $conditionTemplate;
    /**
     * @var ConditionNamesTranslatesForm[]
     */
    public $conditionNameTranslates;

    /**
     * Sets conditionTemplateReference
     * @param $conditionTemplateReference
     */
    public function setConditionsTemplateReference($conditionTemplateReference)
    {
        $this->conditionTemplateReference = $conditionTemplateReference;
    }

    /**
     * @inheritdoc
     */
    public function initialize($filesBlockId = null)
    {

    }

    /**
     * @inheritdoc
     */
    public function validate()
    {

    }

    /**
     * @inheritdoc
     */
    public function load($data)
    {

    }

    /**
     * @inheritdoc
     */
    public function save()
    {

    }

    /**
     * @inheritdoc
     */
    public function render(ActiveForm $form)
    {
        throw new CommonException('Not implemented for developer conditions (not necessary)');
    }
}
