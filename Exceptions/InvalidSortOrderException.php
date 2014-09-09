<?php
/**
 * @name        InvalidSortOrderException
 * @package		BiberLtd\Bundle\MultiLanguageSupportBundle
 *
 * @author		Can Berkol
 * @version     1.0.1
 * @date        25.08.2014
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Throws when the provided parameter value is not a valid sort order.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class InvalidSortOrderException extends Services\ExceptionAdapter {
    public function __construct($kernel, $msg = "", $code = "CRE0006", Exception $previous = null) {
        $numeriCode = ord($code[0]).ord($code[1]).ord($code[2]).substr($code, 3, 3);
        parent::__construct(
            $kernel,
            $code.' :: Invalid Sort Order'.PHP_EOL
            .'The sort order parameter only ccepts "asc" or "desc" strings as value.'.PHP_EOL.$msg,
            $numeriCode,
            $previous);
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Can Berkol
 * 25.08.2013
 * **************************************
 * U __construct()
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 01.08.2013
 * **************************************
 * A __construct()
 *
 */