<?php

namespace Iliich246\YicmsCommon\Controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\DevFilter;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\DevFieldsGroup;
use Iliich246\YicmsCommon\Fields\FieldsDevModalWidget;
use Iliich246\YicmsCommon\Images\ImagesBlock;
use Iliich246\YicmsCommon\Images\DevImagesGroup;
use Iliich246\YicmsCommon\Images\ImagesDevModalWidget;

/**
 * Class DeveloperImagesController
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DeveloperImagesController extends Controller
{
    /** @inheritdoc */
    public $layout = '@yicms-common/Views/layouts/developer';

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

    /**
     * Action for refresh dev image modal window
     * @param $imageTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws \Exception
     */
    public function actionLoadModal($imageTemplateId)
    {
        if (Yii::$app->request->isPjax &&
            Yii::$app->request->post('_pjax') == '#'.ImagesDevModalWidget::getPjaxContainerId())
        {
            $devImagesGroup = new DevImagesGroup();
            $devImagesGroup->initialize($imageTemplateId);

//            $fieldTemplatesTranslatable = FieldTemplate::getListQuery($devImagesGroup->imagesBlock->image_template_reference)
//                ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE])
//                ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
//                ->all();
//
//            $fieldTemplatesSingle = FieldTemplate::getListQuery($devImagesGroup->imagesBlock->image_template_reference)
//                ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE])
//                ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
//                ->all();

            return ImagesDevModalWidget::widget([
                'devImagesGroup' => $devImagesGroup,
//                'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
//                'fieldTemplatesSingle' => $fieldTemplatesSingle
            ]);
        }

        throw new BadRequestHttpException();
    }

    /**
     * Action for send empty image modal window
     * @param $imageTemplateReference
     * @return string
     * @throws BadRequestHttpException
     * @throws \Exception
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function actionEmptyModal($imageTemplateReference)
    {
        if (Yii::$app->request->isPjax &&
            Yii::$app->request->post('_pjax') == '#'.ImagesDevModalWidget::getPjaxContainerId())
        {
            $devImagesGroup = new DevImagesGroup();
            $devImagesGroup->setImagesTemplateReference($imageTemplateReference);
            $devImagesGroup->initialize();

            return ImagesDevModalWidget::widget([
                'devImagesGroup' => $devImagesGroup
            ]);
        }

        throw new BadRequestHttpException();
    }

    /**
     * Action for update images blocks list container
     * @param $imageTemplateReference
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionUpdateImagesListContainer($imageTemplateReference)
    {
        if (Yii::$app->request->isPjax &&
            Yii::$app->request->post('_pjax') == '#update-images-list-container') {

            $imagesBlocks = ImagesBlock::getListQuery($imageTemplateReference)
                ->orderBy([ImagesBlock::getOrderFieldName() => SORT_ASC])
                ->all();

            return  $this->render('/pjax/update-images-list-container', [
                'imageTemplateReference' => $imageTemplateReference,
                'imagesBlocks' => $imagesBlocks,
            ]);

        }

        throw new BadRequestHttpException();
    }

    /**
     * Action for delete image block template
     * @param $imageTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDeleteImageBlockTemplate($imageTemplateId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var ImagesBlock $imagesBlock */
        $imagesBlock = ImagesBlock::findOne($imageTemplateId);

        if (!$imagesBlock) throw new NotFoundHttpException('Wrong imageTemplateId');

        //TODO: for field templates with constraints makes request of root password

        $imageTemplateReference = $imagesBlock->image_template_reference;

        $imagesBlock->delete();

        $imagesBlocks = ImagesBlock::getListQuery($imageTemplateReference)
            ->orderBy([ImagesBlock::getOrderFieldName() => SORT_ASC])
            ->all();

        return  $this->render('/pjax/update-images-list-container', [
            'imageTemplateReference' => $imageTemplateReference,
            'imagesBlocks' => $imagesBlocks,
        ]);
    }

    /**
     * Action for up image block template order
     * @param $imageTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionImageTemplateUpOrder($imageTemplateId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var ImagesBlock $imagesBlock */
        $imagesBlock = ImagesBlock::findOne($imageTemplateId);

        if (!$imagesBlock) throw new NotFoundHttpException('Wrong imageTemplateId');

        $imageTemplateReference = $imagesBlock->image_template_reference;

        $imagesBlock->upOrder();

        $imagesBlocks = ImagesBlock::getListQuery($imageTemplateReference)
            ->orderBy([ImagesBlock::getOrderFieldName() => SORT_ASC])
            ->all();

        return  $this->render('/pjax/update-images-list-container', [
            'imageTemplateReference' => $imageTemplateReference,
            'imagesBlocks' => $imagesBlocks,
        ]);
    }

    /**
     * Action for down file block template order
     * @param $imageTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionImageTemplateDownOrder($imageTemplateId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var ImagesBlock $imagesBlock */
        $imagesBlock = ImagesBlock::findOne($imageTemplateId);

        if (!$imagesBlock) throw new NotFoundHttpException('Wrong imageTemplateId');

        $imageTemplateReference = $imagesBlock->image_template_reference;

        $imagesBlock->downOrder();

        $imagesBlocks = ImagesBlock::getListQuery($imageTemplateReference)
            ->orderBy([ImagesBlock::getOrderFieldName() => SORT_ASC])
            ->all();

        return  $this->render('/pjax/update-images-list-container', [
            'imageTemplateReference' => $imageTemplateReference,
            'imagesBlocks' => $imagesBlocks,
        ]);
    }

    /**
     * Show fields for image block template
     * @param $imageTemplateId
     * @return string
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function actionShowImageBlockFields($imageTemplateId)
    {
        /** @var ImagesBlock $imagesBlock */
        $imagesBlock = ImagesBlock::findOne($imageTemplateId);

        if (!$imagesBlock) throw new NotFoundHttpException('Wrong imageTemplateId');

        $devFieldGroup = new DevFieldsGroup();
        $devFieldGroup->setFieldTemplateReference($imagesBlock->getFieldTemplateReference());
        $devFieldGroup->initialize(Yii::$app->request->post('_fieldTemplateId'));

        //try to load validate and save field via pjax
        if ($devFieldGroup->load(Yii::$app->request->post()) && $devFieldGroup->validate()) {

            if (!$devFieldGroup->save()) {
                //TODO: bootbox error
            }

            return FieldsDevModalWidget::widget([
                'devFieldGroup' => $devFieldGroup,
                'dataSaved' => true,
            ]);
        }

        $fieldTemplatesTranslatable = FieldTemplate::getListQuery($imagesBlock->field_template_reference)
            ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_TRANSLATABLE])
            ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
            ->all();

        $fieldTemplatesSingle = FieldTemplate::getListQuery($imagesBlock->field_template_reference)
            ->andWhere(['language_type' => FieldTemplate::LANGUAGE_TYPE_SINGLE])
            ->orderBy([FieldTemplate::getOrderFieldName() => SORT_ASC])
            ->all();

        return $this->render('/developer/show-image-block-fields', [
            'imagesBlock' => $imagesBlock,
            'devFieldGroup' => $devFieldGroup,
            'fieldTemplatesTranslatable' => $fieldTemplatesTranslatable,
            'fieldTemplatesSingle' => $fieldTemplatesSingle,
        ]);
    }
}