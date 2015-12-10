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

trait LocalizationTrait {
	/**
	 * @param $translate
	 *
	 * @return string
	 */
	public function generateUrlKey($translate) {
		$dictionaries = array(
				'upper' => array(
					/** TURKISH */
						'Ç' => 'C', 	'I' => 'I', 	'İ' => 'I', 	'Ö' => 'O', 	'Ş' => 'S',		'Ü' => 'U',
						'Ğ' => 'G',
					/** PUNCTUATION */
						'.' => '_', 	' ' => '_', 	'/' => '_', 	'\\' => '_',  	'*' => '_', 	'+' => '_',
						'%' => '_',		'#' => '_', 	'{' => '_', 	'}' => '_', 	',' => '_', 	';' => '_',
						'?' => '_', 	'!' => '_', 	':' => '_', 	'=' => '_', 	'"' => '_', 	"'" => '_',
						'`' => '_', 	'¨' => '_',		'(' => '_',		')' => '_',  	'[' => '_', 	']' => '_',
						'*' => '_', 	'|' => '_', 	'<' => '_', 	'>' => '_',		'$' => '_',
					/** EUROPEAN ACCENTS */
						'é' => 'e',
					/** RUSSIAN */
						'Б' => 'B', 	'Г' => 'H', 	'Ґ' => 'G', 	'Д' => 'D', 	'Є' => 'E', 	'Ж' => 'ZH,',
						'З' => 'Z', 	'И' => 'Y', 	'Ї' => 'YI', 	'Й' => 'J', 	'Л' => 'L', 	'П' => 'P',
						'У' => 'U', 	'Ф' => 'F',		'Ц' => 'TS', 	'Ч' => 'CH', 	'Ш' => 'SH', 	'Щ' => 'SHCH',
						'Ь' => '_', 	'Ю' => 'yu', 	'Я' => 'ya'
				),
				'lower' => array(
					/** TURKISH */
						'ç' => 'c',		'ı' => 'i',		'i' => 'i',		'ö' => 'o',		'ş' => 's',		'ü' => 'u',
						'ğ' => 'g',
					/** PUNCTUATION */
						'.' => '_',		' ' => '_',		'/' => '_',		'\\' => '_',	'*' => '_',
						'+' => '_',		'%' => '_',		'#' => '_',		'{' => '_',		'}' => '_',		',' => '_',
						';' => '_',		'?' => '_',		'!' => '_',		':' => '_',		'=' => '_',		'$' => '_',
						'"' => '_',		"'" => '_',		'`' => '_',		'¨' => '_',		'(' => '_',		')' => '_',
						'[' => '_',		']' => '_',		'*' => '_',		'|' => '_',		'<' => '_',		'>' => '_',
					/** EUROPEAN ACCENTS */
						'é' => 'e',
					/** RUSSIAN */
						'б' => 'b',		'в' => 'v',		'г' => 'h',		'ґ' => 'g',		'д' => 'd', 	'є' => 'e',
						'ж' => 'zh', 	'з' => 'z', 	'и' => 'y',		'ї' => 'yi', 	'й' => 'j', 	'л' => 'l',
						'м' => 'm', 	'н' => 'n', 	'п' => 'p',		'ф' => 'F', 	'ц' => 'ts', 	'ч' => 'ch',
						'ш' => 'sh', 	'щ' => 'shch', 	'ь' => '_',		'ю' => 'yu', 	'я' => 'ya',
				),
		);
		$translated = strtr($translate, $dictionaries['lower']);
		$translated = strtr($translated, $dictionaries['upper']);
		return strtolower($translated);
	}
}