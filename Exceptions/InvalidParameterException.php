<?php
/**
 * @name        InvalidParameterException
 * @package		BiberLtd\Bundle\CoreBundle
 *
 * @author		Can Berkol
 * @version     1.0.2
 * @date        17.06.2014
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle generic parameter issues.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class InvalidParameterException extends Services\ExceptionAdapter {
    public function __construct($kernel, $msg = "", $code = 'CRE0001', Exception $previous = null) {
        $numeriCode = ord($code[0]).ord($code[1]).ord($code[2]).substr($code, 3, 3);
        parent::__construct(
            $kernel,
            $code.' :: Invalid Parameter'.PHP_EOL.$msg,
            $numeriCode,
            $previous);
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.2                      Can Berkol
 * 17.06.2014
 * **************************************
 * U __construct() Message changed.
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * 11.10.2013
 * **************************************
 * U __construct() Message changed.
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 01.08.2013
 * **************************************
 * A __construct()
 *
 */