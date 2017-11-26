<?php

namespace Iliich246\YicmsCommon\Fields;

use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Languages\Language;
use yii\base\Model;

/**
 * Class DevFieldsGroup
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DevFieldsGroup extends AbstractGroup
{
    /** @var FieldReferenceInterface  */
    protected $referenceAble;

    /**
     * @var FieldTemplate
     */
    public $fieldTemplate;

    /**
     * @var FieldNamesTranslatesForm[]
     */
    public $fieldNameTranslates;

    /**
     * @param FieldReferenceInterface $referenceAble
     */
    public function setFieldsReferenceAble(FieldReferenceInterface $referenceAble)
    {
        $this->referenceAble = $referenceAble;
    }

    public function initialize()
    {
        $this->fieldTemplate = new FieldTemplate();
        $this->fieldTemplate->field_template_reference = $this->referenceAble->getTemplateFieldReference();

        $languages = Language::getInstance()->usedLanguages();

        $this->fieldNameTranslates = [];

        foreach($languages as $key => $language) {

            $fieldNameTranslate = new FieldNamesTranslatesForm();
            $fieldNameTranslate->setLanguage($language);
            $this->fieldNameTranslates[] = $fieldNameTranslate;
        }
    }

    public function validate()
    {
        return ($this->fieldTemplate->validate() && Model::validateMultiple($this->fieldNameTranslates));
    }

    public function load($data)
    {
        return ($this->fieldTemplate->load($data) && Model::loadMultiple($this->fieldNameTranslates, $data));
    }

    public function render()
    {

    }
}
