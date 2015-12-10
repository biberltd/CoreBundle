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

class InvalidEntityException extends Services\ExceptionAdapter {
    public function __construct($kernel, $entity = "", $code = 998002, Exception $previous = null) {
        parent::__construct(
            $kernel,
            '"'.$entity.'" entity expected, but another type of value is provided.',
            $code,
            $previous);
    }
}