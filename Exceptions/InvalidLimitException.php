<?php
/**
 * @name        InvalidLimitException
 * @package		BiberLtd\Bundle\MultiLanguageSupportBundle
 *
 * @author		Can Berkol
 * @version     1.0.1
 * @date        01.01.2015
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Throws when the provided parameter value is not a valid limit.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class InvalidLimitException extends Services\ExceptionAdapter {
    public function __construct($kernel, $message = "", $code = 998005, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The limit parameter must be an array with at least two keys: "start" and "count". Also, optional "pagination" key can be supplied.',
            $code,
            $previous);
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      01.05.2015
 * Can Berkol
 * **************************************
 * CR :: Exception message updated.
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 01.08.2013
 * **************************************
 * A __construct()
 *
 */