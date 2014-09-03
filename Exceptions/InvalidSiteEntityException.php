<?php
/**
 * @name        InvalidSiteEntityException
 * @package		BiberLtd\Core\Bundles\SiteManagementBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        03.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Throws when the provided parameter value or the collection item value is not a valid Site entity.
 *
 */
namespace BiberLtd\Core\Exceptions;

use BiberLtd\Bundles\ExceptionBundle\Services;

class InvalidSiteEntityException extends Services\ExceptionAdapter {
    public function __construct($kernel, $message = "", $code = 998011, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'A valid Site Entity must be provided.',
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