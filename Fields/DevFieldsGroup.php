<?php

namespace Iliich246\YicmsCommon\Fields;

use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Languages\Language;

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
     * @var FieldNamesTranslatesForm
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
            $this->fieldNameTranslates = $fieldNameTranslate;
        }
    }

    public function validate()
    {

    }

    public function load($data)
    {

    }

    public function render()
    {

    }
}
