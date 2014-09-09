<?php
/**
 * @name        InvalidMethodException
 * @package		BiberLtd\Bundle\MultiLanguageSupportBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        05.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle generic parameter issues.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class InvalidMethodException extends Services\ExceptionAdapter {
    public function __construct($kernel, $method = "", $code = 998006, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The class does not have the a method named "'.$method.'", or the method has protected or private access level.',
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