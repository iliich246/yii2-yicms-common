<?php

namespace Iliich246\YicmsCommon\Images;

use Yii;
use yii\helpers\Url;
use yii\bootstrap\Widget;
use Iliich246\YicmsCommon\Fields\FieldTemplate;

/**
 * Class ImagesDevModalWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ImagesDevModalWidget extends Widget
{
    /** @var DevImagesGroup */
    public $devImagesGroup;
    /** @var bool true means that widget initialized after success data save in DevImagesGroup */
    public $dataSaved = false;
    /** @var string part of link for delete images block template */
    public $deleteLink;
    /** @var string keeps current form action */
    public $action;
    /** @var FieldTemplate[] array of translate able fields for current image block template */
    public $fieldTemplatesTranslatable;
    /** @var FieldTemplate[] array of single fields for current image block template */
    public $fieldTemplatesSingle;
    /** @var string if true widget must close modal window after save data */
    public $saveAndExit = 'false';
    /** @var string keeps path for annotating */
    public $annotatePath;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->deleteLink = Url::toRoute(['/common/dev-images/delete-image-block-template']);

        if (Yii::$app->request->post('_saveAndExit'))
            $this->saveAndExit = 'true';
    }

    /**
     * Returns name of form name of widget
     * @return string
     */
    public static function getFormName()
    {
        return 'create-update-images';
    }

    /**
     * Return name of modal window of widget
     * @return string
     */
    public static function getModalWindowName()
    {
        return 'imagesDevModal';
    }

    /**
     * Returns name of pjax container for this widget
     * @return string
     */
    public static function getPjaxContainerId()
    {
        return 'images-pjax-container';
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('images_dev_modal_widget', [
            'widget' => $this
        ]);
    }
}
