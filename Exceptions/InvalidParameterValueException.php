<?php
/**
 * @name        InvalidParameterValueException
 * @package		BiberLtd\Bundle\CoreBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        11.10.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle generic parameter issues.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class InvalidParameterValueException extends Services\ExceptionAdapter {
    public function __construct($kernel, $values = "", $code = 998015, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'You must provide a variable with the following value: '.$values,
            $code,
            $previous);
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Can Berkol
 * 11.10.2013
 * **************************************
 * A __construct()
 *
 */