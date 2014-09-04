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
 * @version     1.3.1
 * @date        11.07.2014
 *
 */

namespace BiberLtd\CoreBundle;

use BiberLtd\Core\Bundles\FileManagementBundle\Entity\File;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CoreController extends Controller {

    protected $url;         /** Array Base urls.  */
    protected $session = null;     /** Session */
    protected $translator;  /** Translator */
    protected $av;          /** Access validator */
    protected $iv;          /** Input validator */
    protected $sm;          /** Session manager */
    protected $language;    /** Current locale's language entity. */
    protected $locale;      /** Current locale */
    protected $site;        /** Current site */
    protected $page;        /** Array that contains current page details */
    protected $flash;       /** Flash messages */
    protected $renderResponse; /** Render response */
    protected $vars;        /** vars to use in template */
    protected $head;        /** Holds details of head: css, js */
    protected $body;        /** Holds details of body: js, classes */
    protected $timezone;    /** new \DateTimeZone identifies app time zone */
    protected $previousUrl; /** stores previous url */

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
     * @version             1.3.0
     *
     * @param               integer         $siteId
     * @param               string          $pageCode
     * @param               string          $theme
     */
    public function init($siteId, $pageCode = null, $theme = null){
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
        $this->timezone = new \DateTimeZone($this->container->getParameter('app_timezone'));
        if(is_null($this->session)){
            $this->session = $this->get('session');
        }
        $this->locale =  $this->session->get('_locale');
        $this->translator = $this->get('translator');
        $this->translator->setLocale($this->locale);
        $this->av = $this->get('access_validator');
        $this->iv = $this->get('input_validator');
        $this->sm = $this->get('session_manager');

        /** Get current language */
        $mlsModel = $this->get('multilanguagesupport.model');
        $response = $mlsModel->getLanguage($this->locale, 'iso_code');
        $this->language = false;
        if(!$response['error']){
            $this->language = $response['result']['set'];
        }
        unset($response);
        /** Get current site */
        $siteModel = $this->get('sitemanagement.model');
        $response = $siteModel->getSite($siteId, 'id');
        $this->site = false;
        if(!$response['error']){
            $this->site = $response['result']['set'];
        }
        unset($response);
        if(!is_null($pageCode)){
            /** Get current page */
            $cmsModel = $this->get('cms.model');
            $response = $cmsModel->getPage($pageCode, 'code');
            $this->page['entity'] = false;
            if(!$response['error']) {
                $this->page['entity'] = $response['result']['set'];
                $this->theme = $this->page['entity']->getLayout()->getTheme()->getFolder();
            }
            /** Get page blocks */
            $response = $cmsModel->listModulesOfPageLayoutsGroupedBySection($this->page['entity'], array('sort_order' => 'asc'));
            if ($response['error']) {
                /** Show error if no modules can be loaded */
                echo $this->translator->trans('msg.error.modules', array(), 'manage');
                exit;
            }
            $this->page['blocks'] = $response['result']['set'];
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
        $this->flash = $this->prepareFlash($this->session);
        if(!is_null($pageCode) && strpos($this->get('request')->getPathInfo(), '|-') === false){
            $this->get('session')->set('previousUrl', $this->get('request')->getPathInfo());
        }
    }
    /**
     * @name            initDefaults()
     *                  Initializes default values.
     *
     * @author          Can Berkol
     * @since           1.1.1
     * @version         1.1.9
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
        $defaults = array(
            /** @deprecated assets. */
            'assets' => array(
                'url' => array(
                    'theme' => $this->url['domain'] . '/themes/' . $theme . '/',
                ),
            ),
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
    /**
     * @name            init_defaults()
     *                  Initializes default values.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.1.1
     *
     * @use             $this->initDefaults()
     *
     * @param           array       $css
     * @param           array       $js
     * @param           array       $vars
     * @param           string      $theme
     *
     * @deprecated      Will be deleted in v1.3.0. Use initDefaults instead.
     *
     * @return          mixed
     */
    public function init_defaults($css = null, $js = null, $vars = null, $theme = null) {
        return $this->initDefaults($css, $js, $vars, $theme);
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
     * @since           1.0.0
     * @version         1.1.1
     *
     * @param               mixed       $var        Content of variable
     * @param               bool        $exit       true|false
     * @param               string      $type       dump|print|echo
     *
     */
    public function debug($var, $exit = true, $type = 'dump') {
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
     * prints provided class methods
     *
     * @author          Said İmamoğlu
     *
     * @param mixed $class Class
     * @param bool $exit true|false
     * @since           1.1.3
     * @version         1.1.3
     *
     */
    public function debugClass($class, $exit = true) {
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
     *                  Generates a unique hash for cross site foregary attack prevention.
     *
     * @author          Can Berkol
     * @since           1.2.5
     * @version         1.2.5
     *
     * @return          array           $flash
     */
    public function generateXssCode(){
        if(!isset($this->session)){
            $this->session = $this->container->get('session');
        }
        $currentXssCode = $this->session->get('_xss');
        $currentXssTime = $this->session->get('_xss_timestamp');

        /** @deprecated use _xss in the future, will be removed in v1.3.0 */
        if(is_null($currentXssCode) || $currentXssCode == ''){
            $currentXssCode = $this->session->get('_csfr');
        }
        if(is_null($currentXssTime) || $currentXssTime == ''){
            $currentXssTime = $this->session->get('_csfr_timestamp');
        }

        $ip = $this->container->get('request')->getClientIp();
        $now = time();
        if (!$currentXssTime || !$currentXssCode) {
            $currentXssCode = md5($ip . $now);
            $this->session->set('_xss', $currentXssCode);
            $this->session->set('_xss_timestamp', $now);
            /** @deprecated use _xss in the future, will be removed in v1.3.0 */
            $this->session->set('_csfr', $currentXssCode);
            $this->session->set('_csfr_timestamp', $now);
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
            /** @deprecated use _xss in the future, will be removed in v1.3.0 */
            $this->session->set('_csfr', md5($ip . $now));
            $this->session->set('_csfr_timestamp', $now);
        }
        else{
            /** If the last request happenned earliear than 10 minutes just update timestamp and increase life span of hash code. */
            $this->session->set('_xss_timestamp', $now);
            /** @deprecated use _xss in the future, will be removed in v1.3.0 */
            $this->session->set('_csfr_timestamp', $now);
        }
        return $currentXssCode;
    }
    /**
     * @name            generateCSFR()
     *
     * @author          Can Berkol
     * @since           1.0.6
     * @version         1.2.5
     *
     * @param           Session         $session
     *
     * @return          array           string
     *
     * @deprecated      use $this->generateXssCode() instead !! Will be removed in version 1.3.0
     */
    public function generateCSFR($session) {
        unset($session);
        return $this->generateXssCode();
    }

    /**
     * @name            generateUrlKey()
     *                  Generates url keys / slugs.
     *
     * @author          Can Berkol
     * @since           1.0.7
     * @version         1.0.7
     *
     * @param           string          $translate
     *
     * @return          string
     */
    public function generateUrlKey($translate) {
        $dictionaries = array(
            'upper' => array(
                'Ç' => 'C',
                'I' => 'I',
                'İ' => 'I',
                'Ö' => 'O',
                'Ş' => 'S',
                'Ü' => 'U',
                'Ğ' => 'G',
                '.' => '_',
                ' ' => '_',
                '/' => '_',
                '\\' => '_',
                '*' => '_',
                '+' => '_',
                '%' => '_',
                '#' => '_',
                '{' => '_',
                '}' => '_',
                ',' => '_',
                ';' => '_',
                '?' => '_',
                '!' => '_',
                ':' => '_',
                '=' => '_',
                '$' => '_',
                '"' => '_',
                "'" => '_',
                '`' => '_',
                '¨' => '_',
                '(' => '_',
                ')' => '_',
                '[' => '_',
                ']' => '_',
                '*' => '_',
                '|' => '_',
                '<' => '_',
                '>' => '_',
                'é' => 'e',
                'Б' => 'B',
                'Г' => 'H',
                'Ґ' => 'G', 'Д' => 'D', 'Є' => 'E', 'Ж' => 'ZH,', 'З' => 'Z', 'И' => 'Y',
                'Ї' => 'YI', 'Й' => 'J', 'Л' => 'L', 'П' => 'P', 'У' => 'U', 'Ф' => 'F',
                'Ц' => 'TS', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SHCH', 'Ь' => '_', 'Ю' => 'yu',
                'Я' => 'ya'
            ),
            'lower' => array(
                'ç' => 'c',
                'ı' => 'i',
                'i' => 'i',
                'ö' => 'o',
                'ş' => 's',
                'ü' => 'u',
                'ğ' => 'g',
                '.' => '_',
                ' ' => '_',
                '/' => '_',
                '\\' => '_',
                '*' => '_',
                '+' => '_',
                '%' => '_',
                '#' => '_',
                '{' => '_',
                '}' => '_',
                ',' => '_',
                ';' => '_',
                '?' => '_',
                '!' => '_',
                ':' => '_',
                '=' => '_',
                '$' => '_',
                '"' => '_',
                "'" => '_',
                '`' => '_',
                '¨' => '_',
                '(' => '_',
                ')' => '_',
                '[' => '_',
                ']' => '_',
                '*' => '_',
                '|' => '_',
                '<' => '_',
                '>' => '_',
                'é' => 'e',
                'б' => 'b',
                'в' => 'v',
                'г' => 'h',
                'ґ' => 'g','д' => 'd', 'є' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'y',
                'ї' => 'yi', 'й' => 'j', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'п' => 'p',
                'ф' => 'F', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ь' => '_',
                'ю' => 'yu', 'я' => 'ya',
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
     *
     * @return              mixed       bool|RedirectResponse
     *
     */
    public function ifAccessNotGranted($action, $redirect = false){
        if(!$this->av->isActionGranted($action)){
            if($redirect){
                return $this->redirectWithMessage('danger', $this->translator->trans('msg.error.insufficient.rights', array(), 'core'), $redirect);
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
     * @version             1.2.2
     *
     * @param               mixed       $redirect   false | string containing route definition
     *
     * @return              mixed       bool|RedirectResponse
     *
     */
    public function ifLoggedin($redirect = false){
        $access_map = array(
            'unmanaged' => false,
            'guest' => false,
            'authenticated' => true,
            'members' => array(),
            'groups' => array(),
            'status' => array('a')
        );
        if ($this->av->has_access(null, $access_map)) {
            $this->sm->logAction('page.visit.fail.insufficient.rights', 1, array('route' => '/manage/account/login'));
            if(!$redirect){
                return true;
            }
            return $this->redirect($redirect, true);
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
     * @version             1.2.1
     *
     * @param               mixed       $redirect   false | string containing route definition
     * @param               array       $managers
     *
     * @return              mixed       bool|RedirectResponse
     *
     */
    public function ifLoggedinManager($redirect = false, $managers = array()){
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
        if ($this->av->has_access(null, $access_map)) {
            $this->sm->logAction('page.visit.fail.insufficient.rights', 1, array('route' => '/manage/account/login'));
            if(!$redirect){
                return true;
            }
            return $this->redirect($redirect, true);
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
     * @version             1.2.6
     *
     * @param               mixed       $redirect   false | string containing route definition
     *
     * @return              mixed       bool|RedirectResponse
     *
     */
    public function ifNotManager($redirect = false){
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
        if (!$this->av->has_access(null, $access_map) && !$this->av->isActionGranted('manage.access')) {
            $this->sm->logAction('page.visit.fail.insufficient.rights', 1, array('route' => '/manage/dashboard'));
            if(!$redirect){
                return true;
            }
            return $this->redirect($redirect, true);
        }
        return false;
    }

    /**
     * @name                ifNotMember()
     *                      Wrapper to check if the user is a manager and has access rights to manage/ controllers.
     *
     * @author              Can Berkol
     *
     * @since               1.1.1
     * @version             1.1.1
     *
     * @param               mixed       $redirect   false | string containing route definition
     *
     * @return              mixed       bool|RedirectResponse
     *
     */
    public function ifNotMember($redirect = false){
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
        if (!$this->av->has_access(null, $access_map)) {
            $this->sm->logAction('page.visit.fail.insufficient.rights', 1, array('route' => '/hesap/giris'));
            if(!$redirect){
                return true;
            }
            return $this->redirect($redirect, false);
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
     * @version         1.1.1
     *
     * @param           bool            $append_locale      If set to true appends current locale.
     * @param           bool            $prepend_boot
     * @param           mixed           $parameters         Either a string or an array of strings.
     *
     * @return          string          $url
     */
    public function prepareUrl($append_locale = true, $prepend_boot = false, $parameters = null) {
        $url = '';
        $host = $this->get('request')->getHttpHost();
        $protocol = 'http://';
        if ($this->get('request')->isSecure()) {
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
     * @name            prepare_url()
     *                  Prepares URLs.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.1.1
     *
     * @deprecated      Will be deleted in v1.2.0. Use prepareUrl instead.
     *
     * @param           bool            $append_locale      If set to true appends current locale.
     * @param           mixed           $parameters         Either a string or an array of strings.
     *
     * @return          string          $url
     */
    public function prepare_url($append_locale = true, $prepend_boot = false, $parameters = null) {
        return $this->prepareUrl($append_locale, $prepend_boot, $parameters);
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
     * @name            prepare_flash()
     *                  Prepares Flash messages.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.1.1
     *
     * @deprecated      Will be deleted in v1.2.0. Use prepareFlash instead.
     *
     * @param           Session         $session
     *
     * @return          array           $flash
     */
    public function prepare_flash($session) {
        return $this->prepareFlash($session);
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
            if(strpos($item, 'http') !== false){
                $jsStr .= '<script type="text/javascript" src="'.$item.'"></script>'.PHP_EOL;
            }
            else{
                $jsStr .= '<script type="text/javascript" src="'.$this->url['domain'].'/themes/'.$this->theme.$item.'"></script>'.PHP_EOL;
            }
        }
        return $jsStr;
    }
    /**
     * @name            prepareManagementSidebar()
     *                  Prepares management sidebar.
     *
     * @author          Can Berkol
     * @since           1.1.0
     * @version         1.1.0
     *
     * @param           string          $parentNavUrlKey
     * @deprecated     Will be removed şn v2.0.0
     *
     * @return          string
     */
    public function prepareManagementSidebar($parentNavUrlKey = null){
        $cmsModel = $this->get('cms.model');
        /**
         * Get core render model and prepare core information
         */
        $coreRender = $this->get('corerender.model');
        $core = array(
            'locale' => $this->locale,
            'theme' => $this->page['entity']->getLayout()->getTheme()->getFolder(),
            'url' => $this->url,
        );
        /** Get project logo */
        $siteSettings = json_decode($this->site->getSettings());
        $projectLogoUrl = $this->url['cdn'].'/site/logo/'.$siteSettings->logo;
        $dashboardSettings = array(
            'link'  => $this->url['base_l'].'/manage/dashboard',
            'title' => $this->translator->trans('dashboard.title', array(), 'admin'),
        );
        $renderedProjectLogo = $coreRender->renderProjectLogo($projectLogoUrl, $this->site->getTitle(), $core, $dashboardSettings);
        unset($projectLogoUrl, $dashboardSettings);
        $sidebar['projectLogo'] = $renderedProjectLogo;
        /** Prepare sidebar separator */
        $renderedSidebarSeparator = $coreRender->renderSidebarSeparator($core);
        $sidebar['separator'] = $renderedSidebarSeparator;
        /** Get sidebar navigation */
        $response = $cmsModel->listItemsOfNavigation('cms_nav_main', 'top', array('sort_order' => 'asc'));
        $sideNavItems = array();
        if(!$response['error']){
            $sideNavItems = $response['result']['set'];
        }
        unset($response);
        $navCollection = array();
        foreach($sideNavItems as $navItem){
            $response = $cmsModel->listNavigationItemsOfParent($navItem, array('sort_order' => 'asc'));
            $childItems = array();
            $hasChildren = false;
            $selectedParent = false;
            if(!$response['error']){
                $hasChildren = true;
                foreach($response['result']['set'] as $childItem){
                    $childNavSelected = false;
                    if($childItem->getPage()->getId() == $this->page['entity']->getId()){
                        $childNavSelected = true;
                        $selectedParent = $childItem->getParent()->getId();
                    }
                    $childItems[] = array(
                        'entity'  => $childItem,
                        'selected'=> $childNavSelected,
                    );
                }
            }
            $navSelected = false;
            if(is_null($parentNavUrlKey)){
                if($navItem->getId() == $selectedParent){
                    $navSelected = true;
                }
            }
            else{
                foreach($navItem->getLocalizations() as $localization){
                    if($localization->getUrlKey() == $parentNavUrlKey){
                        $navSelected = true;
                        break;
                    }
                }
            }
            $navCollection[]  = array(
                'children'      => $childItems,
                'code'          => time(),
                'entity'        => $navItem,
                'hasChildren'   => $hasChildren,
                'selected'      => $navSelected,
            );
            unset($response, $childItems);
        }
        unset($sideNavItems);

        $renderedSidebarNavigation = $coreRender->renderSidebarNavigation($navCollection, $core);
        $sidebar['navigation'] = $renderedSidebarNavigation;
        return $sidebar;
    }
    /**
     * @name            prepareManagementTopbar()
     *                  Prepares management topbar.
     *
     * @author          Can Berkol
     * @since           1.1.0
     * @version         1.1.8
     *
     * @deprecated     Will be removed şn v2.0.0
     *
     * @return          string
     */
    public function prepareManagementTopbar(){
        $cmsModel = $this->get('cms.model');
        $mlsModel = $this->get('multilanguagesupport.model');
        /**
         * Get core render model and prepare core information
         */
        $coreRender = $this->get('corerender.model');
        $core = array(
            'locale' => $this->locale,
            'theme' => $this->page['entity']->getLayout()->getTheme()->getFolder(),
            'url' => $this->url,
        );
        /** Get top navigation */
        $response = $cmsModel->listItemsOfNavigation('cms_nav_top', 'top', array('sort_order' => 'asc'));
        $topNavItems = array();
        if(!$response['error']){
            $topNavItems = $response['result']['set'];
        }
        $topNavigation = $coreRender->renderQuickActionsNavigation($topNavItems, $core);
        unset($topNavItems, $response);
        $topbar['navigation'] = $topNavigation;
        unset($topNavigation);
        /** Create language dropdown */
        $response = $mlsModel->listAllLanguages();
        $otherLanguages = array();
        $currentLanguage = null;
        if(!$response['error']) {
            $allLanguages = $response['result']['set'];
            foreach($allLanguages as $language){
                if($language->getIsoCode() == $this->locale){
                    $currentLanguage = $language;
                }
                else{
                    $otherLanguages[] = $language;
                }
            }
            unset($allLanguages, $language);
        }
        $pathInfo = str_replace($this->locale.'/', '', $this->get('request')-> getPathInfo());
        $langDropDown = $coreRender->renderLanguageDropdown($currentLanguage, $otherLanguages, $core, $pathInfo);
        $topbar['navigationLang'] = $langDropDown;
        unset($langDropDown);

        return $topbar;
    }
    /**
     * @name            preparePagination()
     *                  Prepares pagination.
     *
     * @author          Can Berkol
     *                  Mehmet Aydın Bahadır
     *                  Said İmamoğlu
     * @since           1.1.1
     * @version         1.2.4
     *
     * @param           integer         $page       Current page number
     * @param           integer         $total      Total number of items found in database / collection
     * @param           integer         $limit
     * @param           integer         $maxPage
     *
     * @return          array           $pagination
     */
    public function preparePagination($page, $total, $limit, $maxPage = false) {
        /**
         * Calculate number of pages.
         */
        $number_of_pages = 0;
        if ($total % $limit > 0) {
            $number_of_pages = round($total / $limit);
        } else {
            $number_of_pages = round($total / $limit);
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
     * @name            prepare_pagination()
     *                  Prepares pagination.
     *
     * @author          Can Berkol
     *                  Mehmet Aydın Bahadır
     * @since           1.0.3
     * @version         1.1.1
     *
     * @deprecated      Will be deleted in v1.2.0. Use preparePagination instead.
     *
     * @param           integer         $page       Current page number
     * @param           integer         $total      Total number of items found in database / collection
     * @param           integer         $limit
     *
     * @return          array           $pagination
     */
    public function prepare_pagination($page, $total, $limit) {
        return $this->preparePagination($page, $total, $limit);
    }
    /**
     * @name            redirect()
     *                  Redirects controller to a specific url.
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @since           1.1.1
     * @version         1.2.9
     *
     * @param           string          $to         either a route definition or a specific shortcut key.
     *                                              currently available keys are:
     *                                              404
     *                                              dashboard
     *                                              login
     * @param           bool            $backend    is set to true adds manage prefix to route
     *
     * @return          RedirectResponse
     */
    public function redirect($to = '404', $backend = false) {
        $route = '';
        $url = $this->url['base_l'];
        if($backend){
            $url = $this->url['manage'];
        }
        switch($to){
            case '404':
                $route .= '/error/404';
                break;
            case 'dashboard':
                $route .= '/dashboard';
                break;
            case 'login':
                $route .= '/account/login';
                break;
            default:
                $route .= $to;
        }
        $url .= $route;
        if (strpos($to,'http://')!== FALSE || strpos($to,'https://')!== FALSE) {
            echo 1;
            $url = $route;
        }
        return new RedirectResponse($url);
    }
    /**
     * @name            redrectWithMessage()
     *                  Redirects controller to a specific url with flash message.
     *
     * @author          Can Berkol
     * @since           1.1.7
     * @version         1.1.7
     *
     * @param           string              $msgType
     * @param           string              $msgContent
     * @param           string              $to
     * @param           bool                $backend
     * @param           mixed               $optional
     *
     * @return          RedirectResponse
     */
    public function redirectWithMessage($msgType, $msgContent, $to = '404', $backend = false, $optional = null) {
        $response = $this->redirect($to, $backend);
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
     * @version         1.2.7
     *
     * @return          string
     */
    public function renderPage(){
        if(func_num_args() > 1){
            $args = func_get_args();
            return $this->legacyRenderPage($args);
        }
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
        $this->vars['body'] = $this->body;
        $this->vars['head'] = $this->head;
        if(!isset($this->vars['locale'])){
            $this->vars['locale'] = $this->locale;
        }
        if(!isset($this->head['css'])){
            $this->head['css'] = '/css/styles.min.css';
        }
        $this->vars['body']['classes'] = array_unique($this->vars['body']['classes']);
        $this->vars['head']['css'] = array_merge($this->head['css'], $this->vars['head']['css']);
        $this->vars['body']['js'] = $this->prepareJs($this->vars['body']['js']);
        $this->vars['head']['js'] = $this->prepareJs($this->vars['head']['js']);
        $this->vars['head']['css'] = $this->prepareCss($this->vars['head']['css']);
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
        $this->response['body']['js'] = array();
        $this->response['html'] = '';
        $this->response['head']['css'] = array();
        $this->response['head']['js'] = array();
        return $this;
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
     * @since           1.0.5
     * @version         1.2.1
     *
     * @param           string          $theme
     * @param           string          $backend
     *
     * @return          mixed
     */
    public function setURLs($theme = 'cms', $backend = 'manage') {
        if ($this->get('kernel')->getEnvironment() == 'dev') {
            $url['base_l'] = $this->prepareUrl(true, true);
        } else {
            $url['base_l'] = $this->prepareUrl(true, false);
        }
        if ($this->get('kernel')->getEnvironment() == 'dev') {
            $url['base'] = $this->prepareUrl(false, true);
        } else {
            $url['base'] = $this->prepareUrl(false, false);
        }
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
     * @name            legacyRenderPage()
     *                  This is the legacy renderPage function for older versions of core.
     *
     * @author          Can Berkol
     * @since           1.2.2
     * @version         1.2.2
     *
     * @deprecated      Will be deleted in v1.2.0 - Use the new $this->renderPage() instead.
     *
     * @return          string
     */
    private function legacyRenderPage($args){
        if(count($args) == 3){
            $var = $args[0];
            $css = $args[1];
            $js = $args[2];
        }
        else if (count($args) == 4){
            $var = $args[0];
            $css = $args[1];
            $js = $args[2];
            $var['meta'] = $args[3];
        }
        $defaultVar['flash']   = $this->flash;
        $defaultVar['page']    = array(
            'blocks'        => $this->page['blocks'],
            'entity'        => $this->page['entity'],
        );

        $defaultVar['page']['meta'] = array(
            'description'   => $this->page['entity']->getLocalization($this->locale)->getMetaDescription(),
            'keywords'      => $this->page['entity']->getLocalization($this->locale)->getMetaKeywords(),
            'title'         => $this->page['entity']->getLocalization($this->locale)->getTitle(),
        );

        $defaultVar['style']   = array(
            'body'          => array(
                'classes'       => array(),
            ),
        );
        $defaultVar['site']   = array(
            'entity'            => $this->site,
            'name'              => $this->site->getTitle(),
        );
        foreach($var as $key => $content){
            switch($key){
                case 'page':
                    if(isset($var['page']['blocks'])){
                        $defaultVar['page']['blocks'] = array_merge($defaultVar['page']['blocks'], $content['blocks']);
                    }
                    if(isset($var['page']['entity'])){
                        $defaultVar['page']['entity'] = array_merge($defaultVar['page']['entity'], $content['entity']);
                    }
                    if(isset($var['page']['meta'])){
                        $defaultVar['page']['meta'] = array_merge($defaultVar['page']['meta'], $content['meta']);
                    }
                    if(isset($var['page']['form'])){
                        $defaultVar['page']['form'] = $content['form'];
                    }
                    break;
                case 'style':
                    $defaultVar['style'] = array_merge($defaultVar['style'], $content);
                    break;
                default:
                    $defaultVar[$key] = $content;
            }
        }
        if(isset($this->vars) && is_array($this->vars)){
            $defaultVar = array_merge($defaultVar, $this->vars);
        }

        $tags = $this->initDefaults($css, $js, $defaultVar, $this->page['entity']->getLayout()->getTheme()->getFolder());
        return $this->render($this->page['entity']->getBundleName().':'.$this->page['entity']->getLayout()->getTheme()->getFolder().'/Pages:'.$this->page['entity']->getCode().'.html.smarty', $tags);
    }

    /**
     * @name        fileUpload()
     *
     * @author      Said İmamoğlu
     * @since       1.2.0
     * @version     1.2.0
     *
     * @param       $file
     * @param       $folder
     * @param       $db
     * @param       $returnUrl
     * @param       $fileTypes
     *
     * @return      string file name
     *
     * @deprecated  File upload handling is not the job of the CoreController. Will be deleted in v1.3.0 !!!
     *
     */
    public function fileUpload($file,$folder,$db,$returnUrl , $fileTypes = array('jpg', 'jpeg', 'png', 'bmp')){
        /** @var  string $rootDir gets Root Directory of project */
        $rootDir = $this->get('kernel')->getRootDir();
        /** @var object $FMM calls file management model*/
        $FMM = $this->get('filemanagement.model');
        /** If $folder is string then set $folderPath to $folder (Ex: /cdn/product_images ) */

        if (is_string($folder)) {
             $folderPath = $folder;
        }
        /**
         * If folder is array then get folder from database
         * $folder  = array('folder'=>1,'by'=>'id')
         */
        elseif(is_array($folder)){
            $response = $FMM->getFileUploadFolder($folder['folder'], $folder['by']);
            if ($response['error']) {
                return $this->redirectWithMessage('danger', $this->translator->trans('msg.error.notfound.folder', array(), 'manage'), $returnUrl, true);
            }
            $folderEntity = $response['result']['set'];
            $folderPath  = $folderEntity->getPathAbsolute();
            unset($response);
        }
        /** @var string $destinationFolder Merge rootDir and folderPath */
        $destinationFolder = rtrim($rootDir . '/../www' . $folderPath, "/");
        if (!$file instanceof UploadedFile) {
            return $this->redirectWithMessage('danger', $this->translator->trans('msg.error.fileObject', array(), 'manage'), $returnUrl, true);
        }

        $origName = $file->getClientOriginalName();
        /** @var array $nameArray explode file name to get file type*/
        $nameArray = explode('.', $origName);
        $fileType = $nameArray[count($nameArray) - 1];
        $fileName = strlen($nameArray[0])>32 ? substr($nameArray[0],0,30) : $nameArray[0].'_'.md5($origName . time());
        $fileSize = $file->getSize();
        $newFileFullName = $fileName . '.' . $fileType;
        $mimeType = $file->getClientMimeType();
        /** Check file types */
        if (!in_array(strtolower($fileType), $fileTypes)) {
            return $this->redirectWithMessage('danger', $this->translator->trans('msg.error.fileType', array(), 'manage'), $returnUrl, true);
        }
        /** Move file to destination folder */
        $file->move($destinationFolder, $newFileFullName);
        /**
         * SAVING FILE INFO TO DB
         */

        if ($db === true) {
            $fileEntity = new \stdClass();
            $fileEntity->name = $newFileFullName;
            $fileEntity->url_key = $fileName;
            $fileEntity->source_original = $newFileFullName;
            $fileEntity->source_preview = $newFileFullName;
            $fileEntity->type = 'i';
            $fileEntity->folder = $folderEntity>getId();
            $fileEntity->site = 1;
            $fileEntity->size = $fileSize;
            $fileEntity->mime_type = $mimeType;
            $response = $FMM->insertFile($fileEntity);
            if ($response['error']) {
                return $this->translator->trans('msg.error.failed.insert',array(),'manage');
            }
            $fileEntity = $response['result']['set'][0];
        } else{
            $fileEntity = new File();
            $fileEntity->SetName($newFileFullName);
            $fileEntity->SetUrlKey($fileName);
            $fileEntity->SetSourceOriginal($newFileFullName);
            $fileEntity->SetSourcePreview($newFileFullName);
            $fileEntity->SetType('i');
            $fileEntity->SetFolder($folderEntity->getId());
            $fileEntity->SetSite(1);
            $fileEntity->SetSize($fileSize);
            $fileEntity->SetMimeType($mimeType);
        }
        /**
         * Prepare & Return Response
         */
        return array(
            'rowCount' =>1,
            'result' => array(
                'set' => $fileEntity,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.delete.done',
        );
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
}
/**
 * Change Log
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