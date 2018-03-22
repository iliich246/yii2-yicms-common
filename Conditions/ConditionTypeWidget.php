<?php

namespace Iliich246\YicmsCommon\Conditions;

use yii\base\Model;
use yii\bootstrap\Widget;
use Iliich246\YicmsCommon\Base\CommonException;

/**
 * Class ConditionTypeWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionTypeWidget extends Widget
{
    /**
     * @var \yii\bootstrap\ActiveForm form, for render control elements in tabs
     */
    public $form;
    /**
     * @var Condition instance of condition, that`s widget will be render
     */
    public $condition;
    /**
     * @var bool if true widget will render in modal window mode
     */
    public $isModal;

    /**
     * @inheritdoc
     */
    public function run()
    {
        switch($this->condition->getType()) {
            case(ConditionTemplate::TYPE_CHECKBOX): {
                $view = 'type_checkbox';
                break;
            }
            case(ConditionTemplate::TYPE_RADIO): {
                $view = 'type_radio';
                break;
            }
            case(ConditionTemplate::TYPE_SELECT): {
                $view = 'type_select';
                break;
            }
            default:
                throw new CommonException("Unknown type of condition");
        }

        return $view;

        //return $this->render($view, [
        //    'widget' => $this
        //]);
    }

}
