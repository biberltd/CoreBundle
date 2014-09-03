<?php
/**
 * ExceptionListener Class
 *
 * This class aims to handle 404 and other HTTP error responses in a customized manner.
 *
 * @vendor      BiberLtd
 * @package		Core
 * @subpackage	Listeners
 * @name	    ExceptionListener
 *
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.0
 * @date        26.02.2014
 *
 */

namespace BiberLtd\Core\Listeners;
use BiberLtd\Core\Core as Core;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpKernel\Event;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
/**
 * Requires MultiLanguageSupportBundle
 */
use BiberLtd\Core\Bundles\MultiLanguageSupportBundle\Services as MLSServices;

class ExceptionListener extends Core{
    /** @var $container             Service container */
    private     $container;
    /** @var $languages             Available languages. */
    private     $languages;
    /**
     * @name            __construct()
     *                  Constructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.3.0
     *
     * @param           string      $timezone
     * @param           array       $params         'key', 'input', 'output'
     */
    public function __construct($kernel){
        parent::__construct($kernel);
    }
    /**
     * @name            __destruct()
     *                  Destructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.3.0
     *
     */
    public function __destruct(){
        foreach($this as $property => $value) {
            $this->$property = null;
        }
    }
    /**
     * @name 			onKernelController()
     *  				Called onKernelController event and handles browser language detection.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.0.0
     *
     * @param 			GetResponseForExceptionEvent 	        $e
     *
     * @return
     *
     */
    public function onKernelException(GetResponseForExceptionEvent  $e){
        $exception = $e->getException();
        $request = $e->getRequest();
        $baseUrl = 'http://'.$request->getHttpHost().$request->getBaseUrl();
        $pathInfo = explode('/', trim($request->getPathInfo(), '/'));
        if(!empty($pathInfo[0])){
            $baseUrl .= '/'.$pathInfo[0];
            $toBeGlued = array ('manage', 'panel');
            if(isset($pathInfo[1]) && in_array($pathInfo[1], $toBeGlued)){
                $baseUrl .= '/'.$pathInfo[1];
            }
            if(method_exists($exception, 'getStatusCode')){
                $statusCode = $exception->getStatusCode();
                switch($statusCode){
                    case '404':
                        $response = new RedirectResponse($baseUrl.'/error/404');
                        break;
                }
                $e->setResponse($response);
            }
        }
    }
}
/**
 * Change Log
 * **************************************
 * v1.0.0                      Can Berkol
 * 26.02.2014
 * **************************************
 * A __construct()
 * A __destruct()
 * A onKernelRequest()
 */