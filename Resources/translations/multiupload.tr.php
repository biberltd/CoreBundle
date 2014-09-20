<?php
/**
 * multilang.re.php
 *
 * This file registers the bundle's core messages in English.
 *
 * @vendor      BiberLtd
 * @package		Core\Bundles\CoreBundle
 * @subpackage	Resources
 * @name	    multilang.re.php
 *
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.0
 * @date        18.01.2014
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
    'lbl'           => array(
        'default_language' => 'Varsayılan Dil',
        'option'        => array(
            'off'           => 'Evet',
            'on'            => 'Hayır',
        ),
        'switch'        => array(
            'info'          => '* Tercüme ekranlarını açar',
            'title'         => 'Tercüme et:',
        )
    ),
);
/**
 * Change Log
 * **************************************
 * v1.0.0                      Can Berkol
 * 18.01.2014
 * **************************************
 * A lbl
 * A lbl.default_language
 * A lbl.option
 * A lbl.option.off
 * A lbl.option.on
 * A lbl.switch
 * A lbl.switch.info
 * A lbl.switch.title
 */