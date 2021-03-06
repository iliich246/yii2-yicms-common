<?php

namespace Iliich246\YicmsCommon\Files;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class FilesGroup
 *
 * This class implements files group for admin part
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FilesGroup extends AbstractGroup
{
    /** @var string fileTemplateReference value for current group */
    protected $fileTemplateReference;
    /** @var string current fileReference key */
    public $fileReference;
    /** @var FilesBlock instance */
    public $fileBlock;
    /** @var File instance for this group */
    public $fileEntity;
    /** @var FileTranslateForm[] */
    public $translateForms = [];

    /**
     * Set current fileTemplateReference
     * @param $fileTemplateReference
     */
    public function setFileTemplateReference($fileTemplateReference)
    {
        $this->fileTemplateReference = $fileTemplateReference;
    }

    /**
     * Sets current fileReference key
     * @param $fileReference
     */
    public function setFileReference($fileReference)
    {
        $this->fileReference = $fileReference;
    }

    /**
     * Sets current FileBlock
     * @param FilesBlock $filesBlock
     */
    public function setFileBlock(FilesBlock $filesBlock)
    {
        $this->fileBlock = $filesBlock;
    }

    /**
     * Sets file entity for update mode
     * @param File $fileEntity
     */
    public function setFileEntity(File $fileEntity)
    {
        $this->fileEntity = $fileEntity;
    }

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function initialize()
    {
        if ($this->scenario == self::SCENARIO_CREATE) {
            $this->fileEntity = new File();
            $this->fileEntity->setEntityBlock($this->fileBlock);
            $this->fileEntity->editable = true;
            $this->fileEntity->visible  = true;
        }

        $this->fileEntity->prepareValidators();

        $fileBlockId = $this->fileBlock->id;

        $languages = Language::getInstance()->usedLanguages();

        foreach ($languages as $languageKey => $language) {

            $fileTranslate = new FileTranslateForm();
            $fileTranslate->scenario = FileTranslateForm::SCENARIO_CREATE;
            $fileTranslate->setFileBlock($this->fileBlock);
            $fileTranslate->setFileEntity($this->fileEntity);
            $fileTranslate->setLanguage($language);

            if ($this->scenario == self::SCENARIO_UPDATE) {
                $fileTranslate->loadFromDb();
            }

            $this->translateForms["$languageKey-$fileBlockId"] = $fileTranslate;
        }
    }

    /**
     * @inheritdoc
     */
    public function load($data)
    {
        $success = false;

        if ($this->fileEntity->load($data)) {
            $this->fileEntity->file = UploadedFile::getInstance($this->fileEntity, "file");
            $success = true;
        }

        if ($success | Model::loadMultiple($this->translateForms, $data)) {
            foreach ($this->translateForms as $fileTranslateForm) {
                $key = $fileTranslateForm->getKey();
                $fileTranslateForm->translatedFile = UploadedFile::getInstance($fileTranslateForm, "[$key]translatedFile");
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        if ($this->fileBlock->language_type == FilesBlock::LANGUAGE_TYPE_SINGLE) {
            $success = false;

            if ($this->fileEntity->validate())
                $success = true;

            if ($success && Model::validateMultiple($this->translateForms, ['filename']))
                return true;

            return false;
        } else {
            return Model::validateMultiple($this->translateForms);
        }
    }

    /**
     * @inheritdoc
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function save()
    {
        $file = $this->getFileExistedInDbEntity();

        $this->fileEntity->file_reference = $this->fileReference;

        $path = CommonModule::getInstance()->filesPatch;

        if ($this->fileEntity->file) {
            if (!is_dir($path))
                FileHelper::createDirectory($path);

            if ($this->fileBlock->language_type == FilesBlock::LANGUAGE_TYPE_SINGLE) {

                if ($this->scenario == self::SCENARIO_UPDATE) {
                    if (file_exists($path . $file->system_name) &&
                        !is_dir($path . $file->system_name))
                        unlink($path . $file->system_name);
                }

                $name = uniqid() . '.' . $this->fileEntity->file->extension;
                $this->fileEntity->file->saveAs($path . $name);

                $this->fileEntity->system_name = $name;
                $this->fileEntity->original_name =
                    $this->fileEntity->file->baseName;
                $this->fileEntity->size = $this->fileEntity->file->size;
                $this->fileEntity->type = FileHelper::getMimeType($path . $name);
            }
        }

        $this->fileEntity->save();

        foreach ($this->translateForms as $fileTranslateForm) {
            if ($this->scenario == self::SCENARIO_CREATE)
                $fileTranslateForm->setFileEntity($file);

            if ($this->fileBlock->language_type == FilesBlock::LANGUAGE_TYPE_TRANSLATABLE) {
                if ($fileTranslateForm->translatedFile) {
                    if (!is_dir($path))
                        FileHelper::createDirectory($path);

                    if ($this->scenario == self::SCENARIO_UPDATE) {
                        if (file_exists($path . $fileTranslateForm->getCurrentTranslateDb()->system_name) &&
                            !is_dir($path . $fileTranslateForm->getCurrentTranslateDb()->system_name))
                            unlink($path . $fileTranslateForm->getCurrentTranslateDb()->system_name);
                    }

                    $name = uniqid() . '.' . $fileTranslateForm->translatedFile->extension;
                    $fileTranslateForm->translatedFile->saveAs($path . $name);

                    $fileTranslateForm->getCurrentTranslateDb()->system_name = $name;
                    $fileTranslateForm->getCurrentTranslateDb()->original_name =
                        $fileTranslateForm->translatedFile->baseName;
                    $fileTranslateForm->getCurrentTranslateDb()->size = $fileTranslateForm->translatedFile->size;
                    $fileTranslateForm->getCurrentTranslateDb()->type = FileHelper::getMimeType($path . $name);
                }
            }

            $fileTranslateForm->getCurrentTranslateDb()->filename = $fileTranslateForm->filename;
            $fileTranslateForm->getCurrentTranslateDb()->save();
        }
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function render(ActiveForm $form)
    {
        return FilesRenderWidget::widget([
            'form'       => $form,
            'filesGroup' => $this,
            'filesBlock' => $this->fileBlock,
        ]);
    }

    /**
     * Return instance of File entity that necessarily exists in the database
     * @return File
     */
    public function getFileExistedInDbEntity()
    {
        if ($this->fileEntity->isNewRecord) {
            $this->fileEntity->common_files_template_id = $this->fileBlock->id;
            $this->fileEntity->file_reference = $this->fileReference;
            $this->fileEntity->file_order = $this->fileEntity->maxOrder();
            $this->fileEntity->visible = true;
            $this->fileEntity->editable = true;

            $this->fileEntity->save();
        }

        return $this->fileEntity;
    }
}
