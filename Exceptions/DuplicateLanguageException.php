<?php
/**
 * @name        DuplicateLanguageException
 * @package		BiberLtd\Bundle\CoreBundle\Exceptions
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        05.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle duplicate language entries.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class DuplicateLanguageException extends Services\ExceptionAdapter {
    public function __construct($kernel, $id = "", $code = 999000, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'A language with the url_key "'.$id.'" already exists in database.',
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