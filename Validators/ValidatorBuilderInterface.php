<?php

namespace Iliich246\YicmsCommon\Validators;

/**
 * Interface ValidatorBuilderInterface
 *
 * @author iliich246 <iliich246@gmail.com>
 */
interface ValidatorBuilderInterface
{
    /**
     * Returns instance of AbstractValidatorDb
     * @return ValidatorBuilder
     */
    public function getValidatorBuilder();
}
