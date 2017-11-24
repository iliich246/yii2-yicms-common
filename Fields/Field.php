<?php

namespace Iliich246\YicmsCommon\Fields;

use yii\db\ActiveRecord;

/**
 * Class Field
 *
 * @property integer $id
 * @property integer $common_fields_template_id
 * @property integer $field_reference
 * @property integer $editable
 * @property integer $visible
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Field extends ActiveRecord
{
    /**
     * Types of fields
     *
     * Type define style of render of field
     */
    const TYPE_INPUT = 0;
    const TYPE_TEXT = 1;
    const TYPE_REDACTOR = 2;

    /**
     * Modes of field
     *
     * In user mode returns translation only in active language. If can not found translation, returns false.
     *
     * In the administrator mode current object will be to try to find translation on active language.
     * If false he will be try to find translation in any language, which is allowed in system.
     * If false, he return message, that translation not found.
     */
    const MODE_USER = 0;
    const MODE_ADMIN = 1;

    const RETURN_MODE_STRING = 0;
    const RETURN_MODE_OBJECT = 1;

    /**
     * @var int keeps mode of field
     */
    private $mode = self::MODE_USER;

    /**
     * @var int keeps return mode of object
     */
    private $returnMode = self::RETURN_MODE_STRING;

    /**
     * @var
     */
    private $translation = null;

    /** @var FieldTemplate  */
    private $template;

    private $fieldReference;

    private $fieldTemplateReference;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_fields_represents}}';
    }

    /**
     * Sets object in admin mode
     * @return $this
     */
    public function setAdminMode()
    {
        $this->mode = self::MODE_ADMIN;
        return $this;
    }

    /**
     * Sets object in admin mode (it`s default mode)
     * @return $this
     */
    public function setUserMode()
    {
        $this->mode = self::MODE_USER;
        return $this;
    }

    /**
     * Return fetched from db instance of field
     * @param $fieldTemplateReference
     * @param $fieldReference
     * @param $programName
     * @return array|null|ActiveRecord
     */
    public static function getInstance($fieldTemplateReference, $fieldReference, $programName)
    {
        //TODO: may be better to return empty field object
        if (is_null($template = FieldTemplate::getInstance($fieldTemplateReference, $programName))) return null;

        return $field = self::find()->where([
            'common_fields_template_id' => $template->id,
            'field_reference' => $fieldReference
        ])->one();
    }
}
