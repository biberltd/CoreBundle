<?php
/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        10.12.2015
 */
namespace BiberLtd\Bundle\CoreBundle\Listeners;
use BiberLtd\Bundle\CoreBundle\Core as Core;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpKernel\Event;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

use BiberLtd\Bundle\MultiLanguageSupportBundle\Services as MLSServices;

class ExceptionListener extends Core{
    /** @var $container             Service container */
    private     $container;
    /** @var  array */
    private     $languages;

    /**
     * ExceptionListener constructor.
     *
     * @param $kernel
     */
    public function __construct($kernel){
        parent::__construct($kernel);
    }

    /**
     * Destructor
     */
    public function __destruct(){
        foreach($this as $property => $value) {
            $this->$property = null;
        }
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $e
     */
    public function onKernelException(GetResponseForExceptionEvent $e){
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