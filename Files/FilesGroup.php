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

    /**
     * @var File instance for this group
     */
    public $file;

    /**
     * Set $fileTemplateReference
     * @param $fileTemplateReference
     */
    public function setFileTemplateReference($fileTemplateReference)
    {
        $this->fileTemplateReference = $fileTemplateReference;
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        $filesBlockQuery = FilesBlock::getListQuery($this->fileTemplateReference);

        if (!CommonModule::isUnderDev()) $filesBlockQuery->andWhere([
            'editable' => true,
        ]);

        $filesBlockQuery->orderBy([
            FilesBlock::getOrderFieldName() => SORT_ASC
        ])->indexBy('id');

        $languages = Language::getInstance()->usedLanguages();
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
