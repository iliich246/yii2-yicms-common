<?php

namespace Iliich246\YicmsCommon\Widgets;

use Yii;
use yii\base\Widget;

/**
 * Class ReloadAlertWidget
 *
 * Widget for output messages after page reload
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ReloadAlertWidget extends Widget
{
    const TYPE_INFO = 0;
    const TYPE_SUCCESS = 1;
    const TYPE_ERROR = 2;

    /** @var null|integer type of widget  */
    public $type = null;
    /** @var null|string title of message */
    public $title = null;
    /** @var null|string message text */
    public $message = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $data = Yii::$app->session->getFlash('_reload_alert');

        if ($data) {
            $this->type    = $data['type'];
            $this->title   = $data['title'];
            $this->message = $data['message'];
        }

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('reload_alert', [
                'widget' => $this,
            ]
        );
    }

    /**
     * @param $title
     * @param bool|false $message
     */
    public static function setSuccessModal($title, $message = false)
    {
        $session = Yii::$app->session;

        $data['type'] = self::TYPE_SUCCESS;
        $data['title'] = $title;

        if ($message)
            $data['message'] = $message;
        else
            $data['message'] = $title;

        $session->setFlash('_reload_alert', $data);
    }

    /**
     * @param $title
     * @param bool|false $message
     */
    public static function setInfoModal($title, $message = false)
    {
        $session = Yii::$app->session;

        $data['type'] = self::TYPE_INFO;
        $data['title'] = $title;

        if ($message)
            $data['message'] = $message;
        else
            $data['message'] = $title;

        $session->setFlash('_reload_alert', $data);
    }

    /**
     * @param $title
     * @param bool|false $message
     */
    public static function setErrorModal($title, $message = false)
    {
        $session = Yii::$app->session;

        $data['type'] = self::TYPE_ERROR;
        $data['title'] = $title;

        if ($message)
            $data['message'] = $message;
        else
            $data['message'] = $title;

        $session->setFlash('_reload_alert', $data);
    }

    /**
     * Returns title of message
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return text message
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Returns type of widget
     * @return int|null
     */
    public function getType()
    {
        return $this->type;
    }

    public function getTypeClass()
    {
        switch($this->type) {
            case(self::TYPE_SUCCESS): return 'bootbox-success';
            case(self::TYPE_INFO):return 'bootbox-info';
            case(self::TYPE_ERROR): return 'bootbox-error';
            default: return 'bootbox-hz';
        }
    }
}
