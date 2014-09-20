<?php
/**
 * content_editor.en.php
 *
 * This file registers the bundle's core messages in English.
 *
 * @vendor      BiberLtd
 * @package		Core\Bundles\CoreBundle
 * @subpackage	Resources
 * @name	    content_editor.en.php
 *
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.0
 * @date        06.02.2014
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
        'add_image'     => 'Add Image',
        'add_text'      => 'Add Text',
        'change'        => 'Change',
        'delete'        => 'Delete',
        'no'            => 'No',
        'save'          => 'Save',
        'yes'           => 'Yes',
    ),
    'lbl'           => array(
        'confirm'       => array(
            'delete'            => 'Are you sure that you want to delete this part of the content?',
        ),
        'save'          => array(
            'error'             => 'There has been an unknown error. Please try saving your content again.',
            'success'           => 'Your content has been succesfully saved.',
        ),
        'saving'        => 'saving data...'
    ),
);
/**
 * Change Log
 * **************************************
 * v1.0.0                      Can Berkol
 * 06.02.2014
 * **************************************
 * A btn
 * A btn.add_inmage
 * A btn.add_text
 * A btn.change
 * A btn.delete
 * A btn.no
 * A btn.save
 * A btn.yes
 * A lbl
 * A lbl.confirm.delete
 * A lbl.confirm.delete
 * A lbl.save
 * A lbl.save.error
 * A lbl.save.success
 * A lbl.saving
 *
 */