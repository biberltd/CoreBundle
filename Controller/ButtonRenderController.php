<?php
/**
 * ButtonRenderController.php
 *
 * Handles button creations.
 *
 * @vendor         BiberLtd
 * @package        CoreBundle
 * @subpackage     Controller
 * @name           ButtonRenderController
 *
 * @author         Can Berkol
 *
 * @copyright      Biber Ltd. (www.biberltd.com)
 *
 * @version        1.0.3
 * @date           14.11.2014
 *
 */

namespace BiberLtd\Bundle\CoreBundle\Controller;

use BiberLtd\Bundle\CoreBundle\CoreController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

class ButtonRenderController extends  CoreController{

    private $templating;
    private $theme;

    public function __construct(EngineInterface $templating) {
        $this->templating = $templating;
    }
    /**
     * @name            renderAltButton()
     *                  Renders a button view
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @param           array           $buttonDetails
     * @param           array           $core
     *
     * @return          string
     */
    public function renderAltButton($buttonDetails, $core){
        if(func_num_args() != 2){
            $args = func_get_args();
            $buttonDetails = array(
                'name'          => $args[0],
                'icon'          => $args[1],
                'link'          => $args[2],
                'size'          => $args[3],
                'style'         => $args[4],
                'type'          => $args[5],
                'processType'   => $args[6],
                'disabled'      => $args[7],
            );
            $core = $args[8];
        }
        $buttonDetails['alternative'] = true;
        return $this->renderButton($buttonDetails, $core);
    }
    /**
     * @name            renderAltLinkButton()
     *                  Renders a button view with alternative styling.
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->renderButton()
     *
     * @param           array           $buttonDetails
     * @param           array           $core
     *
     * @return          string
     */
    public function renderAltLinkButton($buttonDetails, $core){
        if(func_num_args() != 2){
            $args = func_get_args();
            $buttonDetails = array(
                'name'          => $args[0],
                'icon'          => $args[1],
                'link'          => $args[2],
                'size'          => $args[3],
                'style'         => $args[4],
                'disabled'      => $args[5],
            );
            $core = $args[6];
        }
        $buttonDetails['type'] = 'link';
        $buttonDetails['processType'] = 'button';
        $buttonDetails['alternative'] = true;
        return $this->renderButton($buttonDetails, $core);
    }

    /**
     * @name            renderButton()
     *                  Renders a button view
     *
     * @since           1.0.1
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @param           array       $buttonDetails
     * @param           array       $core
     *
     * @return          string
     */
    public function renderButton($buttonDetails, $core){
        $this->resetRenderResponse();
        if(func_num_args() != 2){
            $args = func_get_args();
            $buttonDetails = array(
                'name'      => $args[0],
                'icon'      => $args[1],
                'link'      => $args[2],
                'size'      => $args[3],
                'style'     => $args[4],
                'type'      => $args[5],
                'processType'=> $args[6],
                'disabled'  => $args[7],
                'alternative'=> $args[8],
            );
            $core = $args[9];
        }
        $defaultButtonDetails = array(
            'alternative'   => false,
            'disabled'      => false,
            'icon'          => '',
            'link'          => '',
            'name'          => '',
            'processType'   => '',
            'size'          => '',
            'style'         => '',
            'type'          => '',
        );
        $buttonDetails = array_merge($defaultButtonDetails, $buttonDetails);
        if(!isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'alternative'               => $buttonDetails['alternative'],
            'disabled'                  => $buttonDetails['disabled'],
            'icon'                      => $buttonDetails['icon'],
            'link'                      => $buttonDetails['link'],
            'name'                      => $buttonDetails['name'],
            'processType'               => $buttonDetails['processType'],
            'style'                     => $buttonDetails['style'],
            'type'                      => $buttonDetails['type'],
        );
        if($buttonDetails['size'] != 'regular'){
            $vars['size'] = $buttonDetails['size'];
        }
        if(isset($buttonDetails['attributes'])){
            $vars['attributes'] = $buttonDetails['attributes'];
        }
        $this->response['html'] =  $this->templating->render('BiberLtdCoreBundle:' . $core['theme'] . '/HtmlParts:button.html.smarty', $vars);
        return $this->response;
    }
    /**
     * @name            renderLargeButton()
     *                  Renders a large button.
     *
     * @since           1.0.1
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->renderButton()
     *
     * @param           array       $buttonDetails
     * @param           array       $core
     *
     * @return          string
     */
    public function renderLargeButton($buttonDetails, $core){
        if(func_num_args() != 2){
            $args = func_get_args();
            $buttonDetails = array(
                'name'          => $args[0],
                'icon'          => $args[1],
                'link'          => $args[2],
                'style'         => $args[3],
                'type'          => $args[4],
                'processType'   => $args[5],
                'disabled'      => $args[6],
                'alternative'   => $args[7],
            );
            $core = $args[8];
        }
        $buttonDetails['size'] = 'lg';
        return $this->renderButton($buttonDetails, $core);
    }
    /**
     * @name            renderLinkButton()
     *                  Renders a button view
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->renderButton()
     *
     * @param           array           $buttonDetails
     * @param           array           $core
     *
     * @return          string
     */
    public function renderLinkButton($buttonDetails, $core){
        if(func_num_args() != 2){
            $args = func_get_args();
            $buttonDetails = array(
                'name'          => $args[0],
                'icon'          => $args[1],
                'link'          => $args[2],
                'size'          => $args[3],
                'style'         => $args[4],
                'disabled'      => $args[6],
                'alternative'   => $args[7],
            );
            $core = $args[8];
        }
        $buttonDetails['type'] = 'link';
        return $this->renderButton($buttonDetails, $core);
    }
    /**
     * @name            renderRegularButton()
     *                  Renders a regular sized button.
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->renderButton()
     *
     * @param           array           $buttonDetails
     * @param           array           $core
     *
     * @return          string
     */
    public function renderRegularButton($buttonDetails, $core){
        if(func_num_args() != 2){
            $args = func_get_args();
            $buttonDetails = array(
                'name'          => $args[0],
                'icon'          => $args[1],
                'link'          => $args[2],
                'style'         => $args[3],
                'type'          => $args[4],
                'processType'   => $args[5],
                'disabled'      => $args[6],
                'alternative'   => $args[7],
            );
            $core = $args[8];
        }

        $buttonDetails['size'] = 'regular';
        return $this->renderButton($buttonDetails, $core);
    }
    /**
     * @name            renderSmallButton()
     *                  Renders a small sized button.
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->renderButton()
     *
     * @param           array           $buttonDetails
     * @param           array           $core
     *
     * @return          string
     */
    public function renderSmallButton($buttonDetails, $core){
        if(func_num_args() != 2){
            $args = func_get_args();
            $buttonDetails = array(
                'name'          => $args[0],
                'icon'          => $args[1],
                'link'          => $args[2],
                'style'         => $args[3],
                'type'          => $args[4],
                'processType'   => $args[5],
                'disabled'      => $args[6],
                'alternative'   => $args[7],
            );
            $core = $args[8];
        }
        $buttonDetails['size'] = 'sm';
        return $this->renderButton($buttonDetails, $core);
    }
    /**
     * @name            renderSubmitButton()
     *                  Renders a submit button view
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @param           array           $buttonDetails
     * @param           array           $core
     *
     * @return          string
     */
    public function renderSubmitButton($buttonDetails, $core){
        if(func_num_args() != 2){
            $args = func_get_args();
            $buttonDetails = array(
                'name'          => $args[0],
                'icon'          => $args[1],
                'size'          => $args[2],
                'style'         => $args[3],
                'type'          => $args[4],
                'disabled'      => $args[6],
                'alternative'   => $args[7],
            );
            $core = $args[8];
        }
        $buttonDetails['size'] = 'sm';
        return $this->renderButton($buttonDetails, $core);
    }
    /**
     * @name            renderXSmallButton()
     *                  Renders an x-small sized button.
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->renderButton()
     *
     * @param           array           $buttonDetails
     * @param           array           $core
     *
     * @return          string
     */
    public function renderXSmallButton($buttonDetails, $core){
        if(func_num_args() != 2){
            $args = func_get_args();
            $buttonDetails = array(
                'name'          => $args[0],
                'icon'          => $args[1],
                'link'          => $args[2],
                'style'         => $args[3],
                'type'          => $args[4],
                'processType'   => $args[5],
                'disabled'      => $args[6],
                'alternative'   => $args[7],
            );
            $core = $args[8];
        }
        $buttonDetails['size'] = 'xs';
        return $this->renderButton($buttonDetails, $core);
    }
    /**
     * @name            _mergeDefaultClass()
     *                  Helper function to merger default class with an array of extra classes
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           string          $defaultClass
     * @param           array           $classes        List of class names.
     *
     * @return          array
     */
    private function _mergeDefaultClass($defaultClass, $classes = array()){
        if(!is_array($classes)){
            $classes = array();
        }
        $defaultClasses = array($defaultClass);
        $classes = array_merge($defaultClasses, $classes);

        return $classes;
    }
}

/**
 * Change Log:
 * **************************************
 * v1.0.0                      Can Berkol
 * 14.10.2014
 * **************************************
 * Namespaces fixed.
 *
 * **************************************
 * v1.0.2                      Can Berkol
 * 30.05.2014
 * **************************************
 * Methods now return a unified $response array.
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * 23.04.2014
 * **************************************
 * U renderButton()
 * Also all functions now accept only one parameter: 1 array
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 23.04.2014
 * **************************************
 * A renderAltButton()
 * A renderAltLinkButton()
 * A renderButton()
 * A renderLargeButton()
 * A renderLinkButton()
 * A renderRegularButton()
 * A renderSmallButton()
 * A renderSubmitButton()
 * A renderXSmallButton()
 */