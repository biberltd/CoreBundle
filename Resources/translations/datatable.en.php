<?php
/**
 * datatable.en.php
 *
 * This file registers the bundle's core messages in English.
 *
 * @vendor      BiberLtd
 * @package		Core\Bundles\CoreBundle
 * @subpackage	Resources
 * @name	    datatable.en.php
 *
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.0
 * @date        04.12.2014
 *
 * =============================================================================================================
 * !!! IMPORTANT !!!
 *
 * Depending your environment run the following code after you have modified this file to clear Symfony Cache.
 * Otherwise your changes will NOT take affect!
 *
 * $ sudo -u apache php app/console cache:clear
 * OR
 * $ php app/console cache:clear
 * =============================================================================================================
 * TODOs:
 * None
 */
/** Nested keys are accepted */
return array(
    'btn'           => array(
        'edit'          => 'Edit',
    ),
    'lbl'           => array(
        'find'          => 'Find',
        'first'         => 'First',
        'info'          => 'Showing _START_ - _END_ Records Out of _TOTAL_',
        'last'          => 'Last',
        'limit'         => 'Show _MENU_ Items',
        'next'          => 'Next',
        'prev'          => 'Prev',
        'processing'    => 'Processing...',
        'record_not_found' => 'No Matching Records Found',
        'no_records'    => 'No Records',
        'number_of_records' => '(in _MAX_ Entries)',
    ),
);
/**
 * Change Log
 * **************************************
 * v1.0.0                      Can Berkol
 * 04.01.2014
 * **************************************
 * A btn
 * A btn.edit
 * A lbl
 * A lbl.find
 * A lbl.first
 * A lbl.info
 * A lbl.last
 * A lbl.limit
 * A lbl.next
 * A lbl.prev
 * A lbl.processing
 * A lbl.record_not_found
 * A lbl.no_records
 * A lbl.number_or_records
 */