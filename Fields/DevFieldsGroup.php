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

    public function initialize($fieldTemplateReference = null)
    {
        if (!$fieldTemplateReference) {
            $this->fieldTemplate = new FieldTemplate();
            $this->fieldTemplate->field_template_reference = $this->referenceAble->getTemplateFieldReference();
        } else {
            $this->fieldTemplate = FieldTemplate::findOne($fieldTemplateReference);
        }

        $languages = Language::getInstance()->usedLanguages();

        $this->fieldNameTranslates = [];

        foreach($languages as $key => $language) {

            $fieldNameTranslate = new FieldNamesTranslatesForm();
            $fieldNameTranslate->setLanguage($language);
            $fieldNameTranslate->setFieldTemplate($this->fieldTemplate);
            $fieldNameTranslate->loadFromDb();

            $this->fieldNameTranslates[$key] = $fieldNameTranslate;
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

    public function save()
    {
        $this->fieldTemplate->save(false);

        foreach($this->fieldNameTranslates as $fieldNameTranslate) {
            $fieldNameTranslate->save();
        }
    }

    public function render()
    {

    }
}
