<?php
/**
 * @name        InvalidFilterException
 * @package		BiberLtd\Core\Bundles\MultiLanguageSupportBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        05.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Throws when the provided parameter value is not a valid filter.
 *
 */
namespace BiberLtd\Core\Exceptions;

use BiberLtd\Bundles\ExceptionBundle\Services;

class InvalidFilterException extends Services\ExceptionAdapter {
    public function __construct($kernel, $message = "", $code = 9980003, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The filter parameter must be an array that matches the method documentation.',
            $code,
            $previous);
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Can Berkol
 * 01.08.2013
 * **************************************
 * A __construct()
 *
 */