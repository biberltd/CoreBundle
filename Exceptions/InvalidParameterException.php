<?php
/**
 * @name        InvalidParameterException
 * @package		BiberLtd\Bundle\CoreBundle
 *
 * @author		Can Berkol
 * @version     1.0.3
 * @date        01.05.2015
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle generic parameter issues.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class InvalidParameterException extends Services\ExceptionAdapter {
    public function __construct($kernel, $msg = "", $code = 998004, Exception $previous = null) {
		parent::__construct(
			$kernel,
			'Invalid parameter. A parameter is either missing or it has wrong value.',
			$code,
			$previous);
	}
}
/**
 * Change Log:
 * **************************************
 * v1.0.3                      01.05.2015
 * Can Berkol
 * **************************************
 * CR :: Exception structure updated.
 *
 * **************************************
 * v1.0.2                      Can Berkol
 * 17.06.2014
 * **************************************
 * U __construct() Message changed.
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * 11.10.2013
 * **************************************
 * U __construct() Message changed.
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 01.08.2013
 * **************************************
 * A __construct()
 *
 */