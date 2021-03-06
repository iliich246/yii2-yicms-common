<?php

namespace Iliich246\YicmsCommon\Conditions;

use yii\base\Model;
use yii\widgets\ActiveForm;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AbstractGroup;

/**
 * Class ConditionsGroup
 *
 * This class implements conditions group for admin part
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionsGroup extends AbstractGroup
{
    /** @var ConditionsReferenceInterface|ConditionsInterface object for current group */
    protected $referenceAble;
    /** @var ConditionTemplate[] instances */
    public $conditionTemplates = [];
    /** @var Condition[] array of conditions */
    public $conditions = [];
    /** @var bool indicate that data in this group was saved in this action */
    public $justSaved = false;

    /**
     * @param ConditionsReferenceInterface $referenceAble
     */
    public function setConditionsReferenceAble(ConditionsReferenceInterface $referenceAble)
    {
        $this->referenceAble = $referenceAble;
    }

    /**
     * Return true, if referenceAble contains any conditions
     * @return bool
     */
    public function isConditions()
    {
        if ($this->conditions) return true;
        return false;
    }

    /**
     * Returns current condition template reference
     * @return string
     */
    public function getCurrentConditionTemplateReference()
    {
        return $this->referenceAble->getConditionTemplateReference();
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
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

        $this->conditionTemplates = $conditionsTemplatesQuery->orderBy([
            ConditionTemplate::getOrderFieldName() =>SORT_ASC
        ])->indexBy('id')
          ->all();

        foreach($this->conditionTemplates as $conditionTemplate) {
            $condition = Condition::find()->where([
                'condition_reference'          => $this->referenceAble->getConditionReference(),
                'common_condition_template_id' => $conditionTemplate->id,
            ])->one();

            if (!$condition) {
                $condition                               = new Condition();
                $condition->common_condition_template_id = $conditionTemplate->id;
                $condition->condition_reference          = $this->referenceAble->getConditionReference();
                $condition->common_value_id              = $conditionTemplate->defaultValueId();
                $condition->editable                     = true;
                $condition->checkbox_state               = $conditionTemplate->defaultCheckboxValue();

                if ($conditionTemplate->type == ConditionTemplate::TYPE_CHECKBOX)
                    $condition->value = (string)$condition->checkbox_state;

                $condition->save();

                $this->referenceAble->getConditionsHandler()->setToCache($conditionTemplate->program_name, $condition);
            }

            $this->conditions["$conditionTemplate->id"] = $condition;
        }
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return Model::validateMultiple($this->conditions);
    }

    /**
     * @inheritdoc
     */
    public function load($data)
    {
        return Model::loadMultiple($this->conditions, $data);
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        $success = true;

        foreach($this->conditions as $condition) {
            if (!$success) return false;
            $success = $condition->save();
        }

        return true;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function render(ActiveForm $form, $isModal = false)
    {
        $result = '';

        if ($this->conditions) {
            $result .= ConditionRenderWidget::widget([
                'form'            => $form,
                'conditionsArray' => $this->conditions,
                'isModal'         => $isModal
            ]);
        }

        return $result;
    }
}
