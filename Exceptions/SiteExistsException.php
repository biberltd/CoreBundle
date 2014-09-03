<?php
/**
 * @name        SiteExistsException
 * @package		BiberLtd\Core\Bundles\SiteManagementBundle
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
namespace BiberLtd\Core\Exceptions;

use BiberLtd\Bundles\ExceptionBundle\Services;

class SiteExistsException extends Services\ExceptionAdapter {
    public function __construct($kernel, $site = "", $code = 999003, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The site you want to add already exists in database with the id "'.$site.'".',
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