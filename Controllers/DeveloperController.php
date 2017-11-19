<?php

namespace Iliich246\YicmsCommon\Controllers;


use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\helpers\Url;
use Iliich246\YicmsCommon\Base\DevFilter;
use Iliich246\YicmsCommon\Base\CommonUser;
use Iliich246\YicmsCommon\Base\CommonHashForm;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use Iliich246\YicmsCommon\Languages\DefaultLanguageForm;

/**
 * Class DeveloperController
 *
 * Controller for developer section in common module
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DeveloperController extends Controller
{
    /** @inheritdoc */
    public $layout = '@yicms-common/Views/layouts/developer';
    /** @inheritdoc */
    public $defaultAction = 'languages-list';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            'root' => [
//                'class' => DevFilter::className(),
//                'except' => ['login-as-root'],
//            ],
        ];
    }

    /**
     * Return list of languages and some language configs
     * @return string
     */
    public function actionLanguagesList()
    {
        $defaultLanguageForm = new DefaultLanguageForm();

        if ($defaultLanguageForm->load(Yii::$app->request->post()) && $defaultLanguageForm->validate()) {
            if ($defaultLanguageForm->save())
            {
                return $this->render('/developer/languages-list', [
                    'pjaxError' => 'Was pjax error',
                    'defaultLanguageModel' => $defaultLanguageForm,
                    'success' => true,
                ]);
            }
        }

        $languages = LanguagesDb::find()->all();

        return $this->render('/developer/languages-list', [
            'pjaxError' => false,
            'languages' => $languages,
            'defaultLanguageModel' => $defaultLanguageForm
        ]);
    }

    /**
     * Creates new system language
     * @return string
     */
    public function actionCreateLanguage()
    {
        $model = new LanguagesDb();
        $model->scenario = LanguagesDb::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post()) && !$model->validate()) {
            if ($model->save())
                return $this->redirect(Url::toRoute(['languages-list']));

            //TODO: add bootbox error
        }

        return $this->render('/developer/create-update-language', [
            'model' => $model,
        ]);
    }

    /**
     * Updates system language
     * @param $id
     * @return string|\yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionUpdateLanguage($id)
    {
        /** @var LanguagesDb $model */
        $model = LanguagesDb::findOne($id);

        if (!$model)
            throw new BadRequestHttpException('No language with same id in system');

        $model->scenario = LanguagesDb::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (!$model->save()) {
                //TODO: add bootbox error
            }
        }

        return $this->render('/developer/create-update-language', [
            'model' => $model,
        ]);
    }

    /**
     * Delete language
     * @param $id
     * @return string
     */
    public function actionDeleteLanguage($id)
    {
//        return $this->render('languagesList', [
//
//        ]);
    }

    /**
     * Action for login as dev
     * Need to send: <site>/common/dev/login-as-dev?hash=<hash>&asDev
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionLoginAsDev()
    {
        $commonUser = new CommonUser();
        if ($commonUser->loginAsDev())
            return $this->redirect(Url::toRoute('languages-list'));

        throw new NotFoundHttpException();
    }

    /**
     * Action for change dev hash
     * @return string
     */
    public function actionChangeDevHash()
    {
        $model = new CommonHashForm();
        $model->scenario = CommonHashForm::SCENARIO_CHANGE_DEV;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->changeDev())
                return $this->redirect(Url::toRoute('languages-list'));
        }

        return $this->render('/developer/change-hash', [
            'model' => $model
        ]);
    }

    /**
     * Action for change admin hash
     * @return string|\yii\web\Response
     */
    public function actionChangeAdminHash()
    {
        $model = new CommonHashForm();
        $model->scenario = CommonHashForm::SCENARIO_CHANGE_ADMIN;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->changeAdmin())
                return $this->redirect(Url::toRoute('languages-list'));
        }

        return $this->render('/developer/change-hash', [
            'model' => $model
        ]);
    }
}
