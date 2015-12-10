<?php
/**
 * @author		Can Berkol
 * @author		Said İmamoğlu
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        10.12.2015
 */
namespace BiberLtd\Bundle\CoreBundle\CoreTraits;

trait DebugTrait {
	/**
	 * @param           $var
	 * @param bool|true $exit
	 *
	 * If $exit = true then th method forces the application to stop.
	 */
	function debug($var, $exit = true) {
		echo '<pre>';
		if(is_object($var)){
			$reflectionClass = new \ReflectionClass($var);
			$props = $reflectionClass->getProperties();
			$methods = $reflectionClass->getMethods();
			echo 'Class Properties ::<br><br>';
			foreach ($methods as $method) {
				echo $method->class . '->' . $method->name . '()' . '<br>';
			}
			echo 'Class Methods ::<br><br>';
			foreach ($props as $prop) {
				echo $method->class . '->' . $prop->getName() . '<br>';
			}

		}
		else{
			var_dump($var);
		}
		if ($exit) {
			die;
		}
	}
}