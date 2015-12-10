<?php
/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        10.12.2015
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