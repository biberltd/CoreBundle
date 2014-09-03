<?php
/**
 * @name        MemberDoesNotExistException
 * @package		BiberLtd\Core\Bundles\MemberManagementBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        05.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to inexisting Member entries.
 *
 */
namespace BiberLtd\Core\Exceptions;

use BiberLtd\Bundles\ExceptionBundle\Services;

class MemberDoesNotExistException extends Services\ExceptionAdapter {
    public function __construct($kernel, $member = "", $code = 997001, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The requested member with the given id/username/email '.$member.' cannot be found in database.',
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