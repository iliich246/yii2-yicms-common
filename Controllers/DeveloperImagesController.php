<?php

namespace Iliich246\YicmsCommon\Controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\DevFilter;
use Iliich246\YicmsCommon\Base\CommonHashForm;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\DevFieldsGroup;
use Iliich246\YicmsCommon\Fields\FieldsDevModalWidget;
use Iliich246\YicmsCommon\Images\ImagesBlock;
use Iliich246\YicmsCommon\Images\DevImagesGroup;
use Iliich246\YicmsCommon\Images\ImagesThumbnails;
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
                'imagesBlocks'           => $imagesBlocks,
            ]);

        }

        throw new BadRequestHttpException();
    }

    /**
     * Action for delete image block template
     * @param $imageTemplateId
     * @param bool|false $deletePass
     * @return string
     * @throws BadRequestHttpException
     * @throws CommonException
     * @throws NotFoundHttpException
     */
    public function actionDeleteImageBlockTemplate($imageTemplateId, $deletePass = false)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var ImagesBlock $imagesBlock */
        $imagesBlock = ImagesBlock::findOne($imageTemplateId);

        if (!$imagesBlock) throw new NotFoundHttpException('Wrong imageTemplateId');

        if ($imagesBlock->isConstraints())
            if (!Yii::$app->security->validatePassword($deletePass, CommonHashForm::DEV_HASH))
                throw new CommonException('Wrong dev password');

        $imageTemplateReference = $imagesBlock->image_template_reference;

        $imagesBlock->delete();

        $imagesBlocks = ImagesBlock::getListQuery($imageTemplateReference)
            ->orderBy([ImagesBlock::getOrderFieldName() => SORT_ASC])
            ->all();

        return  $this->render('/pjax/update-images-list-container', [
            'imageTemplateReference' => $imageTemplateReference,
            'imagesBlocks'           => $imagesBlocks,
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
            'imagesBlocks'           => $imagesBlocks,
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
            'imagesBlocks'           => $imagesBlocks,
        ]);
    }

    /**
     * Render list of thumbnails configurators for selected imageTemplateId
     * @param $imageTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionShowThumbnailsList($imageTemplateId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('Not Pjax');
        /** @var ImagesBlock $imagesBlock */
        $imagesBlock = ImagesBlock::findOne($imageTemplateId);

        if (!$imagesBlock) throw new NotFoundHttpException('Wrong imageTemplateId');

        $thumbnails = ImagesThumbnails::find()->where([
            'common_images_templates_id' => $imageTemplateId
        ])->all();

        return $this->renderAjax('@yicms-common/Images/views/images_thumbnails_list', [
            'thumbnails'   => $thumbnails,
            'imagesBlock'  => $imagesBlock,
        ]);
    }

    /**
     * Add new thumbnail configurator
     * @param $imageTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionAddNewThumbnailConfigurator($imageTemplateId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('Not Pjax');

        /** @var ImagesBlock $imagesBlock */
        $imagesBlock = ImagesBlock::findOne($imageTemplateId);

        if (!$imagesBlock) throw new NotFoundHttpException('Wrong imageTemplateId = ' . $imageTemplateId);

        $thumbnail = new ImagesThumbnails();
        $thumbnail->setImagesBlock($imagesBlock);
        $thumbnail->scenario = ImagesThumbnails::SCENARIO_CREATE;

        if ($thumbnail->load(Yii::$app->request->post()) && $thumbnail->validate()) {

            if (!$thumbnail->save()) {
                //TODO: return pjax error
            }

            if (Yii::$app->request->post('_saveAndBack')) {

                return $this->renderAjax('@yicms-common/Images/views/create_update_thumbnail', [
                    'thumbnail'   => $thumbnail,
                    'imagesBlock' => $imagesBlock,
                    'returnBack'  => true,
                ]);
            }
        }

        return $this->renderAjax('@yicms-common/Images/views/create_update_thumbnail', [
            'thumbnail'   => $thumbnail,
            'imagesBlock' => $imagesBlock,
        ]);
    }

    /**
     * Updates existed thumbnail configurator
     * @param $thumbnailId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdateThumbnailConfigurator($thumbnailId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('Not Pjax');

        /** @var ImagesThumbnails $thumbnail */
        $thumbnail = ImagesThumbnails::findOne($thumbnailId);

        if (!$thumbnail) throw new NotFoundHttpException('Wrong $thumbnailId = ' . $thumbnailId);

        /** @var ImagesBlock $imagesBlock */
        $imagesBlock = ImagesBlock::findOne($thumbnail->common_images_templates_id);

        if (!$imagesBlock) throw new NotFoundHttpException('Wrong $imagesBlock');

        $thumbnail->scenario = ImagesThumbnails::SCENARIO_UPDATE;

        if ($thumbnail->load(Yii::$app->request->post()) && $thumbnail->validate()) {

            if (!$thumbnail->save()) {
                //TODO: return pjax error
            }

            if (Yii::$app->request->post('_saveAndBack')) {

                return $this->renderAjax('@yicms-common/Images/views/create_update_thumbnail', [
                    'thumbnail'   => $thumbnail,
                    'imagesBlock' => $imagesBlock,
                    'returnBack'  => true,
                ]);
            }
        }

        return $this->renderAjax('@yicms-common/Images/views/create_update_thumbnail', [
            'thumbnail'   => $thumbnail,
            'imagesBlock' => $imagesBlock,
        ]);
    }

    /**
     * Delete thumbnail configurator
     * @param $thumbnailId
     * @return false|int|void
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDeleteThumbnailConfigurator($thumbnailId)
    {
        if (!Yii::$app->request->isAjax) throw new BadRequestHttpException('Not Ajax');
        /** @var ImagesThumbnails $thumbnail */
        $thumbnail = ImagesThumbnails::findOne($thumbnailId);

        if (!$thumbnail) throw new NotFoundHttpException('Wrong $thumbnailId = ' . $thumbnailId);

        return $thumbnail->delete();
    }
}
