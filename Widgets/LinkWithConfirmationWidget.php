<?php

namespace Iliich246\YicmsCommon\Widgets;

use Iliich246\YicmsCommon\CommonModule;
use yii\bootstrap\Widget;

/**
 * Class LinkWithConfirmationWidget
 *
 * Render link with confirmation. She will output alert bootbox message before apply link;
 *
 * @author iliich246 <iliich246@gmail.com>
 */
//Example for copy-paste insert ---->
//
//<?= LinkWithConfirmationWidget::widget([
//    'url' => Url::toRoute(['']),
//    'title' => 'Delete field',
//    'caption' => 'Delete Field',
//    'message' => 'WARNING! Do not delete ...',
//    'withBlock' => true,
//    'viaPjax' => true
//])
class LinkWithConfirmationWidget extends Widget
{
    /**
     * @var string url, that will be inserted in href
     */
    public $url;

    /**
     * @var string title of bootbox block
     */
    public $title;

    /**
     * @var string message in bootbox block
     */
    public $message;

    /**
     * @var string link text
     */
    public $caption;

    /**
     * @var string label on ok button in message
     */
    public $okLabel = 'Ok';

    /**
     * @var string label of cancel button in message
     */
    public $cancelLabel = 'Cancel';

    /**
     * @var bool if true markup for all button block will be inserted
     */
    public $withBlock = true;

    /** @var bool glyphicon for button, if false will be returned standard markup */
    public $glyphicon = false;

    /** @var bool if true link will have data-pjax */
    public $viaPjax = true;

    /** @var string id of pjax container  */
    public $pjaxContainer = false;


    /**
     * @inheritdoc
     */
    public function init()
    {
        //$this->cancelLabel = CommonModule::t('app', 'Cancel');
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('link-with-confirmation',[
            'widget' => $this,
        ]);
    }
}
