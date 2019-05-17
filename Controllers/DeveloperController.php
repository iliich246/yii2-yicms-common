<?php

namespace Iliich246\YicmsCommon\Controllers;

use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\Base\DevFilter;
use Iliich246\YicmsCommon\Base\CommonUser;
use Iliich246\YicmsCommon\Base\CommonHashForm;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use Iliich246\YicmsCommon\Languages\DefaultLanguageForm;
use Iliich246\YicmsCommon\Widgets\ReloadAlertWidget;
use Iliich246\YicmsCommon\FreeEssences\FreeEssences;
use Iliich246\YicmsCommon\FreeEssences\FreeEssenceNamesTranslatesForm;
use Iliich246\YicmsCommon\Fields\DevFieldsGroup;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\FieldsDevModalWidget;
use Iliich246\YicmsCommon\Files\FilesBlock;
use Iliich246\YicmsCommon\Files\DevFilesGroup;
use Iliich246\YicmsCommon\Files\FilesDevModalWidget;
use Iliich246\YicmsCommon\Images\ImagesBlock;
use Iliich246\YicmsCommon\Images\DevImagesGroup;
use Iliich246\YicmsCommon\Images\ImagesDevModalWidget;
use Iliich246\YicmsCommon\Conditions\ConditionTemplate;
use Iliich246\YicmsCommon\Conditions\DevConditionsGroup;
use Iliich246\YicmsCommon\Conditions\ConditionsDevModalWidget;

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
            'dev' => [
                'class' => DevFilter::class,
                'except' => ['login-as-dev'],
                'redirect' => function() {
                    return $this->redirect(Url::home());
                }
            ],
        ];
    }

    /**
     * Return list of languages and some language configs
     * @return string
     * @throws CommonException
     */
    public function actionLanguagesList()
    {
        $defaultLanguageForm = new DefaultLanguageForm();

        if ($defaultLanguageForm->load(Yii::$app->request->post()) && $defaultLanguageForm->validate()) {
            if ($defaultLanguageForm->save())
                return $this->render('/developer/languages_list', [
                    'defaultLanguageModel' => $defaultLanguageForm,
                    'success' => true,
                ]);
            else
                throw new CommonException('Can`t save data in database');
        }

        $languages = LanguagesDb::find()->all();

        return $this->render('/developer/languages_list', [
            'pjaxError' => false,
            'languages' => $languages,
            'defaultLanguageModel' => $defaultLanguageForm
        ]);
    }

    /**
     * Creates new system language
     * @return string|\yii\web\Response
     * @throws CommonException
     */
    public function actionCreateLanguage()
    {
        $model = new LanguagesDb();
        $model->scenario = LanguagesDb::SCENARIO_CREATE;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                ReloadAlertWidget::setSuccessModal('Language created',
                    'Language successfully created');

                return $this->redirect(Url::toRoute(['languages-list'])); }
            else
                throw new CommonException('Can`t save data in database');
        }

        return $this->render('/developer/create_update_language', [
            'model' => $model,
        ]);
    }

    /**
     * Updates system language
     * @param $id
     * @return string
     * @throws BadRequestHttpException
     * @throws CommonException
     */
    public function actionUpdateLanguage($id)
    {
        /** @var LanguagesDb $model */
        $model = LanguagesDb::findOne($id);

        if (!$model)
            throw new BadRequestHttpException('No language with same id in system');

        $model->scenario = LanguagesDb::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                return $this->render('/developer/create_update_language', [
                    'model' => $model,
                    'success' => true,
                ]);
            } else
                throw new CommonException('Can`t save data in database');
        }

        Url::remember(Url::toRoute(['update-language', 'id' => $id]), 'update-language');

        return $this->render('/developer/create_update_language', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws CommonException
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionDeleteLanguage($id)
    {
        if (!Yii::$app->request->isPjax)
            return $this->redirect(Url::previous('update-language'));

        /** @var LanguagesDb $language */
        $language = LanguagesDb::findOne($id);

        if (!$language)
            return $this->redirect(Url::previous('update-language'));

        if ($language->delete()) {
            ReloadAlertWidget::setSuccessModal('Language deleted',
                'Language successfully deleted');

            return $this->redirect(Url::toRoute('languages-list'));
        }

        throw new CommonException();
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
     * @return string|\yii\web\Response
     * @throws CommonException
     * @throws \yii\base\Exception
     */
    public function actionChangeDevHash()
    {
        $model = new CommonHashForm();
        $model->scenario = CommonHashForm::SCENARIO_CHANGE_DEV;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->changeDev()) {
                ReloadAlertWidget::setSuccessModal('Dev hash changed',
                    'Dev hash successfully changes');

                return $this->redirect(Url::toRoute('languages-list'));
            }
            throw new CommonException('Can`t change dev hash');
        }

        return $this->render('/developer/change_hash', [
            'model' => $model
        ]);
    }

    /**
     * Action for change admin hash
     * @return string|\yii\web\Response
     * @throws CommonException
     * @throws \yii\base\Exception
     */
    public function actionChangeAdminHash()
    {
        $model = new CommonHashForm();
        $model->scenario = CommonHashForm::SCENARIO_CHANGE_ADMIN;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->changeAdmin()) {
                ReloadAlertWidget::setSuccessModal('Admin hash changed',
                    'Admin hash successfully changes');

                return $this->redirect(Url::toRoute('languages-list'));
            }
            throw new CommonException('Can`t change admin hash');
        }

        return $this->render('/developer/change_hash', [
            'model' => $model
        ]);
    }

    /**
     * Render list of all free essences
     * @return string
     */
    public function actionFreeEssencesList()
    {
        $freeEssences = FreeEssences::find()->orderBy([
            'free_essences_order' => SORT_ASC
        ])->all();

        return $this->render('/developer/free_essences_list', [
            'freeEssences' => $freeEssences,
        ]);
    }

    /**
     * Creates new free essence
     * @return string|\yii\web\Response
     * @throws CommonException
     * @throws \ReflectionException
     */
    public function actionCreateFreeEssence()
    {
        $freeEssence = new FreeEssences();
        $freeEssence->scenario = FreeEssences::SCENARIO_CREATE;

        if ($freeEssence->load(Yii::$app->request->post()) && $freeEssence->validate()) {

            if ($freeEssence->save()) {
                $freeEssence->annotate();
                return $this->redirect(Url::toRoute(['update-free-essence', 'id' => $freeEssence->id]));
            } else {
                //TODO: add bootbox error
            }
        }

        return $this->render('/developer/create_update_free_essence', [
            'freeEssence' => $freeEssence,
        ]);
    }

    /**
     * Updates free essence
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionUpdateFreeEssence($id)
    {
        /** @var FreeEssences $freeEssence */
        $freeEssence = FreeEssences::findOne($id);

        if (!$freeEssence)
            throw new NotFoundHttpException('Wrong free essence ID');

        $freeEssence->scenario = FreeEssences::SCENARIO_UPDATE;

        //update page via pjax
        if ($freeEssence->load(Yii::$app->request->post()) && $freeEssence->validate()) {

            if ($freeEssence->save()) {
                $success = true;
            } else {
                $success = false;
            }

            return $this->render('/developer/create_update_free_essence', [
                'freeEssence' => $freeEssence,
                'success' => $success
            ]);
        }

        //initialize fields group
        $devFieldGroup = new DevFieldsGroup();
        $devFieldGroup->setFieldTemplateReference($freeEssence->getFieldTemplateReference());
        $devFieldGroup->initialize(Yii::$app->request->post('_fieldTemplateId'));

        //try to load validate and save field via pjax
        if ($devFieldGroup->load(Yii::$app->request->post()) && $devFieldGroup->validate()) {

            if (!$devFieldGroup->save()) {
                //TODO: bootbox error
            }

            $freeEssence->annotate();

            return FieldsDevModalWidget::widget([
                'devFieldGroup' => $devFieldGroup,
                'dataSaved' => true,
            ]);
        }

        $devFilesGroup = new DevFilesGroup();
        $devFilesGroup->setFilesTemplateReference($freeEssence->getFileTemplateReference());
        $devFilesGroup->initialize(Yii::$app->request->post('_fileTemplateId'));

        //try to load validate and save file block via pjax
        if ($devFilesGroup->load(Yii::$app->request->post()) && $devFilesGroup->validate()) {

            if (!$devFilesGroup->save()) {
                //TODO: bootbox error
            }

            $freeEssence->annotate();

            return FilesDevModalWidget::widget([
                'devFilesGroup' => $devFilesGroup,
                'dataSaved' => true,
            ]);
        }

        $devImagesGroup = new DevImagesGroup();
        $devImagesGroup->setImagesTemplateReference($freeEssence->getImageTemplateReference());
        $devImagesGroup->initialize(Yii::$app->request->post('_imageTemplateId'));

        //try to load validate and save image block via pjax
        if ($devImagesGroup->load(Yii::$app->request->post()) && $devImagesGroup->validate()) {

            if (!$devImagesGroup->save()) {
                //TODO: bootbox error
            }

            $freeEssence->annotate();

            return ImagesDevModalWidget::widget([
                'devImagesGroup' => $devImagesGroup,
                'dataSaved' => true,
            ]);
        }

        $devConditionsGroup = new DevConditionsGroup();
        $devConditionsGroup->setConditionsTemplateReference($freeEssence->getConditionTemplateReference());
        $devConditionsGroup->initialize(Yii::$app->request->post('_conditionTemplateId'));

        //try to load validate and save image block via pjax
        if ($devConditionsGroup->load(Yii::$app->request->post()) && $devConditionsGroup->validate()) {

            if (!$devConditionsGroup->save()) {
                //TODO: bootbox error
            }

            $freeEssence->annotate();

            return ConditionsDevModalWidget::widget([
                'devConditionsGroup' => $devConditionsGroup,
                'dataSaved' => true,
            ]);
        }

        $fieldTemplatesTranslatable = FieldTemplate::getListQuery($freeEssence->getFieldTemplateReference())
                                        ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE])
                                        ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                                        ->all();

        $fieldTemplatesSingle = FieldTemplate::getListQuery($freeEssence->getFieldTemplateReference())
                                        ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE])
                                        ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
                                        ->all();

        $filesBlocks = FilesBlock::getListQuery($freeEssence->getFileTemplateReference())
                                        ->orderBy([FilesBlock::getOrderFieldName() => SORT_ASC])
                                        ->all();

        $imagesBlocks = ImagesBlock::getListQuery($freeEssence->getImageTemplateReference())
                                        ->orderBy([ImagesBlock::getOrderFieldName() => SORT_ASC])
                                        ->all();

        $conditionTemplates = ConditionTemplate::getListQuery($freeEssence->getConditionTemplateReference())
                                        ->orderBy([ConditionTemplate::getOrderFieldName() => SORT_ASC])
                                        ->all();

        $freeEssence->annotate();

        Url::remember('', 'dev');

        return $this->render('/developer/create_update_free_essence', [
            'freeEssence' => $freeEssence,
            'devFieldGroup' => $devFieldGroup,
            'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
            'fieldTemplatesSingle' => $fieldTemplatesSingle,
            'devFilesGroup' => $devFilesGroup,
            'filesBlocks' => $filesBlocks,
            'devImagesGroup' => $devImagesGroup,
            'imagesBlocks' => $imagesBlocks,
            'devConditionsGroup' => $devConditionsGroup,
            'conditionTemplates' => $conditionTemplates

        ]);
    }

    /**
     * Action for delete free essence
     * @param $id
     * @param bool|false $deletePass
     * @return \yii\web\Response
     * @throws CommonException
     * @throws NotFoundHttpException
     */
    public function actionDeleteFreeEssence($id, $deletePass = false)
    {
        /** @var FreeEssences $freeEssence */
        $freeEssence = FreeEssences::findOne($id);

        if (!$freeEssence)
            throw new NotFoundHttpException('Wrong free essence ID');

        if ($freeEssence->isConstraints())
            if (!Yii::$app->security->validatePassword($deletePass, CommonHashForm::DEV_HASH))
                throw new CommonException('Wrong dev password');


        if ($freeEssence->delete())
            return $this->redirect(Url::toRoute(['free-essences-list']));

        throw new CommonException('Delete error');
    }

    /**
     * Displays page for work with admin translations of free essences
     * @param $id
     * @return string
     * @throws CommonException
     * @throws NotFoundHttpException
     */
    public function actionFreeEssenceTranslates($id)
    {
        /** @var FreeEssences $freeEssence */
        $freeEssence = FreeEssences::findOne($id);

        if (!$freeEssence)
            throw new NotFoundHttpException('Wrong free essence ID');

        $languages = Language::getInstance()->usedLanguages();

        $translateModels = [];

        foreach($languages as $key => $language) {
            $pageTranslate = new FreeEssenceNamesTranslatesForm();
            $pageTranslate->setLanguage($language);
            $pageTranslate->setFreeEssence($freeEssence);
            $pageTranslate->loadFromDb();

            $translateModels[$key] = $pageTranslate;
        }

        if (Model::loadMultiple($translateModels, Yii::$app->request->post()) &&
            Model::validateMultiple($translateModels)) {

            /** @var FreeEssenceNamesTranslatesForm $translateModel */
            foreach($translateModels as $key=>$translateModel) {
                $translateModel->save();
            }

            return $this->render('/developer/free_essence_translates', [
                'freeEssence'     => $freeEssence,
                'translateModels' => $translateModels,
                'success'         => true,
            ]);
        }

        return $this->render('/developer/free_essence_translates', [
            'freeEssence'     => $freeEssence,
            'translateModels' => $translateModels,
        ]);
    }

    /**
     * @param $freeEssenceId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionFreeEssenceUpOrder($freeEssenceId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var FreeEssences $freeEssence */
        $freeEssence = FreeEssences::findOne($freeEssenceId);

        if (!$freeEssence) throw new NotFoundHttpException('Wrong freeEssenceId = ' . $freeEssenceId);

        $freeEssence->configToChangeOfOrder();
        $freeEssence->upOrder();

        $freeEssences = FreeEssences::find()->orderBy([
            'free_essences_order' => SORT_ASC
        ])->all();

        return $this->render('/pjax/update-free-essence-list-container', [
            'freeEssences' => $freeEssences
        ]);
    }

    /**
     * @param $freeEssenceId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionFreeEssenceDownOrder($freeEssenceId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var FreeEssences $freeEssence */
        $freeEssence = FreeEssences::findOne($freeEssenceId);

        if (!$freeEssence) throw new NotFoundHttpException('Wrong freeEssenceId = ' . $freeEssenceId);

        $freeEssence->configToChangeOfOrder();
        $freeEssence->downOrder();

        $freeEssences = FreeEssences::find()->orderBy([
            'free_essences_order' => SORT_ASC
        ])->all();

        return $this->render('/pjax/update-free-essence-list-container', [
            'freeEssences' => $freeEssences
        ]);
    }
}
