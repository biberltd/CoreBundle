<?php
/**
 * @vendor      BiberLtd
 * @package		Core
 * @subpackage
 * @name	    Core
 *
 * @author		Can Berkol
 * @author		Said İmamoğlu
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.1.1
 * @date        30.04.2015
 *
 */
namespace BiberLtd\Bundle\CoreBundle;

class Core{
    protected $error        = array();
    public $timezone        = 'Europe/Istanbul';
    protected $kernel;

    public function __construct($kernel){
        $this->kernel = $kernel;
    }

	/**
	 * @name            debug ()
	 *
	 * @author          Can Berkol
	 * @author          Said İmamoğlu
	 *
	 * @param 			mixed 			$var
	 * @param 			bool 			$exit
	 * @since           1.1.1
	 * @version         1.1.1
	 *
	 */
	public function debug($var, $exit = true) {
		echo '<pre>';
		var_dump($var);
		if ($exit) {
			die;
		}
	}

	/**
	 * @name            debugClass ()
	 *
	 * @author          Said İmamoğlu
	 *
	 * @param 			mixed $class Class
	 * @param 			bool $exit true|false
	 *
	 * @since           1.1.1
	 * @version         1.1.1
	 *
	 */
	public function debugClass($class, $exit = true){
		if (is_object($class)) {
			$reflectionClass = new \ReflectionClass($class);
			$methods = $reflectionClass->getMethods();
			foreach ($methods as $method) {
				echo $method->class . '->' . $method->name . '()' . '<br>';
			}
		} else {
			echo $class . ' is not a valid Class.';
		}
		if ($exit) {
			die;
		}
	}
}
/**
 * Change Log
 * **************************************
 * v1.1.0                      Can Berkol
 * 12.01.2014
 * **************************************
 * CR :: debug() method has been moved into the object.
 * CR :: debugClass() method has been moved into the object.
 *
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