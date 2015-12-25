<?php
/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        11.12.2015
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class InvalidEntityException extends Services\ExceptionAdapter {
    public function __construct($kernel, $table = "", $code = 998001, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The requested entry is not found in '.$table.' table.',
            $code,
            $previous);
    }
}