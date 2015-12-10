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
namespace BiberLtd\Bundle\CoreBundle\Services;
use BiberLtd\Bundle\CoreBundle\Core as Core;

class Encryption extends Core{
    /** @var $key           Encryption key. This is set from configuration files. */
    private     $key;
    /** @var $output        Holds the string to be outputted. */
    protected   $output;
    /** @var    $algorithms  Holds the list of available algorithms. */
    private   $algorithms;
    /** @var    $input      Holds the input string. */
    private   $input;

    /**
     * Encryption constructor.
     *
     * @param string $timezone
     * @param array  $params
     */
    public function __construct($timezone = 'Europe/Istanbul', $params = array()){
        $this->timezone = $timezone;

        if(count($params) > 0){
            foreach($params as $key => $value){
                switch($key){
                    case 'key':
                    case 'input':
                        $this->$key = $value;
                        break;
                    default:
                        break;
                }
            }
        }
        else{
            $this->input = '';
            $this->key = md5('bbr@encryption');
        }
        $this->algorithms = array(
            'enc_reversible_pkey',
            'enc_simple_replace',
            'enc_rot13_simple_replace',
            'hash_md5',
            'hash_sha1'
        );
        $this->output = null;
    }

    /**
     * Destructor
     */
    public function __destruct(){
        foreach($this as $key => $element) {
            $this->$key = null;
        }
    }
    
    /**
     * @param $key
     *
     * @return $this|bool
     */
    private function setKey($key){
        if(!is_string($key)){
            return false;
        }
        $this->key = md5($key);
        return $this;
    }
    
    /**
     * @return string
     */
    private function getKey(){
        return $this->key;
    }
    
    /**
     * @return $this|\BiberLtd\Bundle\CoreBundle\Services\Encryption|bool
     */
    public function key(){
        $count_args = func_num_args();
        $args = func_get_args();

        switch($count_args){
            case 0:
                return $this->getKey();
            case 1:
                return $this->setKey($args[0]);
            default:
                break;
        }
    }
    
    /**
     * @param $input
     *
     * @return $this
     */
    private function setInput($input){
        $this->input = $input;
        return $this;
    }
    /**
     * @name 			get_input()
     *  				Gets the value of the input property.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.2.0
     *
     * @return			string          $this->input
     */
    private function get_input(){
        return $this->input;
    }
    /**
     * @name 			input()
     *  				Accepts 0 or 1 arguments. If there is argument provided, the function returns the value of the
     *                  input property. If there is one argument provided, the function sets the value of the input
     *                  property.
     *
     * @author          Can Berkol
     *
     * @since			1.2.0
     * @version         1.2.0
     *
     * @return			mixed          $this, $this->key, false
     */
    public function input(){
        /** Get arguments passed */
        $count_args = func_num_args();
        $args = func_get_args();
        switch($count_args){
            case 0:
                return $this->get_input();
            case 1:
                return $this->setInput($args[0]);
            default:
                break;
        }
    }
    /**
     * @name 			encrypt()
     *  				Encrypts the given input based on the provided algorithm.
     *
     * @author          Can Berkol
     *
     * @since			1.2.0
     * @version         1.2.3
     *
     * @param           string          $algorithm          Selected encryption algorithm.
     *
     * @return			mixed           $this, false
     */
    public function encrypt($algorithm = 'enc_reversible_pkey'){
        $func = '';
        switch($algorithm){
            case 'enc_reversible_pkey':
            case 'enc_simple_replace':
            case 'enc_shift_simple_replace':
            case 'enc_shift_simple_replace':
            case 'hash_md5':
            case 'hash_sha1':
                $func .= 'encrypt_via_'.$algorithm;
                break;
            default:
                return false;
        }
        return $this->$func();
    }

    /**
     * @param string $algorithm
     *
     * @return bool
     */
    public function decrypt($algorithm = 'enc_reversible_pkey'){
        $func = '';
        switch($algorithm){
            case 'enc_reversible_pkey':
                $func .= 'decrypt_via_'.$algorithm;
                break;
            case 'enc_simple_replace':
            case 'enc_rot13_simple_replace':
            case 'hash_md5':
            case 'hash_sha1':
            default:
                return false;
        }
        return $this->$func();
    }

    /**
     * @return $this
     */
    private function encrypt_via_enc_reversible_pkey(){
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $cryptText = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->key(), $this->input(), MCRYPT_MODE_ECB, $iv);
        $cryptText = base64_encode($cryptText);
        $this->output = $cryptText;
        return $this;
    }

    /**
     * @return $this
     */
    private function decrypt_via_enc_reversible_pkey(){
        $encryptedText = base64_decode($this->input());
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $deCryptText = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->key(), $encryptedText, MCRYPT_MODE_ECB, $iv);
        $this->output = $deCryptText;
        return $this;
    }

    /**
     * @return $this
     */
    private function encrypt_via_hash_md5(){
        $this->output = md5($this->input().$this->key());
        return $this;
    }

    /**
     * @return $this
     */
    private function encrypt_via_hash_sha1(){
        $this->output = sha1($this->input().$this->key());
        return $this;
    }

    /**
     * @return $this
     */
    private function encrypt_via_enc_simple_replace(){
        $replace_map = array(
            'a'     => '@',
            'A'     => '!@',
            'b'     => 'bi',
            'B'     => 'Bi',
            'c'     => '<',
            'C'     => '!<',
            'd'     => '>',
            'D'     => '!>',
            'e'     => 'é',
            'E'     => '€',
            'f'     => '|+',
            'F'     => '!+',
            'g'     => '@|',
            'G'     => '@_',
            'h'     => '|-',
            'H'     => '|-|',
            'i'     => ':',
            'I'     => '|',
            'j'     => '?',
            'J'     => '!?',
            'k'     => '|<',
            'K'     => '!<',
            'l'     => '|_',
            'L'     => '!_',
            'm'     => 'nn',
            'M'     => '/\\/\\',
            'n'     => '^',
            'N'     => '!^',
            'o'     => '0',
            'O'     => '!0',
            'p'     => '|@',
            'P'     => '!!@',
            'q'     => 'o|',
            'Q'     => 'O,',
            'r'     => '|/',
            'R'     => '|\\',
            's'     => '3',
            'S'     => '!3',
            't'     => '+',
            'T'     => '*',
            'u'     => '|_|',
            'U'     => '!_!',
            'v'     => 'x',
            'V'     => 'X',
            'y'     => '/|',
            'Y'     => '/!',
            'x'     => '#',
            'X'     => '!#',
            'z'     => '_/-',
            'Z'     => '!/-',
            '0'     => 'O',
            '1'     => 'l',
            '2'     => 'Z',
            '3'     => 'E',
            '4'     => 'A',
            '5'     => 'F',
            '6'     => 'G',
            '7'     => '/-',
            '8'     => '%',
            '9'     => 'q',
        );
        $output = '';
        $i = 0;
        for($i; $i < strlen($this->input); $i++){
            $key = $this->input[$i];
            if(isset($replace_map[$key])){
                $output .= $$replace_map[$key];
            }
            else{
                $output .= '&';
            }

        }
        $this->output = md5($output);
        return $this;
    }

    /**
     * @return $this
     */
    private function encrypt_via_enc_rot13_simple_replace(){
        $input = str_rot13($this->input);
        $replace_map = array(
            'a'     => '@',
            'A'     => '!@',
            'b'     => 'bi',
            'B'     => 'Bi',
            'c'     => '<',
            'C'     => '!<',
            'd'     => '>',
            'D'     => '!>',
            'e'     => 'é',
            'E'     => '€',
            'f'     => '|+',
            'F'     => '!+',
            'g'     => '@|',
            'G'     => '@_',
            'h'     => '|-',
            'H'     => '|-|',
            'i'     => ':',
            'I'     => '|',
            'j'     => '?',
            'J'     => '!?',
            'k'     => '|<',
            'K'     => '!<',
            'l'     => '|_',
            'L'     => '!_',
            'm'     => 'nn',
            'M'     => '/\\/\\',
            'n'     => '^',
            'N'     => '!^',
            'o'     => '0',
            'O'     => '!0',
            'p'     => '|@',
            'P'     => '!!@',
            'q'     => 'o|',
            'Q'     => 'O,',
            'r'     => '|/',
            'R'     => '|\\',
            's'     => '3',
            'S'     => '!3',
            't'     => '+',
            'T'     => '*',
            'u'     => '|_|',
            'U'     => '!_!',
            'v'     => 'x',
            'V'     => 'X',
            'y'     => '/|',
            'Y'     => '/!',
            'x'     => '#',
            'X'     => '!#',
            'z'     => '_/-',
            'Z'     => '!/-',
            '0'     => 'O',
            '1'     => 'l',
            '2'     => 'Z',
            '3'     => 'E',
            '4'     => 'A',
            '5'     => 'F',
            '6'     => 'G',
            '7'     => '/-',
            '8'     => '%',
            '9'     => 'q',
        );
        $output = '';
        $i = 0;
        for($i; $i < strlen($input); $i++){
            $key = $this->input[$i];
            if(isset($replace_map[$key])){
                $output .= $replace_map[$key];
            }
            else{
                $output .= '&';
            }

        }
        $this->output = md5($output);
        return $this;
    }
    /**
     * @return string
     */
    public function output(){
        /** trimmed because encrypt/decrypt add whitespaces */
        return trim($this->output);
    }
}