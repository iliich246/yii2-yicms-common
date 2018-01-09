<?php

namespace Iliich246\YicmsCommon\Controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\DevFilter;

/**
 * Class DeveloperImagesController
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DeveloperImagesController
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

    }

    /**
     * Action for update images blocks list container
     * @param $fileTemplateReference
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionUpdateFilesListContainer($fileTemplateReference)
    {

    }

    /**
     * Action for delete file block template
     * @param $imageTemplateId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDeleteImageBlockTemplate($imageTemplateId)
    {

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

    }

    /**
     * Show fields for image block template
     * @param $fileTemplateId
     * @return string
     */
    public function actionShowImageBlockFields($fileTemplateId)
    {

    }
}
