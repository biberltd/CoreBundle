<?php
/**
 * Access Validator Class
 *
 * This class provides mechanism to validate user access based on groups an access management rights
 * stored in database.
 *
 * @vendor      BiberLtd
 * @package		Core
 * @subpackage	Services
 * @name	    AccessValidator
 *
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.5
 * @date        05.06.2014
 *
 */

namespace BiberLtd\Bundle\CoreBundle\Services;
use BiberLtd\Bundle\CoreBundle\Core as Core;

class AccessValidator extends Core{

    private $session;

    /**
     * @name            __construct()
     *                  Constructor.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.0.2
     *
     * @param           object          $kernel
     *
     * @param           string          $timezone
     */
    public function __construct($kernel){
        parent::__construct($kernel);
        $this->session = $kernel->getContainer()->get('session');

        /** ************************************** */
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
     * @name            isActionGranted()
     *                  Checks if a particular action is granted for current session's member.
     *
     * @author          Can Berkol
     *
     * @since           1.0.4
     * @version         1.0.4
     *
     * @param           string          $actionCode
     * @param           array           $memberData
     *
     * @return          bool
     *
     */
    public function isActionGranted($actionCode, $memberData = null){
        $data = $memberData;
        if(is_null($data)){
            $data = $this->decryptSessionData();
        }
        if(!isset($data['granted_actions'])){
            $data['granted_actions'] = array();
        }
        if(in_array($actionCode, $data['granted_actions'])){
            return true;
        }
        return false;
    }
    /**
     * @name            isActionRevoked()
     *                  Checks if a particular action is revoked for current session's member.
     *
     * @author          Can Berkol
     *
     * @since           1.0.5
     * @version         1.0.5
     *
     * @param           string          $actionCode
     * @param           array           $memberData
     *
     * @return          bool
     *
     */
    public function isActionRevoked($actionCode, $memberData = null){
        $data = $memberData;
        if(is_null($data)){
            $data = $this->decryptSessionData();
        }
        if(!isset($data['revoked_actions'])){
            $data['revoked_actions'] = array();
        }
        if(in_array($actionCode, $data['revoked_actions'])){
            return true;
        }
        return false;
    }

    /**
     * @name            isGuest()
     *                  Checks if a given member is guest.
     *
     * @author          Can Berkol
     * @since           1.0.4
     * @version         1.0.4
     *
     * @param           array           $member_data     if not provided; will be read from session.
     *
     * @return          bool
     */
    public function isGuest($member_data = null){
        if($this->session->get('is_logged_in')){
            return false;
        }
        $data = $member_data;
        if(is_null($member_data)){
            $data = $this->decryptSessionData();
        }
        if(!isset($data['username']) || $data['username'] == 'guest'){
            return true;
        }
        return false;
    }
    /**
     * @name            is_guest()
     *                  Checks if a given member is guest.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.0.4
     *
     * @param           array           $member_data     if not provided; will be read from session.
     *
     * @return          bool
     * @deprecated      Will be removed in v1.2.0. Use $this->isGuest() instead.
     */
    public function is_guest($member_data = null){
       return $this->isGuest($member_data);
    }
    /**
     * @name            isAuthenticated()
     *                  Checks if a given member is guest.
     *
     * @author          Can Berkol
     *
     * @since           1.0.4
     * @version         1.0.4
     *
     * @param           array           $member_data     if not provided; will be read from session.
     *
     * @return          bool
     */
    public function isAuthenticated($member_data = null){
        $data = $member_data;
        if(is_null($member_data)){
            $data = $this->decryptSessionData();
        }
        if($this->session->get('authentication_data') != false){
            if(isset($data['username']) && $data['username'] != 'guest'){
                return true;
            }
        }
        return false;
    }
    /**
     * @name            is_authenticated()
     *                  Checks if a given member is guest.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.4
     *
     * @param           array           $member_data     if not provided; will be read from session.
     *
     * @deprecated      Will be removed in v1.2.0
     *
     * @return          bool
     */
    public function is_authenticated($member_data = null){
        return $this->isAuthenticated($member_data);
    }
    /**
     * @name            hasAccess()
     *                  Checks if a given member is guest.
     *
     * @author          Can Berkol
     *
     * @since           1.0.4
     * @version         1.0.4
     *
     * @param           array           $member_data        if not provided; will be read from session.
     * @param           array           $access_map         access map to process
     * @debug           bool            $debug              if set to true debug messages will be outputted.
     *
     * @return          bool
     *
     * ACCESS MAP
     *      'unmanaged' => true | false
     *      'quest'  => true | false
     *      'authenticated' => true | false
     *      'members' => member ids
     *      'groups' => group codes
     *      'status' => a | i | b (a=> active, i=> inactive, b=> banned
     *
     * @todo access control sıralamasının üstünden geç
     */
    public function hasAccess($member_data = null, $access_map = array(), $debug = false){
        $data = $member_data;
        if(is_null($member_data)){
            $data = $this->decryptSessionData();
        }
        if(!isset($data['sites'])){
            $data['sites'] = array(1);
        }
        $is_guest = $this->isGuest($data);
        $is_authenticated = $this->isAuthenticated($data);
        /**
         * 0. If unmanaged
         */
        if($access_map['unmanaged']){
            if($debug){
                echo 'This controller is unmanaged and everyone can access it.';
            }
            return true;
        }
        /**
         * 1. If Guest
         */
        if($is_guest && $access_map['guest']){
            if($debug){
                echo 'This controller is only for guests. If you are already logged-in, you should\'t see this message.';
            }
            return true;
        }
        if(!$is_guest && $access_map['guest']){
            if($debug){
                echo 'This controller is only for guests but you are already logged-in.';
            }
            return false;
        }
        /**
         * 2. If authenticated
         */
        if($is_authenticated && $access_map['authenticated']){
            /**
             * Correct site?
             */
            if(!in_array($this->kernel->getContainer()->getParameter('site_id'), $data['sites'])){
                if($debug){
                    echo 'This controller is only for authenticated users that belong the current site.';
                }
                return false;
            }
            /**
             * Correct status?
             */
            if(isset($access_map['status']) && count($access_map['status']) > 0){
                if(!isset($data['status'])){
                    $data['status'] = 'a';
                }
                if(!in_array($data['status'], $access_map['status'])){
                    if($debug){
                        echo 'This controller is only for authenticated users with specific account status.';
                    }
                    return false;
                }
            }
            /**
             * Correct group or member?
             */
            $member_groups = $data['groups'];
            $has_group_access = false;
            foreach($member_groups as $group){
                if(in_array($group, $access_map['groups'])){
                    $has_group_access = true;
                    break;
                }
            }
            if(count($access_map['groups']) == 0 && count($access_map['members']) == 0){
                $has_group_access = true;
            }
            $has_member_access = false;
            $member = $data['id'];
            if(in_array($member, $access_map['members'])){
                $has_member_access = true;
            }
            if($has_member_access || $has_group_access){
                if($debug){
                    echo 'This controller is only for specific authenticated members / member groups and you are one of them.';
                }
                return true;
            }
            if($debug){
                echo 'This controller is only for specific authenticated members / member groups.';
            }
            return false;
        }
        if($is_authenticated && !$access_map['authenticated']){
            if($debug){
                echo 'This controller is only for authenticated members but you are not authenticated.';
            }
            return false;
        }
    }
    /**
     * @name            has_access()
     *                  Checks if a given member is guest.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.4
     *
     * @param           array           $member_data        if not provided; will be read from session.
     * @param           array           $access_map         access map to process
     * @debug           bool            $debug              if set to true debug messages will be outputted.
     *
     * @return          bool
     *
     * @deprecated      Will be removed in v1.2.0. use $this->hasAccess() instead.
     */
    public function has_access($member_data = null, $access_map = array(), $debug = false){
        return $this->hasAccess($member_data, $access_map, $debug);
    }
    /**
     * @name            decryptSessionData()
     *                  Decrypts the session data.
     *
     * @author          Can Berkol
     *
     * @since           1.0.4
     * @version         1.0.4
     *
     * @return          array       $data
     */
    private function decryptSessionData(){
        $session_data = $this->kernel->getContainer()->get('session')->get('authentication_data');
        if($session_data){
            $enc = $this->kernel->getContainer()->get('encryption');
            $data = $enc->input($session_data)->key($this->kernel->getContainer()->getParameter('app_key'))->decrypt('enc_reversible_pkey')->output();
            $data = unserialize(base64_decode($data));
        }
        else{
            $data = array(
                'id'            => null,
                'username'      => 'guest',
                'locale'        => $this->kernel->getContainer()->getParameter('locale'),
                'email'         => null,
                'full_name'     => null,
                'name_first'    => null,
                'name_last'     => null,
                'status'        => 'a',
                'date_birth'    => null,
                'site'          => null,
                'groups'        => array(),
                'session_id'    => $this->kernel->getContainer()->get('session')->getId(),
            );
        }
        return $data;
    }
    /**
     * @name            decrypt_session_data()
     *                  Decrypts the session data.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.4
     *
     * @deprecated      will be depreceated in v1.2.0, use $this->decryptSessionData() instead.
     *
     * @return          array       $data
     */
    private function decrypt_session_data(){
        return $this->decryptSessionData();
    }
}
/**
 * Change Log
 * **************************************
 * v1.0.5                      Can Berkol
 * 05.06.2014
 * **************************************
 * A isActionRevoked()
 *
 * **************************************
 * v1.0.4                      Can Berkol
 * 23.04.2013
 * **************************************
 * CamelCase switch.
 * A isActionGrand()
 *
 * **************************************
 * v1.0.2                      Can Berkol
 * 08.01.2013
 * **************************************
 * U has_access() Now, if status not given status check will not be run.
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * 15.08.2013
 * **************************************
 * B decrypt_session_data() Session key is fixed.
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 11.08.2013
 * **************************************
 * A __construct()
 * A __destruct()
 * A decrypt_session_data()
 * A has_access()
 * A is_authenticated()
 * A is_guest()
 *
 */