<?php

namespace Iliich246\YicmsCommon\Files;

use yii\base\Model;
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
    /**
     * @var string fileTemplateReference value for current group
     */
    protected $fileTemplateReference;


    public $fileBlock;

    /**
     * @var File instance for this group
     */
    public $file;

    public $translateForms = [];

    public $translateFormsArray = [];

    /**
     * Set $fileTemplateReference
     * @param $fileTemplateReference
     */
    public function setFileTemplateReference($fileTemplateReference)
    {
        $this->fileTemplateReference = $fileTemplateReference;
    }

    public function setFileBlock(FilesBlock $filesBlock)
    {
        $this->fileBlock = $filesBlock;
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
//        $filesBlockQuery = FilesBlock::getListQuery($this->fileTemplateReference);
//
//        if (!CommonModule::isUnderDev()) $filesBlockQuery->andWhere([
//            'editable' => true,
//        ]);
//
//        $filesBlockQuery->orderBy([
//            FilesBlock::getOrderFieldName() => SORT_ASC
//        ])->indexBy('id');

        $this->file = new File();
        $this->file->setEntityBlock($this->fileBlock);

        $languages = Language::getInstance()->usedLanguages();

        foreach($languages as $languageKey => $language) {

            $fileTranslate = new FileTranslateForm();
            $fileTranslate->scenario = FileTranslateForm::SCENARIO_CREATE;
            $fileTranslate->setLanguage($language);

            $this->translateForms["$languageKey-"] = $fileTranslate;


        }
    }

    public function initializeUpdate()
    {

    }

    /**
     * @inheritdoc
     */
    public function validate()
    {

    }

    /**
     * @inheritdoc
     */
    public function load($data)
    {

    }

    /**
     * @inheritdoc
     */
    public function save()
    {

    }

    /**
     * @inheritdoc
     */
    public function render(ActiveForm $form)
    {

    }
}
