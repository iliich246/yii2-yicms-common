<?php

namespace Iliich246\YicmsCommon\Fields;

use yii\base\Model;
use yii\bootstrap\Widget;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Base\FictiveInterface;

/**
 * Class FieldTypeWidget
 *
 * This widget render concrete field by his type
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldTypeWidget extends Widget
{
    /** @var \yii\bootstrap\ActiveForm form, for render control elements in tabs */
    public $form;
    /** @var Model|FieldRenderInterface|FictiveInterface instance of model, that`s widget will be render */
    public $fieldModel;
    /** @var bool if true widget will render in modal window mode */
    public $isModal;

    /**
     * @inheritdoc
     * @throws CommonException
     */
    public function run()
    {
        switch($this->fieldModel->getType()) {
            case(FieldTemplate::TYPE_INPUT): {
                $view = "type_input";
                break;
            }
            case(FieldTemplate::TYPE_TEXT): {
                $view = "type_text_area";
                break;
            }
            case(FieldTemplate::TYPE_REDACTOR): {
                $view = "type_redactor";
                break;
            }
            default:
                throw new CommonException("Unknown type of field");
        }

        return $this->render($view, [
            'widget' => $this
        ]);
    }
}
