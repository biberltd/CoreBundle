<?php
/**
 * @name        InvalidDataException
 * @package		BiberLtd\Bundle\MemberManagementBundle
 *
 * @author		Can Berkol
 * @version     1.0.0
 * @date        27.01.2014
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Exception to handle date time values.
 *
 */
namespace BiberLtd\Bundle\CoreBundle\Exceptions;

use BiberLtd\Bundle\ExceptionBundle\Services;

class InvalidDataException extends Services\ExceptionAdapter {
    public function __construct($kernel, $parameter = "", $code = 989001, Exception $previous = null) {
        parent::__construct(
            $kernel,
            'The parameter must be an array of stdClass objects with keys identical to the entity and with an extra key called "local".
            <br><br>
            Sample:<pre>
            stdClass Object
            (
                [csfr] => c0436ae6e196a67eaad16a2f35f5d568
                [attribute] => Array
                    (
                        [0] => stdClass Object
                            (
                                [sortorder] => 12
                                [local] => stdClass Object
                                    (
                                        [tr] => stdClass Object
                                            (
                                                [title] => yeni özellik
                                            )

                                        [en] => stdClass Object
                                            (
                                                [title] => new attribute
                                            )
                                    )
                            )
                    )
            )</pre>',

            $code,
            $previous);
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Can Berkol
 * 27.01.2014
 * **************************************
 * A __construct()
 *
 */