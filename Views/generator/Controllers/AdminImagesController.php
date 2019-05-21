<?php

namespace app\yicms\Common\Controllers;

use Iliich246\YicmsCommon\Conditions\ConditionsGroup;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AdminFilter;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use Iliich246\YicmsCommon\Fields\FieldsGroup;
use Iliich246\YicmsCommon\Images\Image;
use Iliich246\YicmsCommon\Images\ImagesBlock;
use Iliich246\YicmsCommon\Images\ImagesGroup;

/**
 * Class AdminImagesController
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class AdminImagesController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            'root' => [
//                'class' => AdminFilter::className(),
//                'except' => ['upload-file'],
//            ],
        ];
    }

    /**
     * Action for load list of images via pjax
     * @param $imagesBlockId
     * @param $imageReference
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionImagesList($imagesBlockId, $imageReference)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('No Pjax');

        /** @var ImagesBlock $imagesBlock */
        $imagesBlock = ImagesBlock::findOne($imagesBlockId);

        if (!$imagesBlock) throw new NotFoundHttpException('Wrong imagesBlockId = ' . $imagesBlockId);

        $imagesBlock->offAnnotation();

        if (CommonModule::isUnderAdmin() && !$imagesBlock->editable)
            throw new NotFoundHttpException('Wrong imagesBlockId = ' . $imagesBlockId);

        $imagesBlock->setImageReference($imageReference);

        $imagesQuery = $imagesBlock->getEntityQuery();

        if (!CommonModule::isUnderDev())
            $imagesQuery->andWhere([
                'editable' => true,
            ]);

        /** @var Image[] $imagesList */
        $imagesList = $imagesQuery->all();

        foreach ($imagesList as $image)
            $image->offAnnotation();

        return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/images/images-list.php', [
            'imagesBlock'     => $imagesBlock,
            'imagesList'      => $imagesList,
            'imageReference'  => $imageReference,
        ]);

    }

    /**
     * Load new image in image block
     * @param $imagesBlockId
     * @param $imageReference
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionLoadNewImage($imagesBlockId, $imageReference)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('No Pjax');

        /** @var ImagesBlock $imagesBlock */
        $imagesBlock = ImagesBlock::findOne($imagesBlockId);

        $imagesBlock->offAnnotation();

        if (!$imagesBlock) throw new NotFoundHttpException('Wrong imageBlockId = ' . $imagesBlockId);

        if (CommonModule::isUnderAdmin() && !$imagesBlock->editable)
            throw new NotFoundHttpException('Wrong imageBlockId = ' . $imagesBlockId);

        $imagesGroup = new ImagesGroup();
        $imagesGroup->scenario = ImagesGroup::SCENARIO_CREATE;
        $imagesGroup->setImageTemplateReference($imagesBlock->image_template_reference);
        $imagesGroup->setImagesBlock($imagesBlock);
        $imagesGroup->setImageReference($imageReference);
        $imagesGroup->initialize();

        if ($imagesGroup->load(Yii::$app->request->post()) && $imagesGroup->validate()) {
            $imagesGroup->save();

            if (Yii::$app->request->post('_saveAndBack')) {
                return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/images/image-load.php', [
                    'imagesBlock'  => $imagesBlock,
                    'imagesGroup'  => $imagesGroup,
                    'returnBack'   => true,
                ]);
            }

            return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/images/image-load.php', [
                'imagesBlock'         => $imagesBlock,
                'imagesGroup'         => $imagesGroup,
                'updateRedirect'      => true,
                'imageIdForRedirect'   => $imagesGroup->getImageExistedInDbEntity()->id,
            ]);

        }

        return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/images/image-load.php', [
            'imagesBlock' => $imagesBlock,
            'imagesGroup' => $imagesGroup,
        ]);
    }

    /**
     * Action for update images
     * @param $imageId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function actionUpdateImage($imageId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('No Pjax');

        /** @var Image $image */
        $image = Image::findOne($imageId);

        if (!$image) throw new NotFoundHttpException('Wrong image id = ' . $imageId);

        $image->offAnnotation();

        if (CommonModule::isUnderAdmin() && !$image->editable)
            throw new NotFoundHttpException('Wrong image id = ' . $imageId);

        /** @var ImagesBlock $imagesBlock */
        $imagesBlock = ImagesBlock::findOne($image->common_images_templates_id);

        if (!$imagesBlock) throw new NotFoundHttpException('Wrong imagesBlock in file');

        if (CommonModule::isUnderAdmin() && !$imagesBlock->editable)
            throw new NotFoundHttpException('Wrong imagesBlockId = ' . $imagesBlock->id);

        $image->setEntityBlock($imagesBlock);

        $imagesGroup = new ImagesGroup();
        $imagesGroup->scenario = ImagesGroup::SCENARIO_UPDATE;
        $imagesGroup->setImageTemplateReference($imagesBlock->image_template_reference);
        $imagesGroup->setImagesBlock($imagesBlock);
        $imagesGroup->setImageEntity($image);
        $imagesGroup->setImageReference($image->image_reference);
        $imagesGroup->initialize();

        $fieldsGroup = new FieldsGroup();
        $fieldsGroup->setFieldsReferenceAble($image);
        $fieldsGroup->initialize();

        $conditionsGroup = new ConditionsGroup();
        $conditionsGroup->setConditionsReferenceAble($image);
        $conditionsGroup->initialize();

        $isImagesSave = false;

        if ($imagesGroup->load(Yii::$app->request->post()) && $imagesGroup->validate()) {

            $isImagesSave = $imagesGroup->save();
        }

        $isFieldsSave = true;

        if ($fieldsGroup->load(Yii::$app->request->post()) && $fieldsGroup->validate()) {

            $isFieldsSave = $fieldsGroup->save();
        }

        $isConditionsSave = true;

        if ($conditionsGroup->load(Yii::$app->request->post()) && $conditionsGroup->validate()) {

            $isConditionsSave = $conditionsGroup->save();
        }

        if ($isImagesSave || $isFieldsSave || $isConditionsSave) {
            if (Yii::$app->request->post('_saveAndBack')) {
                return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/images/image-load.php', [
                    'imagesBlock'     => $imagesBlock,
                    'imagesGroup'     => $imagesGroup,
                    'fieldsGroup'     => $fieldsGroup,
                    'conditionsGroup' => $conditionsGroup,
                    'imageId'         => $imageId,
                    'returnBack'      => true,
                ]);
            }
        }

        return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/images/image-load.php', [
            'imagesBlock'     => $imagesBlock,
            'imagesGroup'     => $imagesGroup,
            'fieldsGroup'     => $fieldsGroup,
            'conditionsGroup' => $conditionsGroup,
            'imageId'         => $imageId,
        ]);
    }

    /**
     * Up image order
     * @param $imageId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpImageOrder($imageId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('No Pjax');

        /** @var Image $image */
        $image = Image::findOne($imageId);

        if (!$image) throw new NotFoundHttpException('Wrong image id = ' . $imageId);

        $image->offAnnotation();

        /** @var ImagesBlock $imagesBlock */
        $imagesBlock = ImagesBlock::findOne($image->common_images_templates_id);

        if (!$imagesBlock) throw new NotFoundHttpException('Wrong imagesBlock in image');

        $imagesBlock->setImageReference($image);

        $image->upOrder();

        $imagesQuery = $imagesBlock->getEntityQuery();

        if (!CommonModule::isUnderDev())
            $imagesQuery->andWhere([
                'editable' => true,
            ]);

        $imagesList = $imagesQuery->all();

        foreach ($imagesList as $image)
            $image->offAnnotation();

        return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/images/images-list.php', [
            'imagesBlock'     => $imagesBlock,
            'imagesList'      => $imagesList,
            'imageReference'  => $image->image_reference,
        ]);
    }

    /**
     * Down image order
     * @param $imageId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDownImageOrder($imageId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('No Pjax');

        /** @var Image $image */
        $image = Image::findOne($imageId);

        if (!$image) throw new NotFoundHttpException('Wrong image id = ' . $imageId);

        $image->offAnnotation();

        /** @var ImagesBlock $imagesBlock */
        $imagesBlock = ImagesBlock::findOne($image->common_images_templates_id);

        if (!$imagesBlock) throw new NotFoundHttpException('Wrong imagesBlock in image');

        $imagesBlock->setImageReference($image);

        $image->downOrder();

        $imagesQuery = $imagesBlock->getEntityQuery();

        if (!CommonModule::isUnderDev())
            $imagesQuery->andWhere([
                'editable' => true,
            ]);

        $imagesList = $imagesQuery->all();

        foreach ($imagesList as $image)
            $image->offAnnotation();

        return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/images/images-list.php', [
            'imagesBlock'     => $imagesBlock,
            'imagesList'      => $imagesList,
            'imageReference'  => $image->image_reference,
        ]);
    }

    /**
     * Action for delete image
     * @param $imageId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDeleteImage($imageId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('No Pjax');

        /** @var Image $image */
        $image = Image::findOne($imageId);

        if (!$image) throw new NotFoundHttpException('Wrong image id = ' . $imageId);

        /** @var ImagesBlock $imageBlock */
        $imageBlock = ImagesBlock::findOne($image->common_images_templates_id);

        if (!$imageBlock) throw new NotFoundHttpException('Wrong imageBlock in image');

        $image->delete();

        $imagesQuery = $imageBlock->getEntityQuery();

        if (!CommonModule::isUnderDev())
            $imagesQuery->andWhere([
                'editable' => true,
            ]);

        $imagesList = $imagesQuery->all();

        foreach ($imagesList as $image)
            $image->offAnnotation();

        return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/images/images-list.php', [
            'imagesBlock'     => $imageBlock,
            'imagesList'      => $imagesList,
            'imageReference'  => $image->image_reference,
        ]);
    }
}
