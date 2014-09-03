<?php
/**
 * @name        DuplicateAssociationException
 * @package		BiberLtd\Core\Exceptions
 *
 * @author		Can Berkol
 * @version     1.0.1
 * @date        17.06.2014
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description 995001 :: If the association already exists in database.
 *
 */
namespace BiberLtd\Core\Exceptions;

use BiberLtd\Bundles\ExceptionBundle\Services;

class DuplicateAssociationException extends Services\ExceptionAdapter {
    public function __construct($kernel, $msg = "", $code = 'CRE0003', Exception $previous = null) {
        $numeriCode = ord($code[0]).ord($code[1]).ord($code[2]).substr($code, 3, 3);
        parent::__construct(
            $kernel,
            $code.' :: Duplicate Association'.PHP_EOL.$msg,
            $numeriCode,
            $previous);
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.1                      Can Berkol
 * 17.06.2014
 * **************************************
 * U __construct()
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 01.08.2013
 * **************************************
 * A __construct()
 *
 */