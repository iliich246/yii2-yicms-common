<?php

namespace Iliich246\YicmsCommon\Controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\Base\DevFilter;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Validators\ValidatorDb;
use Iliich246\YicmsCommon\Validators\AbstractValidatorForm;

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
            'dev' => [
                'class' => DevFilter::class,
                'redirect' => function() {
                    return $this->redirect(Url::home());
                }
            ],
        ];
    }

    /**
     * Action for load and update concrete validator form info
     * @param $validatorId
     * @return string
     * @throws BadRequestHttpException
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function actionUpdateValidator($validatorId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('no pjax');

        $validatorForm = AbstractValidatorForm::getConcreteInstance($validatorId);

        if ($validatorForm->load(Yii::$app->request->post()) && $validatorForm->validate()) {
            if (!$validatorForm->save()) {
                //TODO: return pjax error
            }

            if (Yii::$app->request->post('_saveAndBack')) {
                return $this->renderAjax($validatorForm->getRenderView(), [
                    'validatorForm' => $validatorForm,
                    'returnBack' => true,
                ]);
            }
        }

        return $this->renderAjax($validatorForm->getRenderView(), [
            'validatorForm' => $validatorForm,
        ]);
    }

    /**
     * Add new validator to concrete reference
     * @param $validatorReference
     * @param $validator
     * @return string
     * @throws BadRequestHttpException
     * @throws CommonException
     */
    public function actionAddValidator($validatorReference, $validator)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('no pjax');

        $validatorClass = AbstractValidatorForm::$builtInValidators[$validator];

        if (!$validatorClass)
            return Html::hiddenInput('validator', true, ['class' => 'validator-response']);

        /** @var ValidatorDb $validatorDb */
        $validatorDb = new ValidatorDb();
        $validatorDb->is_active = false;
        $validatorDb->validator = $validatorClass;
        $validatorDb->validator_reference = $validatorReference;
        $validatorDb->params = null;

        if (!$validatorDb->save()) throw new CommonException('Can`t save validator');

        return Html::hiddenInput('validator', true, ['class' => 'validator-response']);
    }

    /**
     * Action for delete validator
     * @param $id
     * @return false|int
     * @throws BadRequestHttpException
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionDeleteValidator($id)
    {
        if (!Yii::$app->request->isAjax) throw new BadRequestHttpException('No Pjax');

        /** @var ValidatorDb $validator */
        $validator = ValidatorDb::findOne($id);

        if (!$validator) throw new BadRequestHttpException();

        return $validator->delete();
    }
}
