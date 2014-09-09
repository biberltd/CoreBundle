<?php
/**
 * @name        MissingActivationKeyException
 * @package		BiberLtd\Bundle\MemberManagementBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        05.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception that checks for activation key.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class MissingActivationKeyException extends Services\ExceptionAdapter {
    public function __construct($kernel, $group = "", $code = 996001, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The activation key is required to activate the member unless $bypass parameter is set to true.',
            $code,
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