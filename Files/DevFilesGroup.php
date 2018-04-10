<?php

namespace Iliich246\YicmsCommon\Files;

use yii\base\Model;
use yii\widgets\ActiveForm;
use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class DevFilesGroup
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DevFilesGroup extends AbstractGroup
{
    /** @var string fileTemplateReference value for current group */
    protected $fileTemplateReference;
    /** @var FilesBlock current file block template with group is working (create or update) */
    public $filesBlock;
    /** @var FileNamesTranslatesForm[] */
    public $filesNameTranslates;
    /** @var bool indicate that data in this group was saved in this action */
    public $justSaved = false;

    /**
     * Sets fileTemplateReference
     * @param $fileTemplateReference
     */
    public function setFilesTemplateReference($fileTemplateReference)
    {
        $this->fileTemplateReference = $fileTemplateReference;
    }

    /**
     * @inheritdoc
     */
    public function initialize($filesBlockId = null)
    {
        if (!$filesBlockId) {
            $this->filesBlock = new FilesBlock();
            $this->filesBlock->file_template_reference = $this->fileTemplateReference;
            $this->filesBlock->scenario = FilesBlock::SCENARIO_CREATE;
            $this->scenario = self::SCENARIO_CREATE;
        } else {
            $this->filesBlock = FilesBlock::findOne($filesBlockId);

            if (!$this->filesBlock) throw new CommonException("Wrong filesBlock = $filesBlockId");

            $this->filesBlock->scenario = FilesBlock::SCENARIO_UPDATE;
            $this->scenario = self::SCENARIO_UPDATE;
        }

        $languages = Language::getInstance()->usedLanguages();

        $this->filesNameTranslates = [];

        foreach($languages as $key => $language) {

            $fileNameTranslates = new FileNamesTranslatesForm();
            $fileNameTranslates->setLanguage($language);
            $fileNameTranslates->setFilesBlockTemplate($this->filesBlock);

            if (!$this->filesBlock->isNewRecord)
                $fileNameTranslates->loadFromDb();

            $this->filesNameTranslates[$key] = $fileNameTranslates;
        }
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return ($this->filesBlock->validate() && Model::validateMultiple($this->filesNameTranslates));
    }

    /**
     * @inheritdoc
     */
    public function load($data)
    {
        return $this->filesBlock->load($data) && Model::loadMultiple($this->filesNameTranslates, $data);
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        $needSaveFileBlock = false;

        if (!$needSaveFileBlock &&
            $this->filesBlock->getOldAttribute('program_name') != $this->filesBlock->program_name)
            $needSaveFileBlock = true;

        if (!$needSaveFileBlock &&
            $this->filesBlock->getOldAttribute('type') != $this->filesBlock->type)
            $needSaveFileBlock = true;

        if (!$needSaveFileBlock &&
            $this->filesBlock->getOldAttribute('language_type') != $this->filesBlock->language_type)
            $needSaveFileBlock = true;

        if (!$needSaveFileBlock &&
            $this->filesBlock->getOldAttribute('visible') != $this->filesBlock->visible)
            $needSaveFileBlock = true;

        if (!$needSaveFileBlock &&
            $this->filesBlock->getOldAttribute('editable') != $this->filesBlock->editable)
            $needSaveFileBlock = true;

        if (!$needSaveFileBlock &&
            $this->filesBlock->getOldAttribute('max_files') != $this->filesBlock->max_files)
            $needSaveFileBlock = true;

        $success = true;

        if ($needSaveFileBlock)
            $success = $this->filesBlock->save(false);

        if (!$success) return false;

        /** @var FileNamesTranslatesForm $fieldNameTranslate */
        foreach($this->filesNameTranslates as $fileNameTranslate) {

            $needSaveFileTemplateName = false;

            if (!$needSaveFileTemplateName &&
                $fileNameTranslate->name != $fileNameTranslate->getCurrentTranslateDb()->name)
                $needSaveFileTemplateName = true;

            if (!$needSaveFileTemplateName &&
                $fileNameTranslate->description != $fileNameTranslate->getCurrentTranslateDb()->description)
                $needSaveFileTemplateName = true;

            if ($needSaveFileTemplateName)
                $fileNameTranslate->save();
        }

        $this->justSaved = true;

        //TODO: makes error handling
        return true;
    }

    /**
     * @inheritdoc
     */
    public function render(ActiveForm $form)
    {
        throw new CommonException('Not implemented for developer files group (not necessary)');
    }
}
