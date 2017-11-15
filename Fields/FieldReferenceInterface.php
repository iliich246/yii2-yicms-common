<?php

namespace Iliich246\YicmsCommon\Fields;

/**
 * Interface FieldReferenceInterface
 *
 * This interface must implement any class, that must has fields functionality.
 * All that objects must have ability to give two variables for correct work with field functionality:
 *
 * Variable templateFieldReference used for pointing on group of fields, called fields template.
 * Variable fieldReference user for pointing on single field.
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface FieldReferenceInterface
{
    /**
     * Returns templateFieldReference
     * @return integer
     */
    public function getTemplateFieldReference();

    /**
     * Returns fieldReference
     * @return integer
     */
    public function getFieldReference();
}
