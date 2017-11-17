<?php

namespace Iliich246\YicmsCommon\Controllers;

use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Languages\DefaultLanguageForm;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class DeveloperController
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DeveloperController extends Controller
{
    /**
     * @inheritdoc
     */
    public $layout = '@yicms-common/Views/layouts/developer';

    /**
     * Return list of languages and some language configs
     * @return string
     */
    public function actionLanguagesList()
    {
        $defaultLanguageForm = new DefaultLanguageForm();
//
        if ($defaultLanguageForm->load(Yii::$app->request->post()) && $defaultLanguageForm->validate()) {
//            if (!$defaultLanguageModel->set()) {
//                //TODO: bootbox error
//            }
        }

        $request = \Yii::$app->getRequest();

        if ($request->isPjax && $defaultLanguageForm->load($request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $defaultLanguageForm->validate();
            //$defaultLanguageForm->save();
            return $this->render('/developer/languages-list', [
                'defaultLanguageModel' => $defaultLanguageForm
            ]);
        }

//        if (Yii::$app->request->isAjax && $defaultLanguageForm->load(Yii::$app->request->post())) {
//            Yii::$app->response->format = Response::FORMAT_JSON;
//            return ActiveForm::validate($defaultLanguageForm);
//        }
//
        $languages = LanguagesDb::find()->all();

        return $this->render('/developer/languages-list', [
            'languages' => $languages,
            'defaultLanguageModel' => $defaultLanguageForm
        ]);
    }

    public function actionValidate()
    {
        $defaultLanguageForm = new DefaultLanguageForm();

        $request = \Yii::$app->getRequest();

        if ($request->isPjax && $defaultLanguageForm->load($request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($defaultLanguageForm);
        }

        return false;
    }

    public function actionSave()
    {
        $defaultLanguageForm = new DefaultLanguageForm();
        $request = \Yii::$app->getRequest();
        if ($request->isAjax && $defaultLanguageForm->load($request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => $defaultLanguageForm->save()];
        }

        $languages = LanguagesDb::find()->all();

        return $this->renderAjax('/developer/languages-list', [
            'defaultLanguageModel' => $defaultLanguageForm,
            'languages' => $languages,
        ]);
    }
}
