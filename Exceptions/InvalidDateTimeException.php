<?php
/**
 * @name        InvalidDateTimeException
 * @package		BiberLtd\Bundle\MemberManagementBundle
 *
 * @author		Can Berkol
 * @version     1.0.1
 * @date        03.05.2015
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle date time values.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class InvalidDateTimeException extends Services\ExceptionAdapter {
    public function __construct($kernel, $parameter = "", $code = 998001, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The '.$parameter.' parameter value must be an instance of \DateTime.',
            $code,
            $previous);
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.1                      03.05.2015
 * Can Berkol
 * **************************************
 * CR :: Exception message updated.
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 07.08.2013
 * **************************************
 * A __construct()
 *
 */