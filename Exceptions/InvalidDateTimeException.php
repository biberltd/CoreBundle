<?php
/**
 * @name        InvalidDateTimeException
 * @package		BiberLtd\Core\Bundles\MemberManagementBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        07.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle date time values.
 *
 */
namespace BiberLtd\Core\Exceptions;

use BiberLtd\Bundles\ExceptionBundle\Services;

class InvalidDateTimeException extends Services\ExceptionAdapter {
    public function __construct($kernel, $parameter = "", $code = 998001, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The '.$parameter.' parameter value must be an instance of datetime.',
            $code,
            $previous);
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Can Berkol
 * 07.08.2013
 * **************************************
 * A __construct()
 *
 */