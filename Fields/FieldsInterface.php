<?php

namespace Iliich246\YicmsCommon\Fields;

/**
 * Interface FieldsInterface
 *
 * This interface must implement any class, that must has fields functionality.
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface FieldsInterface
{
    /**
     * Return FieldHandler object, that aggregated in object with field functionality.
     * @return FieldsHandler
     */
    public function getFieldHandler();

    /**
     * This method must proxy FieldHandler method for work with him directly from aggregator.
     * @param string $name
     * @return Field
     */
    public function getField($name);
}
