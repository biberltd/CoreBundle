<?php
/**
 * @name        AssociationException
 * @package		BiberLtd\Bundle\CoreBundle\Exceptions
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        05.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description 995000 :: If no association is found in database.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class AssociationException extends Services\ExceptionAdapter {
    public function __construct($kernel, $msg = "", $code = 995000, Exception $previous = null) {
        parent::__construct(
            $kernel,
            $msg,
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