<?php
/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        10.12.2015
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class LocalizationNotFoundException extends Services\ExceptionAdapter {
    public function __construct($kernel, $entity = "", $code = 600000, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'No localization entry found in database for entity '.get_class($entity).' with id '.$entity->getId(),
            $code,
            $previous);
    }
}

/**
 * 600xxx :: Localization & translation related exceptions.
 */