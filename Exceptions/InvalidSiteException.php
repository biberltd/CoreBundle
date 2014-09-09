<?php
/**
 * @name        InvalidParameterException
 * @package		BiberLtd\Bundle\CoreBundleException
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        05.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle generic parameter issues.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class InvalidSiteException extends Services\ExceptionAdapter {
    public function __construct($kernel, $site = "", $code = 998012, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The site with the id "'.$site.'" that you want to add the language in does not exist.',
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