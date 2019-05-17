<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;
use yii\base\ActionFilter;
use yii\web\NotFoundHttpException;


/**
 * Class AdminFilter
 *
 * This filter uses in admin section for prevent access not permitted users.
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class AdminFilter extends ActionFilter
{
    /* @var mixed for callback */
    public $redirect;

    /**
     * Before admin action
     * @param \yii\base\Action $action
     * @return bool
     * @throws NotFoundHttpException
     */
    public function beforeAction($action)
    {


        if (Yii::$app->user->isGuest) {
            if ($this->redirect) return call_user_func($this->redirect);
            throw new NotFoundHttpException();
        }

        /** @var YicmsUserInterface $user */
        $user = Yii::$app->user->identity;

        if ($user->isThisDev()) return parent::beforeAction($action);
        //throw new \yii\base\Exception(print_r($user, true));
        if (!$user->isThisAdmin()) {

            if ($this->redirect) return call_user_func($this->redirect);
            throw new NotFoundHttpException();
        }

        return parent::beforeAction($action);
    }
}
