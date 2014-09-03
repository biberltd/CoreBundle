<?php
/**
 * @name        LanguageDoesNotExist
 * @package		BiberLtd\Core\Bundles\MultiLanguageSupportBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        01.08.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to inexisting Language entries.
 *
 */
namespace BiberLtd\Core\Exceptions;

use BiberLtd\Bundles\ExceptionBundle\Services;

class LanguageDoesNotExist extends Services\ExceptionAdapter {
    public function __construct($kernel, $lang = "", $code = 997000, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The requested language with the given id/iso_code/url_key '.$lang.' cannot be found in database.',
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