<?php

namespace Iliich246\YicmsCommon\Controllers;

use Iliich246\YicmsCommon\Files\FilesBlock;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\DevFilter;
use Iliich246\YicmsCommon\Files\FilesDevModalWidget;
use Iliich246\YicmsCommon\Files\DevFilesGroup;


/**
 * Class DeveloperFilesController
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DeveloperFilesController extends Controller
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

    /**
     * Action for refresh dev file modal window
     * @param $fileTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws \Exception
     */
    public function actionLoadModal($fileTemplateId)
    {
        if (Yii::$app->request->isPjax &&
            Yii::$app->request->post('_pjax') == '#'.FilesDevModalWidget::getPjaxContainerId())
        {
            $devFilesGroup = new DevFilesGroup();
            $devFilesGroup->initialize($fileTemplateId);

            return FilesDevModalWidget::widget([
                'devFilesGroup' => $devFilesGroup
            ]);
        }

        throw new BadRequestHttpException();
    }

    /**
     * Action for send empty file modal window
     * @param $fileTemplateReference
     * @return string
     * @throws BadRequestHttpException
     * @throws \Exception
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function actionEmptyModal($fileTemplateReference)
    {
        if (Yii::$app->request->isPjax &&
            Yii::$app->request->post('_pjax') == '#'.FilesDevModalWidget::getPjaxContainerId())
        {
            $devFilesGroup = new DevFilesGroup();
            $devFilesGroup->setFilesTemplateReference($fileTemplateReference);
            $devFilesGroup->initialize();

            return FilesDevModalWidget::widget([
                'devFilesGroup' => $devFilesGroup
            ]);
        }

        throw new BadRequestHttpException();
    }

    /**
     * Action for update files blocks list container
     * @param $fileTemplateReference
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionUpdateFilesListContainer($fileTemplateReference)
    {
        if (Yii::$app->request->isPjax &&
            Yii::$app->request->post('_pjax') == '#update-files-list-container') {

            $filesBlocks = FilesBlock::getListQuery($fileTemplateReference)
                ->orderBy([FilesBlock::getOrderFieldName() => SORT_ASC])
                ->all();

            return  $this->render('/pjax/update-files-list-container', [
                'fileTemplateReference' => $fileTemplateReference,
                'filesBlocks' => $filesBlocks,
            ]);
        }

        throw new BadRequestHttpException();
    }

    /**
     * Action for delete file block template
     * @param $fileTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDeleteFileBlockTemplate($fileTemplateId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var FilesBlock $filesBlock */
        $filesBlock = FilesBlock::findOne($fileTemplateId);

        if (!$filesBlock) throw new NotFoundHttpException('Wrong fileTemplateId');

        //TODO: for field templates with constraints makes request of root password

        $fileTemplateReference = $filesBlock->file_template_reference;

        $filesBlock->delete();

        $filesBlocks = FilesBlock::getListQuery($fileTemplateReference)
            ->orderBy([FilesBlock::getOrderFieldName() => SORT_ASC])
            ->all();

        return  $this->render('/pjax/update-files-list-container', [
            'fileTemplateReference' => $fileTemplateReference,
            'filesBlocks' => $filesBlocks,
        ]);
    }

    /**
     * Action for up file block template order
     * @param $fileTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionFileTemplateUpOrder($fileTemplateId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var FilesBlock $filesBlock */
        $filesBlock = FilesBlock::findOne($fileTemplateId);

        if (!$filesBlock) throw new NotFoundHttpException('Wrong fileTemplateId');

        $filesBlock->upOrder();

        $fileTemplateReference = $filesBlock->file_template_reference;

        $filesBlocks = FilesBlock::getListQuery($fileTemplateReference)
            ->orderBy([FilesBlock::getOrderFieldName() => SORT_ASC])
            ->all();

        return $this->render('/pjax/update-files-list-container', [
            'fileTemplateReference' => $fileTemplateReference,
            'filesBlocks' => $filesBlocks,
        ]);
    }

    /**
     * Action for down file block template order
     * @param $fileTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionFileTemplateDownOrder($fileTemplateId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException();

        /** @var FilesBlock $filesBlock */
        $filesBlock = FilesBlock::findOne($fileTemplateId);

        if (!$filesBlock) throw new NotFoundHttpException('Wrong fileTemplateId');

        $filesBlock->downOrder();

        $fileTemplateReference = $filesBlock->file_template_reference;

        $filesBlocks = FilesBlock::getListQuery($fileTemplateReference)
            ->orderBy([FilesBlock::getOrderFieldName() => SORT_ASC])
            ->all();

        return  $this->render('/pjax/update-files-list-container', [
            'fileTemplateReference' => $fileTemplateReference,
            'filesBlocks' => $filesBlocks,
        ]);
    }
}
