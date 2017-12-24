<?php

namespace Iliich246\YicmsCommon\Controllers;

use Iliich246\YicmsCommon\Validators\AbstractValidatorForm;
use Iliich246\YicmsCommon\Validators\RequireValidatorForm;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\BadRequestHttpException;

/**
 * Class DeveloperValidatorController
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DeveloperValidatorsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            'root' => [
//                'class' => DevFilter::className(),
//                'except' => ['change-field-editable'],
//            ],
        ];
    }

    public function actionUpdateValidator($validatorId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('no pjax');

        $validatorForm = AbstractValidatorForm::getConcreteInstance($validatorId);

        //throw new \Exception(print_r($validatorId,true));
        if ($validatorForm->load(Yii::$app->request->post()) && $validatorForm->validate()) {
            $validatorForm->save();
        }


        return $this->renderAjax('@yicms-common/Validators/views/require_form.php', [
            'validatorForm' => $validatorForm
        ]);

    }
}
