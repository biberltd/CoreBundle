<?php
/**
 * @name        SiteDoesNotExistException
 * @package		BiberLtd\Bundle\SiteManagementBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        03.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Throws when the requested site is not found in database.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class SiteDoesNotExistException extends Services\ExceptionAdapter {
    public function __construct($kernel, $site = "", $code = 997003, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The requested site with id "'.$site.'" is not found in the system.',
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