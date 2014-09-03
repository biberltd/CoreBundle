<?php
/**
 * @name        InvalidSelectorException
 * @package		BiberLtd\Core\Bundles\SiteManagementBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        03.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Throws when the provided parameter value is not a unique identifier field.
 *
 */
namespace BiberLtd\Core\Exceptions;

use BiberLtd\Bundles\ExceptionBundle\Services;

class InvalidSelectorException extends Services\ExceptionAdapter {
    public function __construct($kernel, $message = "", $code = 998008, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'You can only use "id" or "url_key" fields as selector.',
            $code,
            $previous);
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Can Berkol
 * 03.08.2013
 * **************************************
 * A __construct()
 *
 */