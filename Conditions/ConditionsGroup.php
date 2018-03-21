<?php

namespace Iliich246\YicmsCommon\Conditions;

use yii\base\Model;
use yii\widgets\ActiveForm;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class ConditionsGroup
 *
 * This class implements conditions group for admin part
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionsGroup extends AbstractGroup
{
    /**
     * @var ConditionsReferenceInterface|ConditionsInterface object for current group
     */
    protected $referenceAble;

    /**
     * @param ConditionsReferenceInterface $referenceAble
     */
    public function setConditionsReferenceAble(ConditionsReferenceInterface $referenceAble)
    {
        $this->referenceAble = $referenceAble;
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        $conditionsTemplatesQuery = ConditionTemplate::getListQuery(
            $this->referenceAble->getConditionTemplateReference()
        );

        if (CommonModule::isUnderAdmin())
            $conditionsTemplatesQuery->andWhere([
                'editable' => true,
            ]);

        $conditionsTemplatesQuery->orderBy([
            ConditionTemplate::getOrderFieldName() =>SORT_ASC
        ])->indexBy('id');
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
    public function render(ActiveForm $form, $isModal = false)
    {

    }

}
