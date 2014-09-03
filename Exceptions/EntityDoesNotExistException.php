<?php
/**
 * @name        EntityDoesNotExistException
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

class EntityDoesNotExistException extends Services\ExceptionAdapter {

    public function __construct($kernel, $msg = "", $code = 'CRE002', Exception $previous = null) {
        $numericCode = ord($code[0]).ord($code[1]).ord($code[2]).substr($code, 2, 3);
        parent::__construct(
            $kernel,
            $code.' :: Entity Does Not Exist'.PHP_EOL.'The requested entity cannot be found in database. '.$msg,
            $numericCode,
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