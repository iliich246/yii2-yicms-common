<?php

namespace Iliich246\YicmsCommon\Fields;

use Iliich246\YicmsCommon\Base\AbstractGroup;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Languages\Language;

/**
 * Class FieldsGroup
 *
 * @package Iliich246\YicmsCommon\Fields
 */
class FieldsGroup extends AbstractGroup
{
    /** @var FieldReferenceInterface|FieldsInterface  */
    protected $referenceAble;

    /**
     * @param FieldReferenceInterface $referenceAble
     */
    public function setFieldsReferenceAble(FieldReferenceInterface $referenceAble)
    {
        $this->referenceAble = $referenceAble;
    }

    public function initialize($fieldTemplateReference = null)
    {
        $fieldTemplatesQuery = FieldTemplate::getListQuery($this->referenceAble->getTemplateFieldReference());

        if (!CommonModule::isUnderDev()) $fieldTemplatesQuery->andWhere([
            'editable' => true,
        ]);

        $fieldTemplatesQuery->orderBy([
            FieldTemplate::getOrderFieldName() => SORT_ASC
        ])->indexBy('id');

        //throw new CommonException(print_r($fieldTemplatesQuery,true));

        /** @var FieldTemplate $fieldTemplates */
        $fieldTemplates = $fieldTemplatesQuery->all();

        $languages = Language::getInstance()->usedLanguages();

        $translateModels = [];
        $splitArray = [];

        foreach($languages as $languageKey => $language) {
            foreach($fieldTemplates as $fieldKey=>$fieldTemplate) {

                $fieldTranslate = new FieldTranslateForm();
                $fieldTranslate->setFieldTemplate($fieldTemplate);
                $fieldTranslate->setLanguage($language);
                $fieldTranslate->setFieldAble($this->referenceAble);
                $fieldTranslate->loadFromDb();

                $translateModels["$languageKey-$fieldKey"] = $fieldTranslate;
                $splitArray[$languageKey][$fieldKey] = $fieldTranslate;
            }
        }


        return $fieldTemplates;
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