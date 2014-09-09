<?php
/**
 * @name        DuplicateMemberException
 * @package		BiberLtd\Bundle\CoreBundle\Exceptions
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        07.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle duplicate language entries.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class DuplicateMemberException extends Services\ExceptionAdapter {
    public function __construct($kernel, $msg = "", $code = 999001, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'A member with the code "'.$msg.'" already exists in database.',
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