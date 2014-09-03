<?php
/**
 * @name        InvalidParameterException
 * @package		BiberLtd\Core\Bundles\CorBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        10.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle generic parameter issues.
 *
 */
namespace BiberLtd\Core\Exceptions;

use BiberLtd\Bundles\ExceptionBundle\Services;

class InvalidPasswordException extends Services\ExceptionAdapter {
    public function __construct($kernel, $msg = "", $code = 998014, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'Invalid password has been supplied by the user.',
            $code,
            $previous);
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Can Berkol
 * 10.08.2013
 * **************************************
 * A __construct()
 *
 */