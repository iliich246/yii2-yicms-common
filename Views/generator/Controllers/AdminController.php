<?php

namespace app\yicms\Common\Controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Fields\FieldsGroup;
use Iliich246\YicmsCommon\FreeEssences\FreeEssences;
use Iliich246\YicmsCommon\Files\FilesBlock;
use Iliich246\YicmsCommon\Images\ImagesBlock;
use Iliich246\YicmsCommon\Conditions\ConditionsGroup;

/**
 * Class AdminController
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class AdminController extends Controller
{
    /** @inheritdoc */
    public $defaultAction = 'edit-free-essence';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->layout = CommonModule::getInstance()->yicmsLocation . '/Common/Views/layouts/admin';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            'root' => [
//                'class' => AdminFilter::className(),
//                'except' => ['login-as-root'],
//            ],
        ];
    }

    /**
     * Action for edit free essence
     * @param null $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionEditFreeEssence($id = null)
    {
        if (!is_null($id)) {
            /** @var FreeEssences $freeEssence */
            $freeEssence = FreeEssences::findOne($id);

            //TODO: make correct translates of error messages
            if (!$freeEssence) throw new NotFoundHttpException('Wrong free essence ID');

            if (!$freeEssence->editable && !CommonModule::isUnderDev())
                throw new NotFoundHttpException('Wrong free essence ID');
        } else {
            $freeEssenceQuery = FreeEssences::find();

            if (!CommonModule::isUnderDev())
                $freeEssenceQuery->where([
                    'editable' => true,
                ]);

            $freeEssence = $freeEssenceQuery->one();

            if (!$freeEssenceQuery)
                throw new NotFoundHttpException('No free essences existed');
        }

        $fieldsGroup = new FieldsGroup();
        $fieldsGroup->setFieldsReferenceAble($freeEssence);
        $fieldsGroup->initialize();

        //try to load validate and save field via pjax
        if ($fieldsGroup->load(Yii::$app->request->post()) && $fieldsGroup->validate()) {

            if (!$fieldsGroup->save()) {
                //TODO: bootbox error
            }

            return $this->render(CommonModule::getInstance()->yicmsLocation  . '/Common/Views/pjax/fields', [
                'fieldsGroup'            => $fieldsGroup,
                'fieldTemplateReference' => $freeEssence->getFieldTemplateReference(),
                'success'                => true,
            ]);
        }

        $conditionsGroup = new ConditionsGroup();
        $conditionsGroup->setConditionsReferenceAble($freeEssence);
        $conditionsGroup->initialize();

        if ($conditionsGroup->load(Yii::$app->request->post()) && $conditionsGroup->validate()) {
            $conditionsGroup->save();

            return $this->render(CommonModule::getInstance()->yicmsLocation  . '/Common/Views/conditions/conditions', [
                'conditionsGroup'            => $conditionsGroup,
                'conditionTemplateReference' => $freeEssence->getConditionTemplateReference(),
                'success'                    => true,
            ]);
        }

        /** @var FilesBlock $filesBlocks */
        $filesBlocksQuery = FilesBlock::find()->where([
            'file_template_reference' => $freeEssence->getFileTemplateReference(),
        ])->orderBy([
            FilesBlock::getOrderFieldName() => SORT_ASC
        ]);

        if (CommonModule::isUnderAdmin())
            $filesBlocksQuery->andWhere([
                'editable' => true,
            ]);

        $filesBlocks = $filesBlocksQuery->all();

        foreach ($filesBlocks as $fileBlock)
            $fileBlock->setFileReference($freeEssence->getFileReference());

        /** @var ImagesBlock $imagesBlock */
        $imagesBlockQuery = ImagesBlock::find()->where([
            'image_template_reference' => $freeEssence->getImageTemplateReference()
        ])->orderBy([
            ImagesBlock::getOrderFieldName() => SORT_ASC
        ]);

        if (CommonModule::isUnderAdmin())
            $imagesBlockQuery->andWhere([
                'editable' => true,
            ]);

        $imagesBlocks = $imagesBlockQuery->all();

        foreach ($imagesBlocks as $imagesBlock)
            $imagesBlock->setImageReference($freeEssence->getImageReference());

        return $this->render(CommonModule::getInstance()->yicmsLocation  . '/Common/Views/admin/edit-free-essence', [
            'freeEssence'            => $freeEssence,
            'fieldsGroup'            => $fieldsGroup,
            'filesBlocks'            => $filesBlocks,
            'imagesBlocks'           => $imagesBlocks,
            'conditionsGroup'        => $conditionsGroup
        ]);
    }
}
