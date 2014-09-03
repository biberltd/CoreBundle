<?php
/**
 * @name        DuplicateMemberGroupException
 * @package		BiberLtd\Core\Exceptions
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
namespace BiberLtd\Core\Exceptions;

use BiberLtd\Bundles\ExceptionBundle\Services;

class DuplicateMemberGroupException extends Services\ExceptionAdapter {
    public function __construct($kernel, $msg = "", $code = 999002, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'A member group with the code "'.$msg.'" already exists in database.',
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