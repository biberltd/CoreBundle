<?php
/**
 * @name        InvalidSiteColumnException
 * @package		BiberLtd\Bundle\SiteManagementBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        04.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Throws when the site does not have the requested column or when the requested column is not settable.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class InvalidSiteColumnException extends Services\ExceptionAdapter {
    public function __construct($kernel, $message = "", $code = 998010, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The requested column "'.$message.'" is either not set in "site" database table or it is not settable.',
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