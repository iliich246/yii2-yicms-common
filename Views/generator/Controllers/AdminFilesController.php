<?php

namespace app\yicms\Common\Controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AdminFilter;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use Iliich246\YicmsCommon\Fields\FieldsGroup;
use Iliich246\YicmsCommon\Files\File;
use Iliich246\YicmsCommon\Files\FilesBlock;
use Iliich246\YicmsCommon\Files\FilesGroup;
use Iliich246\YicmsCommon\Conditions\ConditionsGroup;

/**
 * Class AdminFilesController
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class AdminFilesController extends Controller
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
     * Action for load list of files via pjax
     * @param integer $fileBlockId
     * @param string $fileReference
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionFilesList($fileBlockId, $fileReference)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('No Pjax');

        /** @var FilesBlock $fileBlock */
        $fileBlock = FilesBlock::findOne($fileBlockId);

        if (!$fileBlock) throw new NotFoundHttpException('Wrong fileBlockId = ' . $fileBlockId);

        $fileBlock->offAnnotation();

        if (CommonModule::isUnderAdmin() && !$fileBlock->editable)
            throw new NotFoundHttpException('Wrong fileBlockId = ' . $fileBlockId);

        $fileBlock->setFileReference($fileReference);

        $filesQuery = $fileBlock->getEntityQuery();

        if (!CommonModule::isUnderDev())
            $filesQuery->andWhere([
                'editable' => true,
            ]);

        /** @var File[] $filesList */
        $filesList = $filesQuery->all();

        foreach ($filesList as $file)
            $file->offAnnotation();

        return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/files/files-list.php', [
            'fileBlock'     => $fileBlock,
            'filesList'     => $filesList,
            'fileReference' => $fileReference,
        ]);
    }

    /**
     * Action for load new file
     * @param $fileBlockId
     * @param $fileReference
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionLoadNewFile($fileBlockId, $fileReference)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('No Pjax');

        /** @var FilesBlock $fileBlock */
        $fileBlock = FilesBlock::findOne($fileBlockId);

        if (!$fileBlock) throw new NotFoundHttpException('Wrong fileBlockId = ' . $fileBlockId);

        if (CommonModule::isUnderAdmin() && !$fileBlock->editable)
            throw new NotFoundHttpException('Wrong fileBlockId = ' . $fileBlockId);

        $filesGroup = new FilesGroup();
        $filesGroup->scenario = FilesGroup::SCENARIO_CREATE;
        $filesGroup->setFileTemplateReference($fileBlock->file_template_reference);
        $filesGroup->setFileBlock($fileBlock);
        $filesGroup->setFileReference($fileReference);
        $filesGroup->initialize();

        if ($filesGroup->load(Yii::$app->request->post()) && $filesGroup->validate()) {
            $filesGroup->save();

            if (Yii::$app->request->post('_saveAndBack')) {
                return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/files/file-load.php', [
                    'fileBlock'  => $fileBlock,
                    'filesGroup' => $filesGroup,
                    'returnBack' => true,
                ]);
            }

            return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/files/file-load.php', [
                'fileBlock'         => $fileBlock,
                'filesGroup'        => $filesGroup,
                'updateRedirect'    => true,
                'fileIdForRedirect' => $filesGroup->getFileExistedInDbEntity()->id,
            ]);
        }

        return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/files/file-load.php', [
            'fileBlock'  => $fileBlock,
            'filesGroup' => $filesGroup,
        ]);
    }

    /**
     * Action updates file
     * @param $fileId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdateFile($fileId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('No Pjax');

        /** @var File $file */
        $file = File::findOne($fileId);
        $file->offAnnotation();

        if (!$file) throw new NotFoundHttpException('Wrong file id = ' . $fileId);

        if (CommonModule::isUnderAdmin() && !$file->editable)
            throw new NotFoundHttpException('Wrong file id = ' . $fileId);

        /** @var FilesBlock $fileBlock */
        $fileBlock = FilesBlock::findOne($file->common_files_template_id);

        if (!$fileBlock) throw new NotFoundHttpException('Wrong fileBlock in file');

        if (CommonModule::isUnderAdmin() && !$fileBlock->editable)
            throw new NotFoundHttpException('Wrong fileBlockId = ' . $fileBlock->id);

        $file->setEntityBlock($fileBlock);

        $filesGroup = new FilesGroup();
        $filesGroup->scenario = FilesGroup::SCENARIO_UPDATE;
        $filesGroup->setFileTemplateReference($fileBlock->file_template_reference);
        $filesGroup->setFileBlock($fileBlock);
        $filesGroup->setFileEntity($file);
        $filesGroup->setFileReference($file->file_reference);
        $filesGroup->initialize();

        $fieldsGroup = new FieldsGroup();
        $fieldsGroup->setFieldsReferenceAble($file);
        $fieldsGroup->initialize();

        $conditionsGroup = new ConditionsGroup();
        $conditionsGroup->scenario = ConditionsGroup::SCENARIO_UPDATE;
        $conditionsGroup->setConditionsReferenceAble($file);
        $conditionsGroup->initialize();

        $isFilesSave = false;

        if ($filesGroup->load(Yii::$app->request->post()) && $filesGroup->validate()) {

            $isFilesSave = $filesGroup->save();
        }

        $isFieldsSave = false;

        if ($fieldsGroup->load(Yii::$app->request->post()) && $fieldsGroup->validate()) {

            $isFieldsSave = $fieldsGroup->save();
        }

        $isConditionsSave = false;

        if ($conditionsGroup->load(Yii::$app->request->post()) && $conditionsGroup->validate()) {

            $isConditionsSave = $conditionsGroup->save();
        }

        if ($isFilesSave || $isFieldsSave || $isConditionsSave) {
            if (Yii::$app->request->post('_saveAndBack')) {
                return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/files/file-load.php', [
                    'fileBlock'       => $fileBlock,
                    'filesGroup'      => $filesGroup,
                    'fieldsGroup'     => $fieldsGroup,
                    'conditionsGroup' => $conditionsGroup,
                    'fileId'          => $fileId,
                    'returnBack'      => true,
                ]);
            }
        }

        return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/files/file-load.php', [
            'fileBlock'       => $fileBlock,
            'filesGroup'      => $filesGroup,
            'fieldsGroup'     => $fieldsGroup,
            'conditionsGroup' => $conditionsGroup,
            'fileId'          => $fileId,
        ]);
    }

    /**
     * Up file order
     * @param $fileId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpFileOrder($fileId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('No Pjax');

        /** @var File $file */
        $file = File::findOne($fileId);

        if (!$file) throw new NotFoundHttpException('Wrong file id = ' . $fileId);

        /** @var FilesBlock $fileBlock */
        $fileBlock = FilesBlock::findOne($file->common_files_template_id);

        if (!$fileBlock) throw new NotFoundHttpException('Wrong fileBlock in file');

        $fileBlock->setFileReference($file);

        $file->upOrder();

        $filesQuery = $fileBlock->getEntityQuery();

        if (!CommonModule::isUnderDev())
            $filesQuery->andWhere([
                'editable' => true,
            ]);

        $filesList = $filesQuery->all();

        return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/files/files-list.php', [
            'fileBlock'     => $fileBlock,
            'filesList'     => $filesList,
            'fileReference' => $file->file_reference,
        ]);
    }

    /**
     * Down file order
     * @param $fileId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDownFileOrder($fileId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('No Pjax');

        /** @var File $file */
        $file = File::findOne($fileId);

        if (!$file) throw new NotFoundHttpException('Wrong file id = ' . $fileId);

        /** @var FilesBlock $fileBlock */
        $fileBlock = FilesBlock::findOne($file->common_files_template_id);

        if (!$fileBlock) throw new NotFoundHttpException('Wrong fileBlock in file');

        $fileBlock->setFileReference($file);

        $file->downOrder();

        $filesQuery = $fileBlock->getEntityQuery();

        if (!CommonModule::isUnderDev())
            $filesQuery->andWhere([
                'editable' => true,
            ]);

        $filesList = $filesQuery->all();

        return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/files/files-list.php', [
            'fileBlock'     => $fileBlock,
            'filesList'     => $filesList,
            'fileReference' => $file->file_reference,
        ]);
    }

    /**
     * Action for delete file
     * @param $fileId
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDeleteFile($fileId)
    {
        if (!Yii::$app->request->isPjax) throw new BadRequestHttpException('No Pjax');

        /** @var File $file */
        $file = File::findOne($fileId);

        if (!$file) throw new NotFoundHttpException('Wrong file id = ' . $fileId);

        /** @var FilesBlock $fileBlock */
        $fileBlock = FilesBlock::findOne($file->common_files_template_id);

        if (!$fileBlock) throw new NotFoundHttpException('Wrong fileBlock in file');

        $file->delete();

        $filesQuery = $fileBlock->getEntityQuery();

        if (!CommonModule::isUnderDev())
            $filesQuery->andWhere([
                'editable' => true,
            ]);

        $filesList = $filesQuery->all();

        return $this->renderAjax(CommonModule::getInstance()->yicmsLocation . '/Common/Views/files/files-list.php', [
            'fileBlock'     => $fileBlock,
            'filesList'     => $filesList,
            'fileReference' => $file->file_reference,
        ]);
    }

    /**
     * Action for user file uploading
     * @param $fileBlockId
     * @param $fileId
     * @param bool|false $language
     * @return bool|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     * @throws \yii\base\ExitException
     */
    public function actionUploadFile($fileBlockId, $fileId, $language = false)
    {
        /** @var File $file */
        $file = File::findOne($fileId);

        if (!$file) throw new NotFoundHttpException('Wrong file id = ' . $fileId);

        if ($file->common_files_template_id != $fileBlockId)
            throw new NotFoundHttpException('Wrong fileBlockId');

        $fileBlock = $file->getFileBlock();

        if (!$fileBlock)
            throw new NotFoundHttpException('Wrong fileBlock in file');

        if (!$language) $language = Language::getInstance()->getCurrentLanguage();
        else $language = LanguagesDb::findOne($language);

        if (!$language) throw new NotFoundHttpException('Wrong language');

        $path = $file->getPath($language);

        if (!$path) return $this->goBack();

        $fileName = $file->getFileName($language);

        Yii::$app->response->sendFile($path, $fileName. '.' . substr(strrchr($path, '.'), 1))->send();
        Yii::$app->end();

        return false;
    }
}
