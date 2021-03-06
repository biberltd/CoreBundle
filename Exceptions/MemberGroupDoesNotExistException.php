<?php
/**
 * @name        MemberGroupDoesNotExistException
 * @package		BiberLtd\Bundle\MemberManagementBundle
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
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class MemberGroupDoesNotExistException extends Services\ExceptionAdapter {
    public function __construct($kernel, $group = "", $code = 997002, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The requested member group with the given id/code '.$group.' cannot be found in database.',
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