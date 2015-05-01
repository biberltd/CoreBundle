<?php
/**
 * @name        EntityDoesNotExistException
 * @package		BiberLtd\Bundle\MemberManagementBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        25.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle inexisting entries.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class EntityDoesNotExistException extends Services\ExceptionAdapter {

    public function __construct($kernel, $msg = "", $code = '998025', Exception $previous = null) {
		parent::__construct(
			$kernel,
			'The entry does not exist in database: '.$msg,
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