<?php
/**
 * @name        InvalidLimitException
 * @package		BiberLtd\Bundle\MultiLanguageSupportBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        03.08.2013
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
            'The limit parameter must be an array with two keys start and count.',
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