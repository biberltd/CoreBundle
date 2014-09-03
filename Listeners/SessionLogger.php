<?php
/**
 * SessionLogger Class
 *
 * This class handles session creation.
 *
 * @vendor      BiberLtd
 * @package		Core
 * @subpackage	Services
 * @name	    SessionLogger
 *
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.1
 * @date        29.05.2014
 *
 */

namespace BiberLtd\Core\Listeners;
use BiberLtd\Core\Core as Core;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpKernel\Event;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
/**
 * Requires MultiLanguageSupportBundle
 */
use BiberLtd\Core\Bundles\MultiLanguageSupportBundle\Services as MLSServices;

class SessionLogger extends Core{
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
     * @version         1.0.1
     *
     * @param           object      $container
     * @param           object      $kernel
     * @param           array       $db_options
     */
    public function __construct($container, $kernel, $db_options = array('default', 'doctrine')){
        parent::__construct($kernel);
        $this->container = $container;
        $this->timezone = $kernel->getContainer()->getParameter('app_timezone');
    }
    /**
     * @name            __destruct()
     *                  Destructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.1
     *
     */
    public function __destruct(){
        foreach($this as $property => $value) {
            $this->$property = null;
        }
    }
    /**
     * @name 			onKernelRequest()
     *  				Called onKernelRequest event and registers a new session if necessary.
     *
     * @author          Can Berkol
     *
     * @use             BiberLtd\Core\Services\SessionManager
     *
     * @since			1.0.0
     * @version         1.0.0
     *
     * @param 			GetResponseEvent 	        $e
     *
     */
    public function onKernelRequest(GetResponseEvent $e){
        $sm = $this->container->get('session_manager');
        $sm->register();
    }
}
/**
 * Change Log
 * **************************************
 * v1.0.0                      Can Berkol
 * 09.01.2014
 * **************************************
 * A __construct()
 * A __destruct()
 * A onKernelRequest()
 */