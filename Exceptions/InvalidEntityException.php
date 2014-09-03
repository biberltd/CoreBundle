<?php
/**
 * @name        InvalidEntityException
 * @package		BiberLtd\Core\Bundles\MultiLanguageSupportBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        05.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle wrong entity types.
 *
 */
namespace BiberLtd\Core\Exceptions;

use BiberLtd\Bundles\ExceptionBundle\Services;

class InvalidEntityException extends Services\ExceptionAdapter {
    public function __construct($kernel, $entity = "", $code = 998002, Exception $previous = null) {
        parent::__construct(
            $kernel,
            '"'.$entity.'" entity expected, but another type of value is provided.',
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