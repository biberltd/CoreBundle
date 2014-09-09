<?php
/**
 * @name        InvalidCollectionException
 * @package		BiberLtd\Bundle\CoreBundle
 *
 * @author		Can Berkol
 * @version     1.0.1
 * @date        26.06.2014
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle generic parameter issues.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class InvalidCollectionException extends Services\ExceptionAdapter {
    public function __construct($kernel, $msg = "", $code = 'CRE0004', Exception $previous = null) {
        $numeriCode = ord($code[0]).ord($code[1]).ord($code[2]).substr($code, 3, 3);
        parent::__construct(
            $kernel,
            $code.' :: Invalid Collection'.PHP_EOL.' You must provide an "array". ',
            $numeriCode,
            $previous);
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.1                      Can Berkol
 * 26.06.2014
 * **************************************
 * U __construct()
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 18.06.2014
 * **************************************
 * A __construct()
 *
 */