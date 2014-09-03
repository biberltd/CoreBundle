<?php
/**
 * Core Class
 *
 * This class provides an abstract foundation to all Biber Ltd. Core files.
 *
 * @vendor      BiberLtd
 * @package		Core
 * @subpackage
 * @name	    Core
 *
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.1.0
 * @date        12.01.2014
 *
 */
namespace BiberLtd\CoreBundle;

class Core{
    protected $error        = array();
    public $timezone        = 'Europe/Istanbul';
    protected $kernel;

    public function __construct($kernel){
        $this->kernel = $kernel;
    }
    
    /**
     * @name            debug()
     * prints provided content
     *
     * @author          Said İmamoğlu
     *
     * @param mixed $var Content of variable
     * @since           1.0.0
     * @version         1.0.0
     *
     */
    public function debug($var) {
        echo '<pre>';
        var_dump($var);
        die;
    }
}
/**
 * Change Log
 * **************************************
 * v1.1.0                      Can Berkol
 * 12.01.2014
 * **************************************
 * D display_errors()
 * D email_errors()
 * D issue_error()
 * D process_errors()
 *
 * Since the invention of exception-bundle
 * we don't need these mechanisms any
 * more. Therefore this class will be
 * either deprecated or it will be used
 * as a generic wrapper for more generic
 * functionalities.
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 27.05.2013
 * **************************************
 * A display_errors()
 * A email_errors()
 * A issue_error()
 * A process_errors()
 *
 */