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
}
