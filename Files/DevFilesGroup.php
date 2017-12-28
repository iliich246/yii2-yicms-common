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
    /**
     * @var integer fileTemplateReference value for current group
     */
    protected $fileTemplateReference;
    /**
     * @var FilesBlock current file block template with group is working (create or update)
     */
    protected $filesBlock;
    /**
     * @var FileNamesTranslatesForm[]
     */
    public $filedNameTranslates;

    /**
     *  Sets fileTemplateReference
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

        foreach($languages as $key => $language) {

            $fileNameTranslates = new FileNamesTranslatesForm();
            $fileNameTranslates->setLanguage($language);
            $fileNameTranslates->setFilesBlockTemplate($this->filesBlock);

            if (!$this->filesBlock->isNewRecord)
                $fileNameTranslates->loadFromDb();

            $this->filedNameTranslates[$key] = $fileNameTranslates;
        }
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return ($this->filesBlock->validate() && Model::validateMultiple($this->filedNameTranslates));
    }

    /**
     * @inheritdoc
     */
    public function load($data)
    {
        return $this->filesBlock->load($data) && Model::loadMultiple($this->filedNameTranslates, $data);
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
        throw new CommonException('Not implemented for developer files group (not necessary)');
    }
}
