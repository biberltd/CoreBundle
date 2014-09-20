<?php
/**
 * datatable.tr.php
 *
 * This file registers the bundle's core messages in English.
 *
 * @vendor      BiberLtd
 * @package		Core\Bundles\CoreBundle
 * @subpackage	Resources
 * @name	    datatable.tr.php
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
        'edit'          => 'Düzenle',
    ),
    'lbl'           => array(
        'find'          => 'Bul',
        'first'         => 'İlk',
        'info'          => '_TOTAL_ Kayıt Arasından _START_ - _END_ Arası Kayıtlar',
        'last'          => 'Son',
        'limit'         => 'Sayfada _MENU_ Kayıt Göster',
        'next'          => 'Sonraki',
        'prev'          => 'Önceki',
        'processing'    => 'İşlem Devam Ediyor...',
        'record_not_found' => 'Aramanıza Uygun Kayıt Bulunamadı',
        'no_records'    => 'Kayıt Bulunamadı',
        'number_of_records' => '(_MAX_ Kayıt Arasından Bulunan)',
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