<?php
/**
 * Core Controller Class
 *
 * This class provides an abstract foundation to all Biber Ltd. Core files.
 *
 * @vendor      BiberLtd
 * @package		Core
 * @subpackage
 * @name	    Core
 *
 * @author		Can Berkol
 * @author      Said İmamoğlu
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.4.1
 * @date        09.09.2015
 *
 */

namespace BiberLtd\Bundle\CoreBundle;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CoreController extends Controller {
	public $av;          /** Access validator */
	public $body;        /** Holds details of body: js, classes */
	public $flash;       /** Flash messages */
	public $head;        /** Holds details of head: css, js */
	public $initialized = false;
	public $language;    /** Current locale's language entity. */
	public $locale;      /** Current locale */
	public $page;        /** Array that contains current page details */
	public $previousUrl; /** stores previous url */
	public $renderResponse; /** Render response */
	public $site;        /** Current site */
	public $sm;          /** Session manager */
	public $timezone;    /** new \DateTimeZone identifies app time zone */
	public $translator;  /** Translator */
    public $url;         /** Array Base urls.  */
    /** @var    array       $vars   vars to use in template */
	public $vars;
    /** @var    object      $session */
    protected $session = null;
    /** @var    array       Holds admin arrays. Set with setAdminPs() method.  */
    private $adminIps = [];
    /*******************************************************************
     * INITIALIZATION FUNCTIONS
     *******************************************************************/

    /**
     * @name                init()
     *                      Initializes frequently used items.
     *
     * @author              Can Berkol
     *
     * @since               1.1.1
     * @version             1.3.8
     *
     * @param               integer         $siteId
     * @param               string          $pageCode
     * @param               string          $theme
     */
    public function init($siteId = null, $pageCode = null, $theme = null){
        $this->initCore($theme);

        /** Get current language */
        $mlsModel = $this->get('multilanguagesupport.model');
        $response = $mlsModel->getLanguage($this->locale, 'iso_code');
        $this->language = false;
        if(!$response->error->exist){
            $this->language = $response->result->set;
        }
        unset($response);
        /** Get current site */
        $siteModel = $this->get('sitemanagement.model');
		if(is_null($siteId)){
			$siteId = $this->session->get('_currentSiteId');
		}
		$response = $siteModel->getSite($siteId, 'id');

        $this->site = false;
        if(!$response->error->exist){
            $this->site = $response->result->set;
        }
        unset($response);
        if(!is_null($pageCode)){
            /** Get current page */
            $cmsModel = $this->get('cms.model');
            $response = $cmsModel->getPage($pageCode, 'code');
            $this->page['entity'] = false;
            if(!$response->error->exist) {
                $this->page['entity'] = $response->result->set;
                $this->theme = $this->page['entity']->getLayout()->getTheme()->getFolder();
            }
            /** Get page blocks */
            $response = $cmsModel->listModulesOfPageLayoutsGroupedBySection($this->page['entity'], array('sort_order' => 'asc'));
            if ($response->error->exist) {
                /** Show error if no modules can be loaded */
                echo $this->translator->trans('msg.error.modules', array(), 'systemMessages');
                exit;
            }
            $this->page['blocks'] = $response->result->set;
            unset($response);
        }
        if (!is_null($theme)) {
            $this->theme = $theme;
        }else{
            if (!isset($this->theme) || is_null($this->theme)) {
                $this->theme = $this->get('kernel')->getContainer()->getParameter('current_theme');
            }
        }
        $this->vars['xssCode'] = $this->generateXssCode();
		if(!is_null($pageCode) && strpos($this->get('request')->getPathInfo(), '|-') === false){
			$this->get('session')->set('previousUrl', $this->get('request')->getPathInfo());
		}
		$this->initialized = true;
    }
	/**
	 * @name            initCore()
	 *
	 * @author          Can Berkol
	 * @since           1.1.1
	 * @version         1.3.8
	 *
	 * @param           string  $theme
	 */
	public function initCore($theme = null){
		$this->previousUrl = $this->get('session')->get('previousUrl');
		$this->setURLs($theme);
		$this->body = array(
			'analytics' => '',
			'js'        => array(),
			'classes'   => array(),
		);
		$this->head = array(
			'css'   => array(),
			'js'    => array(),
		);
		/** *** */
		$this->timezone = new \DateTimeZone($this->container->getParameter('app_timezone'));
		if(is_null($this->session)){
			$this->session = $this->get('session');
		}
		$this->locale =  $this->session->get('_locale');
		$this->translator = $this->get('translator');
		$this->translator->setLocale($this->locale);
		$this->av = $this->get('access_validator');
		$this->sm = $this->get('session_manager');
	}
    /**
     * @name            initDefaults()
     *                  Initializes default values.
     *
     * @author          Can Berkol
     * @since           1.1.1
     * @version         1.3.6
     *
     * @param           array   $css
     * @param           array   $js
     * @param           array   $vars
     * @param           string  $theme
     *
     * @return          mixed
     */
    public function initDefaults($css = null, $js = null, $vars = null, $theme = null) {
        if (is_null($theme)) {
            $theme = $this->get('kernel')->getContainer()->getParameter('current_theme');
        }
        /**
         * SET APPLICATION WIDE CONSTANTS
         */
		/**
		 * @deprecated in v1.5.0
		 *             body, head and others will be deprecated in favor for assetic.
		 */
        $default_css = array();
        $default_js = array();
        if (is_null($css)) {
            $css = $default_css;
        } else {
            $css = array_merge_recursive($default_css, $css);
        }

        if (is_null($js)) {
            $js = $default_js;
        } else {
            $js = array_merge_recursive($default_js, $js);
        }
        $pre_conf_css = '';
        $pre_conf_js = '';
        if (!isset($this->url) || is_null($this->url)) {
            $this->setURLs($theme);
        }
        $defaultLinks = $this->url;
        foreach ($css as $item) {
            $pre_conf_css .= '<link rel="stylesheet" href="' . $this->url['themes'] . '/' . $theme . '/' . $item . '" />' . PHP_EOL;
        }
        foreach ($js as $item) {
            $pre_conf_js .= '<script type="text/javascript" src="' . $this->url['themes'] . '/' . $theme . '/' . $item . '"></script>' . PHP_EOL;
        }
		/** *** */
        $defaults = array(
            'doctype' => '<!DOCTYPE html>',
            'conditional_classes' => '',
            /** @deprecated css & js. */
            'css' => $pre_conf_css,
            'js' => $pre_conf_js,
            'head'  => array(
                'css'   => $pre_conf_css,
                'js'    => $pre_conf_js,
            ),
            'locale' => $this->container->get('request')->getLocale(),
            'link' => $this->url,
        );

        /**
         * Setup all other variables.
         */
        if (!is_null($vars)) {
            if (isset($vars['link'])) {
                $links = array_merge($defaultLinks, $vars['link']);
                $defaults['link'] = $links;
                unset($vars['link'], $links);
            }
            $defaults = array_merge($defaults, $vars);
        }
        return $defaults;
    }

    /*******************************************************************
     * DEBUG FUNCTIONS
     *******************************************************************/

    /**
     * @name                debug()
     *                      prints provided content
     *
     * @author              Can Berkol
     * @author              Said İmamoğlu
     *
     * @since           	1.0.0
     * @version         	1.4.1
     *
     * @param               mixed       $var        Content of variable
     * @param               bool        $exit       true|false
     * @param               string      $type       dump|print|echo
     *
     */
    public function debug($var, $exit = true, $type = 'dump') {
        $showOutput = true;
        if(count($this->adminIps) > 0){
            if(!in_array($_SERVER['REMOTE_ADDR'], $this->adminIps)){
                $showOutput = false;
            }
        }
        if(!$showOutput){
            return;
        }
        echo '<pre>';
        switch($type){
            case 'dump':
                var_dump($var);
                break;
            case 'echo':
                echo $var;
                break;
            case 'print':
                print_r($var);
                break;
        }

        if ($exit) {
            die;
        }
    }
    /**
     * @name            debugClass()
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @param 			mixed 		$class Class
     * @param 			bool 		$exit true|false
     * @since           1.1.3
     * @version         1.4.1
     *
     */
    public function debugClass($class, $exit = true) {
        $showOutput = true;
        if(count($this->adminIps) > 0){
            if(!in_array($_SERVER['REMOTE_ADDR'], $this->adminIps)){
                $showOutput = false;
            }
        }
        if(!$showOutput){
            return;
        }
        if (is_object($class)) {
            $reflectionClass = new \ReflectionClass($class);
            $methods = $reflectionClass->getMethods();
            foreach ($methods as $method) {
                echo $method->class.'->'.$method->name.'()'.'<br>';
            }
        } else{
            echo $class .' is not a valid Class.';
        }
        if ($exit) {
            die;
        }
    }

    /*******************************************************************
     * GENERIC FUNCTIONS
     *******************************************************************/

    /**
     * @name            generateUid()
     *                  Frequently used in render fucntions to generate unique ids for HTML elements. But it can be used
     *                  for other purposes.
     *
     * @author          Can Berkol
     * @since           1.2.5
     * @version         1.2.5
     *
     * @return          array           $flash
     */
    public function generateUid($prefix = 'uid-'){
        /** UID's are timestamp based codes. Since id's cannot start with a number in HTML, it requires a prefix. The
        id then glued with the current timestamps last 5 digits. This is done so that the generated id won't be too
        long.
         */
        return $prefix.substr(time(), -5);
    }
    /**
     * @name            generateXssCode()
     *                  Generates a unique hash for cross site forgery attack prevention.
     *
     * @author          Can Berkol
     * @since           1.2.5
     * @version         1.3.7
     *
     * @return          array           $flash
     */
    public function generateXssCode(){
        if(!isset($this->session)){
            $this->session = $this->container->get('session');
        }
        $currentXssCode = $this->session->get('_xss');
        $currentXssTime = $this->session->get('_xss_timestamp');

        $ip = $this->container->get('request')->getClientIp();
        $now = time();
        if (!$currentXssTime || !$currentXssCode) {
            $currentXssCode = md5($ip . $now);
            $this->session->set('_xss', $currentXssCode);
            $this->session->set('_xss_timestamp', $now);
            return $currentXssCode;
        }

        $timeDifference = $now - $currentXssTime;
        /**
         * 120 seconds * 5 = 600 seconds = 10 minutes
         */
        if ($timeDifference > 600) {
            /** If time difference since last request is more than 10 minutes change security code alongside with timestamp. */
            $now = time();
            $this->session->set('_xss', md5($ip . $now));
            $this->session->set('_xss_timestamp', $now);
        }
        else{
            /** If the last request happenned earliear than 10 minutes just update timestamp and increase life span of hash code. */
            $this->session->set('_xss_timestamp', $now);
        }
        return $currentXssCode;
    }
    /**
     * @name            generateUrlKey()
     *
     * @author          Can Berkol
     * @since           1.0.7
     * @version         1.3.6
     *
     * @param           string          $translate
     *
     * @return          string
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
    /**
     * @name                ifAccessNotGranted()
     *                      Checks if current session's user has access or not for the provided action. And if not it
     *                      redirects the user to the defined router.
     *
     * @author              Can Berkol
     *
     * @since               1.2.0
     * @version             1.2.0
     *
     * @param               action      Action code.
     * @param               mixed       $redirect   false | string containing route definition
     * @param               string      $type
     * @param               string      $msg
     * @param               string      $route
     * @param               array 		$params
	 * @param				integer		$status
	 * @param				bool		$https
     *
     * @return              mixed       bool|RedirectResponse
     *
     */
    public function ifAccessNotGranted($action, $redirect = false, $type = 'danger', $msg = '', $route = null, $params = array(), $status = 302, $https = false){
        if(!$this->initialized){
			$this->initCore();
		}
		if(!$this->av->isActionGranted($action)){
            if($redirect){
                return $this->redirectWithMessage($type, $msg, $route, $params, $status, $https, null);
            }
        }
        return false;
    }
    /**
     * @name                ifLoggedin()
     *                      Checks only if the user is logged in. The user group is disregarded.
     *
     * @author              Can Berkol
     *
     * @since               1.2.1
     * @version             1.3.7
     *
     * @param               mixed       $redirect   false | string containing route definition
	 * @param               string      $route
	 * @param               bool      	$https
     *
     * @return              mixed       bool|RedirectResponse
     *
     */
    public function ifLoggedin($redirect = false, $route = null, $https = false){
		if(!$this->initialized){
			$this->initCore();
		}
        $access_map = array(
            'unmanaged' => false,
            'guest' => false,
            'authenticated' => true,
            'members' => array(),
            'groups' => array(),
            'status' => array('a')
        );
        if ($this->av->hasAccess(null, $access_map)) {
            $this->sm->logAction('page.visit.fail.insufficient.rights', 1, array('route' => $this->generateUrl($route)));
            if(!$redirect){
                return true;
            }
			return $this->redirectAdvanced($route, array(), 302, $https);
        }
        return false;
    }
    /**
     * @name                ifLoggedinManager()
     *                      Wrapper to check if the user is a manager and has access rights to manage/ controllers.
     *
     * @author              Can Berkol
     *
     * @since               1.1.1
     * @version             1.3.7
     *
     * @param               mixed       $redirect   false | string containing route definition
	 * @param               string      $route
	 * @param               bool      	$https
     * @param               array       $managers
     *
     * @return              mixed       bool|RedirectResponse
     *
     */
    public function ifLoggedinManager($redirect = false, $route = null, $https = false, $managers = array()){
		if(!$this->initialized){
			$this->initCore();
		}
        if(count($managers) < 1){
            $managers = array('admin', 'support', 'manager', 'management');
        }
        $access_map = array(
            'unmanaged' => false,
            'guest' => false,
            'authenticated' => true,
            'members' => array(),
            'groups' => $managers,
            'status' => array('a')
        );
        if ($this->av->hasAccess(null, $access_map)) {
            $this->sm->logAction('page.visit.fail.insufficient.rights', 1, array('route' => $this->generateUrl($route)));
            if(!$redirect){
                return true;
            }
			return $this->redirectAdvanced($route, array(), 302, $https);
        }
        return false;
    }
    /**
     * @name                ifNotManager()
     *                      Wrapper to check if the user is a manager and has access rights to manage/ controllers.
     *
     * @author              Can Berkol
     *
     * @since               1.1.1
     * @version             1.3.7
     *
     * @param               mixed       $redirect   false | string containing route definition
	 * @param               string      $route
	 * @param               bool      	$https
     *
     * @return              mixed       bool|RedirectResponse
     *
     */
    public function ifNotManager($redirect = false, $route = null, $https = false){
		if(!$this->initialized){
			$this->initCore();
		}
        /**
         * @todo bind it to database
         */
        $access_map = array(
            'unmanaged' => false,
            'guest' => false,
            'authenticated' => true,
            'members' => array(),
            'groups' => array('admin', 'support', 'manager', 'management'),
            'status' => array('a')
        );
        if (!$this->av->hasAccess(null, $access_map) && !$this->av->isActionGranted('manage.access')) {
            $this->sm->logAction('page.visit.fail.insufficient.rights', 1, array('route' => $this->generateUrl($route)));
            if(!$redirect){
                return true;
            }
			return $this->redirectAdvanced($route, array(), 302, $https);
        }
        return false;
    }

    /**
     * @name                ifNotMember()
     *
     * @author              Can Berkol
     *
     * @since               1.1.1
     * @version             1.3.7
     *
     * @param               mixed       $redirect   false | string containing route definition
     * @param               string      $route
     * @param               bool      	$https
     *
     * @return              mixed       bool|RedirectResponse
     *
     */
    public function ifNotMember($redirect = false, $route = null, $https = false){
		if(!$this->initialized){
			$this->initCore();
		}
        /**
         * @todo bind it to database
         */
        $access_map = array(
            'unmanaged' => false,
            'guest' => false,
            'authenticated' => true,
            'members' => array(),
            'groups' => array(),
            'status' => array('a')
        );
        if (!$this->av->hasAccess(null, $access_map)) {
            $this->sm->logAction('page.visit.fail.insufficient.rights', 1, array('route' => $this->generateUrl($route)));
            if(!$redirect){
                return true;
            }
            return $this->redirectAdvanced($route, array(), 302, $https);
        }
        return false;
    }
    /**
     * @name                ifRevokedAction()
     *                      Wrapper to check if the user is a manager and has access rights to manage/ controllers.
     *
     * @author              Can Berkol
     *
     * @since               1.2.7
     * @version             1.2.7
     *
     * @param               string      $actionCode
     * @param               string      $route
     *
     * @return              mixed       bool|RedirectResponse
     *
     */
    public function ifRevokedAction($actionCode, $route = ''){
		if(!$this->initialized){
			$this->initCore();
		}
        if ($this->av->isActionRevoked($actionCode)) {
            $this->sm->logAction($actionCode, 1, array('route' => $route));
            $this->sm->logAction('page.visit.fail.insufficient.rights', 1, array('route' => $route));
            return true;
        }
        return false;
    }
    /**
     * @name                isValidXss()
     *                      Compares form submitted xss code against the session and returns true if both values are
     *                      identical.
     *
     * @author              Can Berkol
     *
     * @since               1.2.5
     * @version             1.2.5
     *
     * @param               string          $xssCode
     *
     * @return              bool
     *
     */
    public function isValidXss($xssCode){
        $savedXssCode = $this->session->get('_xss');
        /** @deprecated and will be removed in v1.3.0 use _xss instad. */
        if(is_null($savedXssCode) || $savedXssCode == ''){
            $savedXssCode = $this->session->get('_csfr');
        }
        if($savedXssCode === $xssCode){
            return true;
        }
        return false;
    }

    /**
     * @name    jsonDecode()
     *          Returns json decoded data after stripping slashes.
     *
     * @author  Said İmamoğlu
     * @since   1.3.1
     * @version 1.3.1
     *
     * @param   string      $data    json data
     * @param   bool        $assoc   true|false
     *
     * @return  mixed   object|array
     */
    public function jsonDecode($data,$assoc=false){
        return json_decode(stripslashes($data),$assoc);
    }
    /**
     * @name    json_encode()
     *          Returns json encoded data after encoding utf-8 .
     *
     * @author  Said İmamoğlu
     * @since   1.3.2
     * @version 1.3.2
     *
     * @param   array|object      $data    json data
     *
     * @return  mixed   string
     */
    public function json_encode($data){
        return preg_replace_callback(
            '/\\\\u([0-9a-zA-Z]{4})/',
            function ($matches) {
                return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');
            },
            json_encode($data)
        );
    }
    /**
     * @name    jsonEncode()
     *          Alias of json_encode()
     *
     * @author  Said İmamoğlu
     * @since   1.3.2
     * @version 1.3.2
     *
     * @param   array|object      $data    json data
     *
     * @return  mixed   string
     */
    public function jsonEncode($data){
        return $this->json_encode($data);
    }
    /**
     * @name                mergeResponseBodyAndHead()
     *                      Merges response body and head of the render function into $this->body & $this->head
     *
     * @author              Can Berkol
     *
     * @since               1.3.3
     * @version             1.2.3
     *
     * @param               array           $response
     */
    public function mergeResponseBodyAndHead($response){
        $body = null;
        $head = null;
        if(isset($response['body'])){
            $body = $response['body'];
        }
        if(isset($response['head'])){
            $head = $response['head'];
        }
        if(!is_null($body)){
            if(isset($body['analytics'])){
                $this->body['analytics'][] = $body['analytics'];
            }
            if(isset($body['classes'])){
                foreach($body['classes'] as $item){
                    $this->body['classes'][] = $item;
                }
            }
            if(isset($body['js'])){
                foreach($body['js'] as $item){
                    $this->body['js'][] = $item;
                }
            }
        }
        if(!is_null($head)){
            if(isset($head['css'])){
                foreach($head['css'] as $item){
                    $this->head['css'][] = $item;
                }
            }
            if(isset($head['js'])){
                foreach($head['js'] as $item){
                    $this->head['js'][] = $item;
                }
            }
        }
    }
    /**
     * @name            prepareUrl()
     *                  Prepares URLs.
     *
     * @author          Can Berkol
     * @since           1.1.1
     * @version         1.3.2
     *
     * @param           bool            $append_locale      If set to true appends current locale.
     * @param           bool            $prepend_boot
     * @param           mixed           $parameters         Either a string or an array of strings.
     * @param           mixed           $force_https         Generate secure url (https://..)
     *
     * @return          string          $url
     */
    public function prepareUrl($append_locale = true, $prepend_boot = false, $parameters = null,$force_https = false) {
        $host = $this->get('request')->getHttpHost();
        $protocol = 'http://';
        if ($this->get('request')->isSecure() || $force_https) {
            $protocol = 'https://';
        }
        $url = $protocol . $host;
        /**
         * Make sure to add app_dev if the environment is set to dev
         */
        if ($prepend_boot) {
            if ($this->get('kernel')->getEnvironment() == 'dev') {
                $url .= '/app_dev.php';
            } else {
                $url .= '/app.php';
            }
        }
        if ($append_locale) {
            $url .= '/' . $this->container->get('request')->getLocale();
        }
        if (!is_null($parameters)) {
            if (is_array($parameters)) {
                $url = $url . implode('/', $parameters);
            } else {
                $url .= $parameters;
            }
        }
        return $url;
    }
    /**
     * @name            prepareCss()
     *                  Prepares css tags.
     *
     * @author          Can Berkol
     * @since           1.2.2
     * @version         1.2.2
     *
     * @param           array   $css
     *
     * @return          mixed
     */
    public function prepareCss($css = null) {
        $css = array_unique($css);
        $cssStr = '';
        foreach ($css as $item) {
            if(strpos($item, 'http') !== false){
                $cssStr .= '<script type="text/javascript" src="'.$item.'"></script>'.PHP_EOL;
            }
            else{
                $cssStr .= '<link rel="stylesheet" href="' . $this->url['domain'].'/themes/'.$this->theme.$item.'" />'.PHP_EOL;
            }
        }
        return $cssStr;
    }
    /**
     * @name            prepareFlash()
     *                  Prepares Flash messages.
     *
     * @author          Can Berkol
     * @since           1.1.1
     * @version         1.1.1
     *
     * @param           Session         $session
     *
     * @return          array           $flash
     */
    public function prepareFlash($session = null) {
        $flash = array(
            'exist' => false,
            'type' => null,
            'message' => null,
            'optional'=> null
        );
        if(is_null($session)){
            $session = $this->session;
        }
        $statuses = $session->getFlashBag()->get('msg.status');
        $types = $session->getFlashBag()->get('msg.type');
        $contents = $session->getFlashBag()->get('msg.content');
        $optional = $session->getFlashBag()->get('optional');
        $optional = count($optional) > 0 ? $optional[0] : '';
        if (isset($statuses[0])) {
            if ($statuses[0]) {
                $flash = array(
                    'exist' => true,
                    'type' => $types[0],
                    'message' => $contents[0],
                    'optional' => $optional,
                );
            }
        }
        return $flash;
    }
    /**
     * @name            prepareJs()
     *                  Prepares js tags.
     *
     * @author          Can Berkol
     * @since           1.2.2
     * @version         1.2.2
     *
     * @param           array   $js
     *
     * @return          mixed
     */
    public function prepareJs($js = null) {
		$js = array_unique($js);
		$jsStr = '';
		foreach ($js as $item) {
			if (strpos($item, 'http') !== false) {
				$jsStr .= '<script type="text/javascript" src="' . $item . '"></script>' . PHP_EOL;
			}
			else {
				$jsStr .= '<script type="text/javascript" src="' . $this->url['domain'] . '/themes/' . $this->theme . $item . '"></script>' . PHP_EOL;
			}
		}

		return $jsStr;
	}

// @todo		Delete the following comments in version >= 1.5.0
//    /**
//     * @name            prepareManagementSidebar()
//     *                  Prepares management sidebar.
//     *
//     * @author          Can Berkol
//     * @since           1.1.0
//     * @version         1.1.0
//     *
//     * @param           string          $parentNavUrlKey
//     * @deprecated     Will be removed şn v2.0.0
//     *
//     * @return          string
//     */
//    public function prepareManagementSidebar($parentNavUrlKey = null){
//        $cmsModel = $this->get('cms.model');
//        /**
//         * Get core render model and prepare core information
//         */
//        $coreRender = $this->get('corerender.model');
//        $core = array(
//            'locale' => $this->locale,
//            'theme' => $this->page['entity']->getLayout()->getTheme()->getFolder(),
//            'url' => $this->url,
//        );
//        /** Get project logo */
//        $siteSettings = json_decode($this->site->getSettings());
//        $projectLogoUrl = $this->url['cdn'].'/site/logo/'.$siteSettings->logo;
//        $dashboardSettings = array(
//            'link'  => $this->url['base_l'].'/manage/dashboard',
//            'title' => $this->translator->trans('dashboard.title', array(), 'admin'),
//        );
//        $renderedProjectLogo = $coreRender->renderProjectLogo($projectLogoUrl, $this->site->getTitle(), $core, $dashboardSettings);
//        unset($projectLogoUrl, $dashboardSettings);
//        $sidebar['projectLogo'] = $renderedProjectLogo;
//        /** Prepare sidebar separator */
//        $renderedSidebarSeparator = $coreRender->renderSidebarSeparator($core);
//        $sidebar['separator'] = $renderedSidebarSeparator;
//        /** Get sidebar navigation */
//        $response = $cmsModel->listItemsOfNavigation('cms_nav_main', 'top', array('sort_order' => 'asc'));
//        $sideNavItems = array();
//        if(!$response->error->exist){
//            $sideNavItems = $response->result->set;
//        }
//        unset($response);
//        $navCollection = array();
//        foreach($sideNavItems as $navItem){
//            $response = $cmsModel->listNavigationItemsOfParent($navItem, array('sort_order' => 'asc'));
//            $childItems = array();
//            $hasChildren = false;
//            $selectedParent = false;
//            if(!$response->error->exist){
//                $hasChildren = true;
//                foreach($response->result->set as $childItem){
//                    $childNavSelected = false;
//                    if($childItem->getPage()->getId() == $this->page['entity']->getId()){
//                        $childNavSelected = true;
//                        $selectedParent = $childItem->getParent()->getId();
//                    }
//                    $childItems[] = array(
//                        'entity'  => $childItem,
//                        'selected'=> $childNavSelected,
//                    );
//                }
//            }
//            $navSelected = false;
//            if(is_null($parentNavUrlKey)){
//                if($navItem->getId() == $selectedParent){
//                    $navSelected = true;
//                }
//            }
//            else{
//                foreach($navItem->getLocalizations() as $localization){
//                    if($localization->getUrlKey() == $parentNavUrlKey){
//                        $navSelected = true;
//                        break;
//                    }
//                }
//            }
//            $navCollection[]  = array(
//                'children'      => $childItems,
//                'code'          => time(),
//                'entity'        => $navItem,
//                'hasChildren'   => $hasChildren,
//                'selected'      => $navSelected,
//            );
//            unset($response, $childItems);
//        }
//        unset($sideNavItems);
//
//        $renderedSidebarNavigation = $coreRender->renderSidebarNavigation($navCollection, $core);
//        $sidebar['navigation'] = $renderedSidebarNavigation;
//        return $sidebar;
//    }
//    /**
//     * @name            prepareManagementTopbar()
//     *                  Prepares management topbar.
//     *
//     * @author          Can Berkol
//     * @since           1.1.0
//     * @version         1.1.8
//     *
//     * @deprecated     Will be removed şn v2.0.0
//     *
//     * @return          string
//     */
//    public function prepareManagementTopbar(){
//        $cmsModel = $this->get('cms.model');
//        $mlsModel = $this->get('multilanguagesupport.model');
//        /**
//         * Get core render model and prepare core information
//         */
//        $coreRender = $this->get('corerender.model');
//        $core = array(
//            'locale' => $this->locale,
//            'theme' => $this->page['entity']->getLayout()->getTheme()->getFolder(),
//            'url' => $this->url,
//        );
//        /** Get top navigation */
//        $response = $cmsModel->listItemsOfNavigation('cms_nav_top', 'top', array('sort_order' => 'asc'));
//        $topNavItems = array();
//        if(!$response->error->exist){
//            $topNavItems = $response->result->set;
//        }
//        $topNavigation = $coreRender->renderQuickActionsNavigation($topNavItems, $core);
//        unset($topNavItems, $response);
//        $topbar['navigation'] = $topNavigation;
//        unset($topNavigation);
//        /** Create language dropdown */
//        $response = $mlsModel->listAllLanguages();
//        $otherLanguages = array();
//        $currentLanguage = null;
//        if(!$response->error->exist) {
//            $allLanguages = $response->result->set;
//            foreach($allLanguages as $language){
//                if($language->getIsoCode() == $this->locale){
//                    $currentLanguage = $language;
//                }
//                else{
//                    $otherLanguages[] = $language;
//                }
//            }
//            unset($allLanguages, $language);
//        }
//        $pathInfo = str_replace($this->locale.'/', '', $this->get('request')-> getPathInfo());
//        $langDropDown = $coreRender->renderLanguageDropdown($currentLanguage, $otherLanguages, $core, $pathInfo);
//        $topbar['navigationLang'] = $langDropDown;
//        unset($langDropDown);
//
//        return $topbar;
//    }

    /**
     * @name            preparePagination()
     *                  Prepares pagination.
     *
     * @author          Can Berkol
     *                  Mehmet Aydın Bahadır
     *                  Said İmamoğlu
     * @since           1.1.1
     * @version         1.3.4
     *
     * @param           integer         $page       Current page number
     * @param           integer         $total      Total number of items found in database / collection
     * @param           integer         $limit
     * @param           mixed           $maxPage
     *
     * @return          array           $pagination
     */
    public function preparePagination($page, $total, $limit, $maxPage = false) {
        /**
         * Calculate number of pages.
         */
        $number_of_pages = 0;
        if ($total % $limit > 0) {
            $number_of_pages = ceil($total / $limit);
        } else {
            $number_of_pages = ceil($total / $limit);
        }
        $number_of_pages = $number_of_pages <1 ? 1 : $number_of_pages;
        /**
         * Sets how many page numbers to be shown.
         */
        if ($maxPage && $number_of_pages > $maxPage) {
            $number_of_pages = $maxPage;
        }
        /**
         * Build pagination
         */
        $pagination = array();

        $pagination['current_page'] = $page;
        $pagination['number_of_pages'] = $number_of_pages;
        /**
         * Decide where to place the dots
         */
        if ($page > 4) {
            $pagination['first_far_away'] = true;
        } else {
            $pagination['first_far_away'] = false;
        }
        if ($page < $number_of_pages - 3) {
            $pagination['last_far_away'] = true;
        } else {
            $pagination['last_far_away'] = false;
        }

        if ($pagination['first_far_away'] && $pagination['last_far_away']) {
            $pagination['items'] = array($page - 2, $page - 1, $page, $page + 1, $page + 2);
        } else if ($pagination['first_far_away'] && !$pagination['last_far_away']) {
            for ($i = $page - 2; $i <= $number_of_pages; $i++) {
                $pagination['items'][] = $i;
            }
        } else if (!$pagination['first_far_away'] && $pagination['last_far_away']) {
            for ($i = 1; $i <= $page + 2; $i++) {
                $pagination['items'][] = $i;
            }
        } else {
            for ($i = 1; $i <= $number_of_pages; $i++) {
                $pagination['items'][] = $i;
            }
        }
        $pagination['prev'] = $page-1 <=0 ? 1 : $page-1;
        $pagination['next'] = $page + 1 > $number_of_pages ? $number_of_pages : $page +1 ;

        return $pagination;
    }
    /**
     * @name            redirectAdvanced()
     *                  Redirects controller to a specific url.
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @since           1.3.7
     * @version         1.3.7
     *
     * @param           string          $routeName		Name of route
     * @param           array           $parameters
     * @param           integer			$status
	 * @param			bool			$https			false|true
     *
     * @return          RedirectResponse
     */
    public function redirectAdvanced($routeName, $parameters = array(), $status = 200, $https = false) {
        $route = '';
        $url = $this->generateUrl($routeName, $parameters);
		if($https){
			$url = str_replace('http://', 'https://', $url);
		}
        return new RedirectResponse($url, $status);
    }
    /**
     * @name            redirectWithMessage()
     *                  Redirects controller to a specific url with flash message.
     *
     * @author          Can Berkol
     * @since           1.1.7
     * @version         1.3.7
     *
     * @param           string              $msgType
     * @param           string              $msgContent
     * @param           string              $to
     * @param           array               $params
     * @param           integer             $status
     * @param           bool                $https
     * @param           mixed               $optional
     *
     * @return          RedirectResponse
     */
    public function redirectWithMessage($msgType, $msgContent, $to = '404', $params = array(), $status = 302, $https = false, $optional = null) {
        $response = $this->redirectAdvanced($to, $params, $status, $https);
        $this->session->getFlashBag()->add('msg.status', true);
        $this->session->getFlashBag()->add('msg.type', $msgType);
        /** $response[$code] must have a corresponding translation */
        $this->session->getFlashBag()->add('msg.content', $msgContent);
        $this->session->getFlashBag()->add('optional', $optional);
        return $response;
    }
    /**
     * @name            renderPage()
     *                  Accepts an ar
     *
     * @author          Can Berkol
     * @since           1.1.0
     * @version         1.3.8
     *
     * @return          string
     */
    public function renderPage(){
		$this->flash = $this->prepareFlash($this->session);
        if(!isset($this->vars['flash'])){
            $this->vars['flash'] = $this->flash;
        }
        if(!isset($this->vars['page'])){
            $this->vars['page'] = array(
                'blocks'        => $this->page['blocks'],
                'entity'        => $this->page['entity'],
            );
            if(!isset($this->vars['page']['meta'])){
                $this->vars['page']['meta'] = array(
                    'description'   => $this->page['entity']->getLocalization($this->locale)->getMetaDescription(),
                    'keywords'      => $this->page['entity']->getLocalization($this->locale)->getMetaKeywords(),
                    'title'         => $this->page['entity']->getLocalization($this->locale)->getTitle(),
                );
            }
        }
        else{
            if(!isset($this->vars['page']['blocks'])){
                $this->vars['page']['blocks'] = $this->page['blocks'];
            }
            if(!isset($this->vars['page']['entity'])){
                $this->vars['page']['entity'] = $this->page['entity'];
            }
            if(!isset($this->vars['page']['meta'])){
                $this->vars['page']['meta'] = array(
                    'description'   => $this->page['entity']->getLocalization($this->locale)->getMetaDescription(),
                    'keywords'      => $this->page['entity']->getLocalization($this->locale)->getMetaKeywords(),
                    'title'         => $this->page['entity']->getLocalization($this->locale)->getTitle(),
                );
            }
        }
        if(!isset($this->vars['site'])){
            $this->vars['site']['entity'] = $this->site;
            $this->vars['site']['name'] = $this->site->getTitle();
        }
        if(!isset($this->vars['link'])){
            $this->vars['link'] = $this->url;
        }
		if(!isset($this->vars['locale'])){
			$this->vars['locale'] = $this->locale;
		}

		/**
		 * @deprecated body and head will be removed in v1.5.0 in favor for assetic..
		 */
        $this->vars['body'] = $this->body;
        $this->vars['head'] = $this->head;
        if(!isset($this->head['css'])){
            $this->head['css'] = '/css/styles.min.css';
        }
        $this->vars['body']['classes'] = array_unique($this->vars['body']['classes']);
        $this->vars['head']['css'] = array_merge($this->head['css'], $this->vars['head']['css']);
        $this->vars['body']['js'] = $this->prepareJs($this->vars['body']['js']);
        $this->vars['head']['js'] = $this->prepareJs($this->vars['head']['js']);
        $this->vars['head']['css'] = $this->prepareCss($this->vars['head']['css']);
		/** *** */
        return $this->render($this->page['entity']->getBundleName().':'.$this->page['entity']->getLayout()->getTheme()->getFolder().'/Pages:'.$this->page['entity']->getCode().'.html.smarty', $this->vars);
    }
    /**
     * @name            resetRenderResponse()
     *                  Resets the response returned from a render controller
     *
     * @author          Can Berkol
     * @since           1.2.0
     * @version         1.2.0
     *
     * @return          $this
     */
    public function resetRenderResponse() {
        $this->response['renderCode'] = '';
		/** @deprecated		in !v1.6.0! body and head will be removed in favor of Assetic */
        $this->response['body']['js'] = array();
		$this->response['head']['css'] = array();
		$this->response['head']['js'] = array();
		/** *** */
		$this->response['html'] = '';
		return $this;
    }
    /**
     * @name            setAdminIps()
     *                  Sets the flash message to flashBag..
     *
     * @author          Can Berkol
     * @since           1.4.1
     * @version         1.4.1
     *
     * @param           array       $ips        a list of ip addresses.
     * @return          mixed
     */
    public function setAdminIps(array $ips){
        $this->adminIps = $ips;

        return $this->session;
    }
    /**
     * @name            setFlashMessage()
     *                  Sets the flash message to flashBag..
     *
     * @author          Can Berkol
     * @since           1.1.5
     * @version         1.1.5
     *
     * @param           string          $content
     * @param           string          $type           danger|success
     * @param           bool            $status         true|false
     *
     * @return          mixed
     */
    public function setFlashMessage($content, $type = 'danger', $status = true){
        $this->session->getFlashBag()->add('msg.status', $status);
        $this->session->getFlashBag()->add('msg.type', $type);
        $this->session->getFlashBag()->add('msg.content', $content);

        return $this->session;
    }
    /**
     * @name            setURLs()
     *                  Sets all URLs.
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     * @since           1.0.5
     * @version         1.3.2
     *
     * @param           string          $theme
     * @param           string          $backend
     *
     * @return          mixed
     */
    public function setURLs($theme = 'cms', $backend = 'manage') {
        $force_https = false;
        if ($this->get('request')->isSecure()) {
            $force_https = true;
        }
	    if ($this->get('kernel')->getEnvironment() == 'dev') {
		    $url['base_l'] = $this->prepareUrl(true, true, null, $force_https);
		    $url['https_l'] = $this->prepareUrl(true, true, null,true);
	    } else {
		    $url['base_l'] = $this->prepareUrl(true, false, null, $force_https);
		    $url['https_l'] = $this->prepareUrl(true, false, null,true);
	    }
	    if ($this->get('kernel')->getEnvironment() == 'dev') {
		    $url['base'] = $this->prepareUrl(false, true, null, $force_https);
		    $url['https'] = $this->prepareUrl(false, true, null,true);
	    } else {
		    $url['base'] = $this->prepareUrl(false, false, null, $force_https);
		    $url['https'] = $this->prepareUrl(false, false, null, true);
	    }
        unset($force_https);

        $url['domain'] = str_replace('/app_dev.php', '', $url['base']);
        $url['themes'] = $url['domain'].'/themes';
        $url['current_theme'] = $url['themes'].'/'.$theme;
        $url['cdn'] = $url['domain'].'/cdn';
        $url['icons'] = $url['current_theme'].'/img/icons';
        $url['manage'] = $url['base_l'].'/'.$backend;
        $this->url = $url;
        return $this;
    }
    /**
     * @name    persistFlashMessages()
     *          Persisting current data in Session\FlashBag
     *
     * @author  Said İmamoğlu
     * @since   1.2.8
     * @version 1.2.8
     *
     * @return  bool
     */
    public function persistFlashMessages(){
        foreach ($this->session->getFlashBag()->all() as $key => $flash) {
            $this->session->getFlashBag()->set($key,$flash);
        }
        return true;
    }
}
/**
 * Change Log
 * **************************************
 * v1.4.1                      09.09.2015
 * Can Berkol
 * **************************************
 * CR :: 3926353 :: debug method now supports admin ip filter.
 * FR :: 3926353 :: setAdminIPs() method implemented.
 *
 * **************************************
 * v1.4.0                      03.07.2015
 * Can Berkol
 * **************************************
 * CR :: properties are now public.
 *
 * **************************************
 * v1.3.8                      08.06.2015
 * Can Berkol
 * **************************************
 * BF :: prepareFlashes() moved to renderPage() method to decrease the chances of flash messages being lost in redirections.
 *
 * **************************************
 * v1.3.7                      04.06.2015
 * Can Berkol
 * **************************************
 * CR :: redirect() method is renamed to redirectAdvanced(). This way Symfony2 redirect() method is not overwritten.
 * CR :: Deprecated key _csfr and related functionality has been removed from generateXssCode() method.
 * BF :: Usage of several deprecated methods fixed.
 *
 * **************************************
 * v1.3.6                      26.05.2015
 * Can Berkol
 * **************************************
 * CR :: Deprecation notices have been added.
 * CR :: ModelResponse usage related fixes applied
 * CR :: site parameter is now optional in init() method and if it is null the siteId automagically is read from session.
 * CR :: Unnecessary use statements have been removed.
 *
 * **************************************
 * v1.3.5                      01.05.2015
 * Can Berkol
 * **************************************
 * CR :: All deprecated methods removed.
 *
 * **************************************
 * v1.3.4                      Said İmamoğlu
 * 24.03.2015
 * **************************************
 * U preparePagination()
 * **************************************
 * v1.3.2                      Said İmamoğlu
 * 03.12.2014
 * **************************************
 * A json_encode()
 * A jsonEncode()
 * U prepareUrl()
 * U setURLs()
 * **************************************
 * v1.3.1                      Said İmamoğlu
 * 12.08.2014
 * **************************************
 * A jsonDecode()
 * **************************************
 * v1.3.0                      Can Bekrol
 * 11.07.2014
 * **************************************
 * U init()
 *
 * ************************************
 * v1.2.9                   Said İmamoğlu
 * 07.07.2014
 * **************************************
 * A persistFlashMessages()
 *
 * **************************************
 * v1.2.8                   Said İmamoğlu
 * 28.06.2014
 * **************************************
 * A persistFlashMessages()
 *
 * **************************************
 * v1.2.7                      Can Berkol
 * 05.06.2014
 * **************************************
 * A revokedAction()
 * U renderPage()
 *
 * **************************************
 * v1.2.6                      Can Berkol
 * 04.06.2014
 * **************************************
 * U ifNotManager()
 *
 * **************************************
 * v1.2.5                      Can Berkol
 * 28.05.2014
 * **************************************
 * A isValidXss()
 * A generateXssCode()
 * A generateUid()
 * D define_dev_ips()
 * R fileUpload()
 * R generateCSFR()
 *
 * **************************************
 * v1.2.4                   Said İmamoğlu
 * 20.05.2014
 * **************************************
 * A mergeResponseBodyAndHead()
 *
 * **************************************
 * v1.2.3                       Can Berkol
 * 29.04.2014
 * **************************************
 * A mergeResponseBodyAndHead()
 * U renderPage()
 *
 * **************************************
 * v1.2.2                       Can Berkol
 * 26.04.2014
 * **************************************
 * A init()
 * A legacyRenderPage()
 * A prepareCss()
 * A prepareJs()
 * U renderPage()
 *
 * **************************************
 * v1.2.1                       Can Berkol
 * 26.04.2014
 * **************************************
 * A ifLoggedin()
 * U ifLoggedinManager()
 * U setUrls()
 *
 * **************************************
 * v1.2.0                   Said İmamoğlu
 *                             Can Berkol
 * 22.04.2014
 * **************************************
 * A fileUpload()
 * A ifAccessNotGranted()
 * A resetRenderResponse()
 *
 * **************************************
 * v1.1.9                      Can Berkol
 * 22.04.2014
 * **************************************
 * U initDefaults()
 *
 * **************************************
 * v1.1.8                      Can Berkol
 * 10.04.2014
 * **************************************
 * U prepareManagementTopBar()
 *
 * **************************************
 * v1.1.7                      Can Berkol
 * 22.03.2014
 * **************************************
 * A redirectWithMessage()
 *
 * **************************************
 * v1.1.6                      Can Berkol
 * 10.03.2014
 * **************************************
 * U setFlashMessage()
 *
 * **************************************
 * v1.1.4                      Can Berkol
 * 05.03.2014
 * **************************************
 * U init()
 *
 * **************************************
 * v1.1.3                   Said İmamoğlu
 * 05.03.2014
 * **************************************
 * A debugClass()
 *
 * **************************************
 * v 1.1.2                     Can Berkol
 * 03.03.2014
 * **************************************
 * A ifLoggedinManager()
 *
 * **************************************
 * v 1.1.1                     Can Berkol
 * 02.03.2014
 * **************************************
 * A ifNotManager()
 * A initDefaults()
 * A prepareFlash()
 * A preparePagination()
 * A prepareManagementSidebar()
 * A prepareManagementTopbar()
 * A prepareUrl()
 * A redirect()
 * A renderPage()
 * R define_dev_ips()
 * R init_defaults()
 * R prepare_flash()
 * R prepare_pagination()
 * R prepare_url()
 * U debug()
 *
 * **************************************
 * v 1.1.0                  Said İmamoğlu
 * 19.02.2014
 * **************************************
 * U debug()
 *
 * **************************************
 * v 1.0.9                     Can Berkol
 * 12.02.2014
 * **************************************
 * U init_defaults()
 * U setUrls()
 *
 * **************************************
 * v 1.0.8                     Can Berkol
 * 05.02.2014
 * **************************************
 * U init_defaults()
 *
 * **************************************
 * v 1.0.7                     Can Berkol
 * 27.01.2014
 * **************************************
 * A generateUrlKey()
 *
 * **************************************
 * v 1.0.6                     Can Berkol
 * 11.01.2014
 * **************************************
 * A generateCSFR()
 *
 * **************************************
 * v 1.0.4                     Can Berkol
 * 08.09.2013
 * **************************************
 * A define_dev_ips()
 * U init_defaults()
 *
 * **************************************
 * v 1.0.3                     Can Berkol
 * 11.08.2013
 * **************************************
 * A prepare_pagination()
 *
 * **************************************
 * v 1.0.2                     Can Berkol
 * 11.08.2013
 * **************************************
 * D decrypt_for_session()
 * D encrypt_for_Session()
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * 10.08.2013
 * **************************************
 * A decrypt_for_session()
 * A encrypt_for_session()
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 10.08.2013
 * **************************************
 * A init_defaults()
 * A prepare_url()
 *
 */