<?php
/**
 * @name        EntityAlreadyExistException
 * @package		BiberLtd\Core\Bundles\MemberManagementBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        25.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle inexisting entries.
 *
 */
namespace BiberLtd\Core\Exceptions;

use BiberLtd\Bundles\ExceptionBundle\Services;

class EntityAlreadyExistException extends Services\ExceptionAdapter {
    public function __construct($kernel, $msg = "", $code = 997004, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The requested entry already exist in database. Expected : '.$msg,
            $code,
            $previous);
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Can Berkol
 * 05.08.2013
 * **************************************
 * A __construct()
 *
 */