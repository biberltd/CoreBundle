<?php
/**
 * Encryption Class
 *
 * This class provides encryption related mechanisms.
 *
 * @vendor      BiberLtd
 * @package		Core
 * @subpackage	Services
 * @name	    Encryption
 *
 * @author		Can Berkol
 * @author		Said İmamoğlu
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.2.3
 * @date        07.06.2014
 *
 * @todo        Bind with exception class. See comment outed error log mechanisms.
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
     * @name            __construct()
     *                  Constructor.
     *
     * @author          Can Berkol
     *
     * @see             Core::issue_error()
     *
     * @since           1.0.1
     * @version         1.2.0
     *
     * @param           string      $timezone
     * @param           array       $params         'key', 'input', 'output'
     */
    public function __construct($timezone = 'Europe/Istanbul', $params = array()){
        $this->timezone = $timezone;
        /** Define required parameters to initialize object */
        $rqrd_params = array('key', 'input');
        if(count($params) > 0){
            foreach($rqrd_params as $key){
                if(!isset($params[$key])){
//                    /** START :: issue_error */
//                    $date = new \DateTime('now', new \DateTimeZone($this->timezone));
//                    $hint = 'Key, and input must be provided to initialize Encryption object.';
//                    $this->error[] = $this->issue_error(__CLASS__, __METHOD__, 'iv::missing:params::'.$key, $date, $hint);
//                    /** END :: issue_error */
                }
            }
            /**
             * Set only allowed parameters
             */
            foreach($params as $key => $value){
                switch($key){
                    case 'key':
                    case 'input':
                        $this->$key = $value;
                        break;
                    default:
//                        /** START :: issue_error */
//                        $date = new \DateTime('now', new \DateTimeZone($this->timezone));
//                        $hint = 'Only key and input can be initialized.';
//                        $this->error[] = $this->issue_error(__CLASS__, __METHOD__, 'iv::missing:params::'.$key, $date, $hint);
//                        /** END :: issue_error */
                        break;
                }
            }
        }
        else{
            /**
             * set default value
             */
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
     * @name            __destruct()
     *                  Destructor.
     *
     * @author          Can Berkol
     *
     * @see             Core::issue_error()
     *
     * @since           1.0.0
     * @version         1.2.0
     *
     *
     */
    public function __destruct(){
        /**
         * First process errors and then destroy
         */
        foreach($this as $key => $element) {
            $this->$key = null;
        }
    }
    /**
     * @name 			set_key()
     *  				Sets the value of the key property.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.2.0
     *
     * @param 			string 	        $key
     *
     * @return			mixed           $this, false
     */
    private function set_key($key){
        if(!is_string($key)){
//            /** START :: issue_error */
//            $date = new \DateTime('now', new \DateTimeZone($this->timezone));
//            $hint = 'Key must be a string value.';
//            $this->error[] = $this->issue_error(__CLASS__, __METHOD__, 'iv::invalid::key::', $date, $hint);
//            /** END :: issue_error */
            return false;
        }
        $this->key = md5($key);
        return $this;
    }
    /**
     * @name 			get_key()
     *  				Gets the value of the key property.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.2.0
     *
     * @return			string          $this->key
     */
    private function get_key(){
        return $this->key;
    }
    /**
     * @name 			key()
     *  				Accepts 0 or 1 arguments. If there is argument provided, the function returns the value of the
     *                  key property. If there is one argument provided, the function sets the value of the key property.
     *
     * @author          Can Berkol
     *
     * @since			1.2.0
     * @version         1.2.0
     *
     * @return			mixed          $this, $this->key, false
     */
    public function key(){
        /** Get arguments passed */
        $count_args = func_num_args();
        $args = func_get_args();

        switch($count_args){
            case 0:
                return $this->get_key();
            case 1:
                return $this->set_key($args[0]);
            default:
//                /** START :: issue_error */
//                $date = new \DateTime('now', new \DateTimeZone($this->timezone));
//                $hint = 'Method accepts only 0 or 1 argument(s).';
//                $this->error[] = $this->issue_error(__CLASS__, __METHOD__, 'iv::invalid::count::'.$count_args, $date, $hint);
//                /** END :: issue_error */
                break;
        }
    }
    /**
     * @name 			set_input()
     *  				Sets the value of the input property.
     *
     * @author          Can Berkol
     * @author		    Said İmamoğlu
     *
     * @since			1.0.0
     * @version         1.2.2
     *
     * @param 			string 	        $input
     *
     * @return			mixed           $this, false
     */
    private function set_input($input){
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
                return $this->set_input($args[0]);
            default:
//                /** START :: issue_error */
//                $date = new \DateTime('now', new \DateTimeZone($this->timezone));
//                $hint = 'Method accepts only 0 or 1 argument(s).';
//                $this->error[] = $this->issue_error(__CLASS__, __METHOD__, 'iv::invalid::count::'.$count_args, $date, $hint);
//                /** END :: issue_error */
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
//                /** START :: issue_error */
//                $date = new \DateTime('now', new \DateTimeZone($this->timezone));
//                $hint = 'Acceptable algorithms are the following: '.implode(',', $this->algorithms);
//                $this->error[] = $this->issue_error(__CLASS__, __METHOD__, 'iv::invalid::algorithm::'.$algorithm, $date, $hint);
//                /** END :: issue_error */
                return false;
        }
        return $this->$func();
    }
    /**
     * @name 			decrypt()
     *  				If supported, decrypts the given input based on the provided algorithm.
     *
     * @author          Can Berkol
     *
     * @since			1.2.0
     * @version         1.2.0
     *
     * @param           string          $algorithm          Selected encryption algorithm.
     *
     * @return			mixed           $this, false
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
//                /** START :: issue_error */
//                $date = new \DateTime('now', new \DateTimeZone($this->timezone));
//                $hint = 'You have provided an invalid algorithm or an algorithm that cannot be decryptable. Acceptable algorithms are the following: '.implode(',', $this->algorithms);
//                $this->error[] = $this->issue_error(__CLASS__, __METHOD__, 'iv::invalid::algorithm::'.$algorithm, $date, $hint);
//                /** END :: issue_error */
                return false;
        }
        return $this->$func();
    }
    /**
     * @name 			encrypt_via_enc_reversible_pkey()
     *  				Encrypts the given via enc_reversible_pkey algorithm.
     *
     * @author          Can Berkol
     *
     * @since			1.2.0
     * @version         1.2.0
     *
     * @return			mixed           $this, false
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
     * @name 			decrypt_via_enc_reversible_pkey()
     *  				Decrypts the given via enc_reversible_pkey algorithm.
     *
     * @author          Can Berkol
     *
     * @since			1.2.0
     * @version         1.2.0
     *
     * @return			mixed           $this, false
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
     * @name 			encrypt_via_hash_md5()
     *  				Encrypts the given via hash_md5 algorithm.
     *
     * @author          Can Berkol
     *
     * @since			1.2.0
     * @version         1.2.0
     *
     * @return			object           $this
     */
    private function encrypt_via_hash_md5(){
        $this->output = md5($this->input().$this->key());
        return $this;
    }
    /**
     * @name 			encrypt_via_hash_sha1()
     *  				Encrypts the given via hash_sha1 algorithm.
     *
     * @author          Can Berkol
     *
     * @since			1.2.0
     * @version         1.2.0
     *
     * @return			mixed           $this, false
     */
    private function encrypt_via_hash_sha1(){
        $this->output = sha1($this->input().$this->key());
        return $this;
    }
    /**
     * @name 			encrypt_via_enc_simple_replace()
     *  				Encrypts the given via enc_simple_replace algorithm.
     *
     * @author          Can Berkol
     *
     * @since			1.2.0
     * @version         1.2.0
     *
     * @return			mixed           $this, false
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
     * @name 			encrypt_via_enc_shift_simple_replace()
     *  				Encrypts the given via enc_rot13_simple_replace algorithm.
     *
     * @author          Can Berkol
     *
     * @since			1.2.0
     * @version         1.2.3
     *
     * @return			mixed           $this, false
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
     * @name 			output()
     *  				Outputs the encrypted/decrypted input.
     *
     * @author          Can Berkol
     *
     * @since			1.2.0
     * @version         1.2.0
     *
     * @return			string          $this->output
     */
    public function output(){
        /** trimmed because encrypt/decrypt add whitespaces */
        return trim($this->output);
    }
}
/**
 * Change Log
 * **************************************
 * v1.2.2                      07.06.2015
 * Can Berkol
 * **************************************
 * BF :: Missing key enc_rot13_simple_replace added to encrypt() method.
 * BF :: Extra $ sign removed from the beginning of variable.
 *
 * **************************************
 * v1.2.2                      Said İmamoğlu
 * 30.04.2014
 * **************************************
 * U set_input()
 *
 * **************************************
 * v1.2.1                      Can Berkol
 * 12.01.2014
 * **************************************
 * A decrypt_via_enc_reversible_pkey()
 *
 * **************************************
 * v1.2.0                      Can Berkol
 * 27.05.2013
 * **************************************
 * A decrypt_via_enc_reversible_pkey()
 * A encrypt_via_enc_reversible_pkey()
 * A get_input().
 * A input().
 * A key().
 * A output().
 * D get_output().
 * M Reworked to match Symfony2
 * U decrypt() supports multiple algorithms.
 * U encrypt() supports multiple algorithms.
 * U get_key() is now private.
 * U set_input() is now private.
 * U set_key() is now private.
 *
 * **************************************
 * v1.1.2                      Can Berkol
 * 14.08.2012
 * **************************************
 * A BBR prefix.
 *
 * **************************************
 * v1.1.2                      Can Berkol
 * 10.08.2012
 * **************************************
 * U Error mechanism updated.
 *
 * **************************************
 * v1.1.1                      Can Berkol
 * 09.08.2012
 * **************************************
 * U Extends BBR_Library
 *
 * **************************************
 * v1.1.0                      Can Berkol
 * **************************************
 * B Encrypt/decrypt whitespace problem solved
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * **************************************
 * - Main release
 *
 */