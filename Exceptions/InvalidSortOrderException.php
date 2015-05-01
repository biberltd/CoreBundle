<?php
/**
 * @name        InvalidSortOrderException
 * @package		BiberLtd\Bundle\MultiLanguageSupportBundle
 *
 * @author		Can Berkol
 * @version     1.0.2
 * @date        01.05.2015
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Throws when the provided parameter value is not a valid sort order.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class InvalidSortOrderException extends Services\ExceptionAdapter {
	public function __construct($kernel, $message = "", $code = 998035, Exception $previous = null) {
		parent::__construct(
			$kernel,
			'The $sortOrder parameter only accepts key => value pair and value can be only one of the following: "asc" or "desc."',
			$code,
			$previous);
	}
}
/**
 * Change Log:
 * **************************************
 * v1.0.2                      01.05.2015
 * Can Berkol
 * **************************************
 * CR :: Message structure updated.
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * 25.08.2013
 * **************************************
 * U __construct()
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 01.08.2013
 * **************************************
 * A __construct()
 *
 */