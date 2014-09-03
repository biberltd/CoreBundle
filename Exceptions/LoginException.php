<?php
/**
 * @name        LoginException
 * @package		BiberLtd\Core\Bundles\MemberManagementBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        01.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle cURL connection problems.
 *
 */
namespace BiberLtd\Core\Exceptions;

use BiberLtd\Bundles\ExceptionBundle\Services;

class LoginException extends Services\ExceptionAdapter {
    public function __construct($kernel, $message = "", $code = 996000, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'Invalid login information is provided by the user.',
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