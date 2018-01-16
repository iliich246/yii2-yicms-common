<?php

namespace Iliich246\YicmsCommon\Fields;

/**
 * Interface FieldRenderInterface
 *
 * This interface must implement all fields classes
 * that must be rendered via FieldTypeWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface FieldRenderInterface
{
    /**
     * Returns type of field essence
     * @return integer
     */
    public function getType();

    /**
     * Returns language type of field essence
     * @return integer
     */
    public function getLanguageType();

    /**
     * Returns true is field is editable
     * @return bool
     */
    public function isEditable();

    /**
     * Return true is field is visible
     * @return bool
     */
    public function isVisible();

    /**
     * Returns instance of field template associated with object
     * @return FieldTemplate
     */
    public function getTemplate();

    /**
     * Returns form key of field
     * example $form->field($model, <key>)->...
     * @return string
     */
    public function getKey();

    /**
     * Returns name of field for admin panel on correct language
     * @return string
     */
    public function getFieldName();

    /**
     * Returns description of field for admin panel on correct language
     * @return string
     */
    public function getFieldDescription();

    /**
     * Returns id of field object
     * @return integer
     */
    public function getFieldId();
}
