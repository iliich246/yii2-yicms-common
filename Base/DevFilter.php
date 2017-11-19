<?php

namespace Iliich246\YicmsCommon\Base;

use Yii;
use yii\base\ActionFilter;
use yii\web\NotFoundHttpException;

/**
 * Class DevFilter
 *
 * This filter uses in developer section for prevent access not permitted users.
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DevFilter extends ActionFilter
{
    /* @var mixed for callback */
    public $redirect;

    /**
     * Before root action
     * @param \yii\base\Action $action
     * @return bool
     * @throws NotFoundHttpException
     */
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) throw new NotFoundHttpException('1');

        /** @var CommonUser $user */
        $user = Yii::$app->user->identity;

        if (!$user->isDev()) {
            if ($this->redirect) call_user_func($this->redirect);
            throw new NotFoundHttpException();
        }

        return parent::beforeAction($action);
    }
}
