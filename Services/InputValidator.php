<?php
/**
 * Input Validator Class
 *
 * This class provides additional mechanism for user input & parameter validation.
 *
 * @vendor      BiberLtd
 * @package		Core
 * @subpackage	Services
 * @name	    InputValidator
 *
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.2.1
 * @date        10.08.2013
 *
 * @deprecated 	This class will be replaced soon. Please do NOT use this class.
 */

namespace BiberLtd\Bundle\CoreBundle\Services;
use BiberLtd\Bundle\CoreBundle\Core as Core;

class InputValidator extends Core{
    protected $input;			/** array('input1', 'input2', 'input3') */
    protected $rules;			/** array('rule_name' => 'value') */

    private $all_rules;

    /**
     * @name            __construct()
     *                  Constructor.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.2.0
     *
     * @param           string          $kernel
     */
    public function __construct($kernel){
        parent::__construct($kernel);
        $this->input = false;
        $this->rules = array();
        $this->timezone = $kernel->getContainer()->getParameter('app_timezone');
        $tmp_rules = get_class_methods(__CLASS__);
        $rules = array();
        foreach($tmp_rules as $rule){
            switch($rule){
                case '__construct':
                case '__destruct':
                case 'issue_error':
                case 'process_errors':
                case 'display_errors':
                case 'email_errors':
                case 'check_rules':
                case 'set_input':
                case 'set_rules':
                case 'add_rule':
                case 'add_rules':
                case 'reset_rules':
                case 'remove_rule':
                case 'remove_rules':
                case 'apply_rules()':
                    break;
                default:
                    $rules[] = $rule;
                    break;
            }
        }
        $this->all_rules = $rules;
    }
    /**
     * @name            __destruct()
     *                  Destructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.2.0
     *
     *
     */
    public function __destruct(){
        foreach($this as $key => $value) {
            $this->$key = null;
        }
    }
    /**
     * @name            check_rules()
     *                  Checks a given rule or a set of rules and if there is an error removes errorprone rules from validation
     *  				list. In the mean time it issues necessary errors.
     *
     * @author          Can Berkol
     *
     * @since           1.0.3
     * @version         1.2.0
     *
     * @return          array
     */
    private function check_rules(){
        $available_rules = get_class_methods(__CLASS__);
        foreach($this->rules as $key => $value){
            if(!in_array($key, $available_rules)){
//                /** START :: issue_error */
//                $date = new \DateTime('now', new \DateTimeZone($this->timezone));
//                $hint = '$this->rules contain undefined rule(s).';
//                $this->error[] = $this->issue_error(__CLASS__, __METHOD__, 'iv::invalid:rule::'.$key, $date, $hint);
//                /** END :: issue_error */
                unset($this->rules[$key]);
            }
        }
        return $this->rules;
    }
    /**
     * @name 			set_input()
     *  				Sets the input collection to be checked.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.2.0
     *
     * @param			mixed		$input 			A collection of variables.
     *
     * @return			mixed 		$this
     */
    public function set_input(array $input){
        $this->input = $input;
        return $this;
    }
    /**
     * @name 			set_rules()
     *  				Sets valid rules so that the rules can be applied to the input later. If there are no valid rules, then it registers
     *  				error message.
     * @author          Can Berkol
     *
     * @since			1.0.2
     * @version         1.2.0
     *
     * @param			mixed		$rules 			A collection of rules which will be applied to input.
     *
     * @return			mixed 		$this
     *
     * @example         $iv = new InputValidator();
     *                  $iv->set_rules(array('is_boolean|325'));
     */
    public function set_rules(array $rules){
        /**
         *  Clean up invalid rules.
         */
        $valid_rules = $this->check_rules($rules);
        if(count($valid_rules) < 1){
//            $date = new \DateTime('now', new \DateTimeZone($this->timezone));
//            $hint = 'You have defined no valid rules. Valid rules are: '.implode(', '.$this->all_rules);
//            $this->issue_error(__CLASS__, __METHOD__, 'iv::notfound::rules', $date, $hint);
            return false;
        }
        $this->rules = $valid_rules;
        return $this;
    }
    /**
     * @name 			add_rule()
     * @description		Adds a new rule into validation set.
     *
     * @author          Can Berkol
     *
     * @see InputValidator::add_rules()
     *
     * @since			1.0.0
     * @version         1.2.0
     *
     * @param			string		$rule_name
     *
     * @return          mixed       the return value of $this->add_rules()
     *
     * @example         $iv = new InputValidator();
     *                  $iv->add_rule('is_boolean|325');
     */
    public function add_rule($rule_name){
        $rules = array($rule_name);
        return $this->add_rules($rules);
    }
    /**
     * @name 			add_rules()
     *  				Adds a new set of rules into validation set.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.2.0
     *
     * @param			mixed		$rules 			Array of rule names.
     *
     * @return			mixed 		$this
     *
     * @example         $iv = new InputValidator();
     *                  $iv->add_rules(array('is_boolean|325','is_numeric|notnumeric'));
     */
    public function add_rules(array $rules){
        $this->rules = array_merge($this->rules, $this->check_rules($rules));
        return $this;
    }
    /**
     * @name 			reset_rules()
     *  				Resets and empties the validation rule set.
     *
     * @author          Can Berkol
     * @since			1.0.0
     * @version         1.2.0
     *
     * @return			mixed 		$this
     */
    public function reset_rules(){
        $this->rules = array();
        return $this;
    }
    /**
     * @name 			remove_rule()
     *  				Removes a given rule from the rules set.
     *
     * @author          Can Berkol
     * @since			1.0.0
     * @version         1.2.0
     *
     * @param			mixed		$rule_name 			Rule to remove.
     *
     * @return			mixed 		$this
     *
     * @example         $iv = new InputValidator();
     *                  $iv->remove_rule(is_boolean'));
     */
    public function remove_rule(string $rule_name){
        $rules = $this->rules;
        $updated_rules = array();
        foreach($rules as $rule){
            $rule_details = explode('|', $rule);
            $stored_rule_name = $rule_details[0];
            if($stored_rule_name != $rule_name){
                $updated_rules[] = $rule;
            }
        }
        return $this->set_rules($updated_rules);
    }
    /**
     * @name 			remove_rules()
     *  				Removes one or more rules from the rules set.
     *
     * @author          Can Berkol
     * @since			1.0.0
     * @version         1.2.0
     *
     * @param			mixed		$rules 			Array of rule names.
     *
     * @return			mixed 		$this
     *
     * @example         $iv = new InputValidator();
     *                  $iv->set_rules(array('is_boolean','is_email'));
     */
    public function remove_rules(array $rules){
        $old_rules = $this->rules;
        $updated_rules = array();
        foreach($rules as $rule){
            $rule_details = explode('|', $rule);
            $rule_name = $rule_details[0];
            if(!array_search($rule_name, $old_rules)){
                $updated_rules[] = $rule;
            }
        }
        return $this->set_rules($updated_rules);
    }
    /**
     * @name 			apply_rules()
     *  				Apply all the previously set rules to the provided input collection and returns false if any of the input
     * 					fails any of the rule sets.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.2.0
     *
     * @return			bool
     *
     * @example         $iv = new InputValidator();
     *                  $iv->set_rules(array('is_boolean|325|42'))->apply_rules();
     */
    public function apply_rules(){
        foreach($this->rules as $rule => $value){
            $flag_start = 1;
            $count = 0;
            $parameter_values = array();
            foreach($value as $parameter_value){
                if($flag_start <= $count){
                    $parameter_values[] = $parameter_value;
                }
                $count++;
            }
            if(count($parameter_values) == 0){
                if (!$this->$rule()){
                    return false;
                }
            }
            else if(count($parameter_values) == 1){
                if (!$this->$rule($parameter_values[0])){
                    return false;
                }
            }
            else if(count($parameter_values) == 2){
                if (!$this->$rule($parameter_values[0], $parameter_values[1])){
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * @name 			is_array()
     *  				Checks if given input element(s) are the type of array [optionally chechs if they match the size criteria]
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @param			integer		$size				optional	If size of array is important
     * @param			string		$criteria			optional	If size of array is important, ==, <, >, >=,<=
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $iv = new InputValidator();
     *                  $iv->set_input(array($check))->is_array(25, '>');       // returns true if $check is an array with more that 25 elements
     */
    public function is_array($size = -1, $criteria = '=='){
        foreach($this->input as $input){
            if(!is_array($input)){
                return false;
            }
            if($size != -1){
                switch($criteria){
                    case '==':
                        if(count($input) != $size){
                            return false;
                        }
                        break;
                    case '<':
                        if(count($input) >= $size){
                            return false;
                        }
                        break;
                    case '<=':
                        if(count($input) > $size){
                            return false;
                        }
                        break;
                    case '>':
                        if(count($input) <= $size){
                            return false;
                        }
                        break;
                    case '>=':
                        if(count($input) < $size){
                            return false;
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        return true;
    }
    /**
     * @name 			is_array_type()
     *  				Checks if given input element(s) is/are a collection given type
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @param			string		$type				optional	string,numeric,object,array,bool
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = array(123,421,2141);
     *                  $iv = new InputValidator();
     *                  $iv->set_input(array($check))->is_array_type('string');       // returns false
     */
    public function is_array_type($type = 'string'){
        $type = strtolower($type);
        foreach($this->input as $input){
            if(!$this->is_array()){
                return false;
            }
            switch($type){
                case 'string':
                    foreach($input as $item){
                        if(!is_string($item)){
                            return false;
                        }
                    }
                    break;
                case 'numeric':
                    foreach($input as $item){
                        if(!is_numeric($item)){
                            return false;
                        }
                    }
                    break;
                case 'object':
                    foreach($input as $item){
                        if(!is_object($item)){
                            return false;
                        }
                    }
                    break;
                case 'array':
                    foreach($input as $item){
                        if(!is_array($item)){
                            return false;
                        }
                    }
                    break;
                case 'bool':
                    foreach($input as $item){
                        if(!is_bool($item)){
                            return false;
                        }
                    }
                    break;
                default:
                    return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_object()
     *  				Checks if given input element(s) is/are objects optionally objects of a given class]
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.2.0
     *
     * @param			string		$class				optional	Name of class
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = new Generation();
     *                  $iv = new InputValidator();
     *                  $iv->set_input(array($check))->is_object();       // returns true
     *                  $iv->set_input(array($check))->is_object('Media');  // returns false
     */

    public function is_object($class = 'NOCLASS'){
        if(!is_string($class)){
//            $date = new \DateTime('now', new \DateTimeZone($this->timezone));
//            $hint = 'Class name must be a string value.';
//            $this->issue_error(__CLASS__, __METHOD__, 'iv::invalid::class', $date, $hint);
            return false;
        }
        if($class != 'NOCLASS'){
            $class = strtolower($class);
        }
        foreach($this->input as $input){
            if($class != 'NOCLASS'){
                if(!is_object($input) || strtolower(get_class($input)) != $class){
                    return false;
                }
            }
            else{
                if(!is_object($input)){
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * @name 			is_positive()
     *  				Checks if given input element(s) is/are positive numeric values
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = -33;
     *                  $iv = new InputValidator();
     *                  $iv->set_input(array($check))->is_positive();       // returns false
     */
    public function is_positive(){
        foreach($this->input as $input){
            if($input < 0){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_numeric()
     *  				Checks if given input element(s) is/are numeric values
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = '213';
     *                  $iv = new InputValidator();
     *                  $iv->set_input(array($check))->is_numeric();       // returns true
     */
    public function is_numeric(){
        foreach($this->input as $input){
            if(!is_numeric($input)){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_empty()
     *  				Checks if given input element(s) is/are empty values
     * @author          Can Berkol
     * @since			1.0.0
     * @version         1.2.1
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = '';
     *                  $iv = new InputValidator();
     *                  $iv->set_input(array($check))->is_empty();       // returns true
     */
    public function is_empty($glue = 'and'){
        $all_empty = true;
        $some_empty = false;
        foreach($this->input as $input){
            if(!empty($input)){
                if($glue == 'and'){
                    return false;
                }
                else{
                    $all_empty = false;
                }
            }
            else{
                $some_empty = true;
            }
        }
        if($all_empty){
            return true;
        }
        if($glue == 'or' && $some_empty){
            return true;
        }
        else{
            return false;
        }
    }
    /**
     * @name 			is_integer()
     *  				Checks if given input element(s) is/are integer values
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = 213.23;
     *                  $iv = new InputValidator();
     *                  $iv->set_input(array($check))->is_integer();       // returns false
     */
    public function is_integer(){
        foreach($this->input as $input){
            if(!filter_var($input, FILTER_VALIDATE_INT)){
                return false;
            }
        }
        return true;
    }

    /**
     * @name 			is_float()
     *  				Checks if given input element(s) is/are float values
     * @author          Can Berkol
     * @since			1.0.1
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = 213.45;
     *                  $iv = new InputValidator();
     *                  $iv->set_input(array($check))->is_float();       // returns true
     */
    public function is_float(){
        foreach($this->input as $input){
            if(!is_float($input)){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_text()
     *  				Checks if given input element(s) is/are text/string values
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = 'test';
     *                  $iv = new InputValidator();
     *                  $iv->set_input(array($check))->is_text();       // returns true
     */
    public function is_text(){
        foreach($this->input as $input){
            if(!is_string($input)){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_string()
     * @see             is_text()
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = '213';
     *                  $iv = new InputValidator();
     *                  $iv->set_input(array($check))->is_string();       // returns true
     */
    public function is_string(){
        return $this->is_text();
    }
    /**
     * @name 			is_negative()
     *  				Checks if given input element(s) is/are negative numeric values
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = 14;
     *                  $iv = new InputValidator();
     *                  $iv->set_input(array($check))->is_negative();       // returns false
     */
    public function is_negative(){
        foreach($this->input as $input){
            if($input > 0){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_equalto()
     *  				Checks if given input element(s) is equal to given value
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @param			string		$valuetocompare
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = '213';
     *                  $iv = new InputValidator();
     *                  $iv->set_input(array($check))->is_equalto(213);       // returns true
     */
    public function is_equalto($valuetocompare){
        foreach($this->input as $input){
            if($input != $valuetocompare){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_not_equalto()
     *  				Checks if given input element(s) is not equal to given value.
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @param			string		$valuetocompare
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = 211';
     *                  $iv = new InputValidator();
     *                  $iv->set_input(array($check))->is_notequalto(21);       // returns true
     */
    public function is_not_equalto($valuetocompare){
        if(!$this->is_equalto($valuetocompare)){
            return true;
        }
        return false;
    }
    /**
     * @name 			is_identicalto()
     *  				Checks if given input element(s) is identical to given value.
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @param			mixed		$valuetocompare
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = '213';
     *                  $iv = new InputValidator();
     *                  $iv->set_input(array($check))->is_identicalto(213);       // returns false
     */
    public function is_identicalto($valuetocompare){
        foreach($this->input as $input){
            if($input === $valuetocompare){
                return true;
            }
        }
        return false;
    }
    /**
     * @name 			is_not_identicalto()
     *  				Checks if given input element(s) is not identical to given value.
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @param			mixed		$valuetocompare
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = '213';
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_notidenticalto(213);       // returns true
     */
    public function is_not_identicalto($valuetocompare){
        if(!$this->is_identicalto($valuetocompare)){
            return true;
        }
        return false;
    }
    /**
     * @name 			is_incollection()
     *  				Checks if given input element(s) has the given value in its collection.
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @param			string		$valutocompare
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = array('apple', 'juice');
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_incollection('pie');       // returns false
     */
    public function is_incollection($valutocompare){
        foreach($this->input as $input){
            if(!is_array($input)){
                return false;
            }
            if(!in_array($valutocompare, $input)){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_ingivencollection()
     *  				Checks if given input element(s) is included in given collection.
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @version         1.1.3
     *
     * @param			mixed		$collection
     *
     * @return			bool		FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = array('apple');
     *                  $fruits = array('apple', 'cherry');
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_incollection($fruits);       // returns true
     */
    public function is_ingivencollection($collection){
        foreach($this->input as $input){
            if(!in_array($input, $collection)){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_date()
     * 					#note# Parameter must either an integer - a unix time stamp - or a yyyy-mm-dd formatted
     * 					date. otherwise the function will always return false...
     *  				Checks if given input element(s) is/are valid calendar dates.
     *
     * @author          Can Berkol
     *
     * @uses            issue_eerror()
     *
     * @since			1.0.0
     * @version         1.2.0
     *
     * @param 			string 		$format 			valid php date formatting string
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = time();
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_date();       // returns true
     */
    public function is_date($format = 'Y-m-d'){
        foreach($this->input as $input){
            if(is_numeric($input)){
                $timestamp = $input;
                $date = date('Y-m-d', $timestamp);
                $exploded_value = explode('-', $date);
            }
            else{
                $timestamp = strtotime(date($format));
                $date = date('Y-m-d', $timestamp);
                $exploded_value = explode('-', $input);
            }

            if(!is_array($exploded_value)){
//                $date = new \DateTime('now', new \DateTimeZone($this->timezone));
//                $hint = 'It looks like date() function does not work properly.';
//                $this->issue_error(__CLASS__, __METHOD__, 'iv::invalid::exploded_value', $date, $hint);
                return false;
            }
            $year = $exploded_value[0];
            $month = $exploded_value[1];
            $day = $exploded_value[2];
            if(!checkdate($month,$day,$year)){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_email()
     *  				Checks if given input element(s) is/are valid e-mail addresses.
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @version        1.0.1
     * @changelog      1.0.1 - removed egrepi function and changed it with preg_match()
     *
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = 'info@biberltd.com';
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_email();       // returns false
     */
    public function is_email(){
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
        foreach($this->input as $input){
            if (preg_match($regex, $input) < 1){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_color()
     *  				Checks if given input is a alid vcolor string/code.
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @version        1.1.2
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         #fffff
     */
    public function is_color(){
        $regex = '/#[a-z0-9]{6}#i/';
        foreach($this->input as $input){
            $clean_input = str_replace('#', '', $this->input);
            if (!preg_match($regex, $clean_input)){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_alphanum()
     *  				Checks if given input element(s) is/are valid alpha numeric characters.
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = 'apple';
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_alphanum();       // returns true
     */
    public function is_alphanum(){
        foreach($this->input as $input){
            if(preg_match("/[\w\s.]/i", $input) < 1){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_boolean()
     *  				Checks if given input element(s) is/are boolean
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = 'apple';
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_boolean('pie');       // returns false
     */
    public function is_boolean(){
        foreach($this->input as $input){
            if(!is_bool($input)){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_coordinate()
     *  				Checks if given input element(s) is/are valid geographical coordinates.
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = 'apple';
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_coordinate();       // returns false
     */
    public function is_coordinate(){
        foreach($this->input as $input){
            if(preg_match('[-]?[0-9]{0,1}[0-9]{0,4}', $input) < 0){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_greaterthan()
     *  				Checks if given input element(s) is/are greater than values to be compared.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.2.0
     *
     * @param			integer		$valuetocompare
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = 33;
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_greaterhan(45);       // returns false
     */
    public function is_greaterthan($valuetocompare){
        foreach($this->input as $input){
            if(!is_numeric($input) || !is_numeric($valuetocompare)){
//                $date = new \DateTime('now', new \DateTimeZone($this->timezone));
//                $hint ='Either $input ('.$input.') or $valuetocompare ('.$valuetocompare.') is not numeric.';
//                $this->issue_error(__CLASS__, __METHOD__, 'iv::invalid::valuetocompare', $date, $hint);
                return false;
            }
            if ($input < $valuetocompare){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_lessthan()
     *  				Checks if given input element(s) is/are less than values to be compared.
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @param			integer		$valuetocompare
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = 25;
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_lessthan(44);       // returns true
     */

    public function is_lessthan($valuetocompare){
        if(!$this->is_greaterthan($valuetocompare)){
            return true;
        }
        return false;
    }
    /**
     * @name 			is_longerthan()
     *  				Checks if given input element(s) is/are longer than values to be compared.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.0.4
     *
     * @param			integer		$valuetocompare
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = 'long';
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_longerthan('longer');       // returns false
     */
    public function is_longerthan($valuetocompare){
        foreach($this->input as $input){
            if(!is_string($input) || !is_string($valuetocompare)){
//                $date = new \DateTime('now', new \DateTimeZone($this->timezone));
//                $hint ='Either $input ('.$input.') or $valuetocompare ('.$valuetocompare.') is not numeric.';
//                $this->issue_error(__CLASS__, __METHOD__, 'iv::invalid::valuetocompare', $date, $hint);
            }
            if (strlen($input) < strlen($valuetocompare)){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_null()
     *  				Checks if given input element(s) is/are null.
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @return			bool							FALSE if not NULL, true otherwise.
     *
     * @example         $check = '';
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_null();       // returns false
     */
    public function is_null(){
        foreach($this->input as $input){
            if(!is_null($input)){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_shorterthan()
     *  				Checks if given input element(s) is/are shorter than the length of value to be compared.
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @param			string		$valuetocompare
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = array('long');
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_shorterthan('longer');       // returns true
     */
    public function is_shorterthan($valuetocompare){
        if(!$this->is_longerthan($valuetocompare)){
            return true;
        }
        return false;
    }
    /**
     * @name 			is_equalto_charlength()
     *  				Checks if given input element(s) is/are equal to given character length.
     * @author          Can Berkol
     * @since			1.0.0
     * @version         1.0.4
     * @param 			integer			$length			Length that string is expected to be.
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = 'en';
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_equalto_charlength(2);       // returns true
     */
    public function is_equalto_charlength($length){
        if(!is_integer($length)){
            $this->error_log[] = array('class'    => get_class($this),
                                       'method'   => 'is_equalto_charlength()',
                                       'error'    => ExI1x004,
                                       'hint'     => 'Either $length ('.$length.') is not an integer.',
                                       'time'     => date('Y-m-d H:i'),
                                       'ip'       => $_SERVER['REMOTE_ADDR'],
                                       'exception' => ''
            );
            return false;
        }
        foreach($this->input as $input){
            if(strlen($input) != $length){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_shorterthan_charlength()
     *  				Checks if given input element(s) is/are shorter than given character length.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.2.0
     *
     * @param 			integer			$length			Length that string is eexpected to be less than.
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = 'en';
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_equalto_charlength(2);       // returns false
     */
    public function is_shorterthan_charlength($length){
        if(!is_integer($length)){
//            $date = new \DateTime('now', new \DateTimeZone($this->timezone));
//            $hint = 'Either $length ('.$length.') is not an integer.';
//            $this->issue_error(__CLASS__, __METHOD__, 'iv::invalid::length', $date, $hint);
            return false;
        }
        foreach($this->input as $input){
            if(strlen($input) >= $length){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			islongerthan_charlength()
     *  				Checks if given input element(s) is/are longer than given character length.
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @param 			integer			$length			Length that string is expected to be.
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = 'en';
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_equalto_charlength(2);       // returns false
     */
    public function is_longerthan_charlength($length){
        if(!$this->is_shorterthan_charlength($length)){
            return true;
        }
        return false;
    }
    /**
     * @name 			does_file_exist()
     *  				Checks if given input element(s) is/are does exist in given location.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.2.0
     *
     * @return			bool							FALSE if not exists, TRUE otherwise.
     *
     * @example         $check = 'file.jpg';
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->does_file_exist();       // returns true if file does exist
     */
    public function does_file_exist(){
        foreach($this->input as $input){
            if(!is_string($input)){
//                $date = new \DateTime('now', new \DateTimeZone($this->timezone));
//                $hint = '$input ('.$input.') must be a valid path as string.';
//                $this->issue_error(__CLASS__, __METHOD__, 'iv::invalid::length', $date, $hint);
//                return false;
            }
            if(!file_exists($input)){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_url()
     *  				Checks if given input element(s) is/are valid URLs.
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @return			bool							FALSE if paremeter is not an URL TRUE otherwise.
     *
     * @example         $check = 'www.biberltd.com';
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_url();       // returns true
     */
    public function is_url(){
        foreach($this->input as $input){
            $pattern = "#^(http:\/\/|https:\/\/|www\.)(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?(\/)*$#i";
            if(!preg_match($pattern, $input) && $input != '/' && $input != 'http://localhost/'){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_ip()
     *  				Checks if given input element(s) is/are valid IP addresses.
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = 'not an ip';
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_ip(2);       // returns false
     */
    public function is_ip(){
        foreach($this->input as $input){
            if(!filter_var($input, FILTER_VALIDATE_IP)){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_version()
     *  				Checks if given input element(s) does have valid version number attached to them.
     *  				The format we are looking at is 1.0.0 where middle and last digits cannot be larger than 9
     * @author          Can Berkol
     * @since			1.0.0
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = '1.0.0';
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_version();       // returns true
     */
    public function is_version(){
        foreach($this->input as $input){
            $input_parts = explode('.', $input);
            if(count($input_parts) != 3){
                return false;
            }
            if($input_parts[0] < 0 || $input_parts[1] < 0 || $input_parts[2] < 0){
                return false;
            }
            if($input_parts[1] > 9 || $input_parts[2] > 9){
                return false;
            }
        }
        return true;
    }
    /**
     * @name 			is_valid_cc()
     *  				Checks if the provided input is a valit credit card number. if type is given then the number is
     *                  validated for that card type only.
     * @author          Can Berkol
     * @since			1.1.0
     *
     * @param           string          $type           visa, mastercard, amex, discover, dinersclub
     *
     * @return			bool							FALSE if not succeeded, TRUE otherwise.
     *
     * @example         $check = '23423523523533';
     *                  $iv = new Input_validator();
     *                  $iv->set_input(array($check))->is_valid_cc('visa');       // returns false
     */
    public function is_valid_cc($type = null){
        $card_types = array(
            "/^4\d{12}(\d\d\d){0,1}$/" => "visa",
            "/^5[12345]\d{14}$/"       => "mastercard",
            "/^3[47]\d{13}$/"          => "amex",
            "/^6011\d{12}$/"           => "discover",
            "/^30[012345]\d{11}$/"     => "dinersclub",
            "/^3[68]\d{12}$/"          => "dinersclub",
            "/^6[0-9].{14}$/"          => "maestro",
            "/^5[0,6-8].{14}$/"        => "maestro"
        );
        $detected_type = null;
        foreach($card_types as $regex => $type){
            foreach($this->input as $input){
                if (preg_match($regex, $input)) {
                    $detected_type = $type;
                    break;
                }
            }
        }
        /** if there is no type detected then this is not a valid cc number */
        if(is_null($detected_type)){
            return false;
        }
        /** if type is set but not equal to detected type then this is not a valid cc number */
        if(!is_null($type)){
            if($type != $detected_type){
                return false;
            }
        }
        return true;
    }
    /**
     * @name           is_iban()
     *                 Checks if given string is a valid IBAN code / format.
     * @author         Can Berkol
     * @since          1.1.3
     *
     * @return		   bool							FALSE if not succeeded, TRUE otherwise.
     *
     */
    public function is_iban(){
        $regex = '/[a-zA-Z]{2}[0-9]{2}[a-zA-Z0-9]{4}[0-9]{7}([a-zA-Z0-9]?){0,16}/';
        foreach($this->input as $input){
            if (!preg_match($regex, $input)){
                return false;
            }
        }
        return true;
    }
    /**
     * @name           is_bic()
     *                 Checks if given string is a valid BIC (switft) code / format.
     * @author         Can Berkol
     * @since          1.1.3
     *
     * @return		   bool							FALSE if not succeeded, TRUE otherwise.
     *
     */
    public function is_bic(){
        $regex = '/([a-zA-Z]{4}[a-zA-Z]{2}[a-zA-Z0-9]{2}([a-zA-Z0-9]{3})?)/';
        foreach($this->input as $input){
            if (!preg_match($regex, $input)){
                return false;
            }
        }
        return true;
    }
    /**
     * @name           is_zip_code()
     *                 Checks if given string is a ZIP number / POSTAL CODE.
     *
     * @author         Can Berkol
     * @since          1.1.3
     * @version        1.2.0
     *
     * @param          string                       $region                      usa, canada
     *
     * @return		   bool							FALSE if not succeeded, TRUE otherwise.
     *
     */
    public function is_zip_code($region = 'usa'){
        $regex = array(
            'canada'        => '/^[ABCEGHJ-NPRSTVXY]{1}[0-9]{1}[ABCEGHJ-NPRSTV-Z]{1}[ ]?[0-9]{1}[ABCEGHJ-NPRSTV-Z]{1}[0-9]{1}$/',
            'usa'           => '/^[0-9]{5}(-[0-9]{4})?$/');
        if(!isset($regex[$region])){
            return false;
        }
        foreach($this->input as $input){
            if (!preg_match($regex[$region], $input)){
                return false;
            }
        }
        return true;
    }

}
/**
 * Change Log
 * **************************************
 * v1.2.1                      Can Berkol
 * 10.08.2013
 * **************************************
 * U is_empty($glue = 'and')
 *
 * **************************************
 * v1.2.0                      Can Berkol
 * 27.05.2013
 * **************************************
 * A is_zip_code()
 * D is_language_code()
 * D is_zip()
 * M Adopted to Symfony 2.
 *
 * **************************************
 * v1.1.3                      Can Berkol
 * 24.09.2012
 * **************************************
 * A is_iban()
 * A is_bic()
 * A is_zip()
 *
 * **************************************
 * v1.1.2                      Can Berkol
 * 21.08.2012
 * **************************************
 * A is_color()
 *
 * **************************************
 * v1.1.1                      Can Berkol
 * 14.08.2012
 * **************************************
 * A BBR prefix.
 *
 * **************************************
 * v1.1.0                      Can Berkol
 * 10.08.2012
 * **************************************
 * A is_valid_cc()
 *
 * **************************************
 * v1.0.4                      Can Berkol
 * 10.08.2012
 * **************************************
 * U Error mechanism updated.
 *
 * **************************************
 * v1.0.2                      Can Berkol
 * **************************************
 * A is_boolean()
 * D is_db_id()
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * **************************************
 * A is_float()
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * **************************************
 * Main release
 *
 */