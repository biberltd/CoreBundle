<?php
/**
 * WidgetRenderController.php
 *
 * Handles all article class related renderings.
 *
 * @vendor         BiberLtd
 * @package        CoreBundle
 * @subpackage     Controller
 * @name           WidgetRenderController
 *
 * @author         Can Berkol
 *
 * @copyright      Biber Ltd. (www.biberltd.com)
 *
 * @version        1.0.6
 * @date           02.05.2014
 *
 */

namespace BiberLtd\Bundle\CoreBundle\Controller;

use BiberLtd\Bundle\CoreBundle\CoreController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

class WidgetRenderController extends CoreController{

    private $templating;
    private $kernel;
    private $theme;
    private $render;

    public function __construct(EngineInterface $templating, $kernel) {
        $this->templating = $templating;
        $this->kernel = $kernel;
        /** Register required renders */
        $this->render = new \stdClass();
        $this->render->typography = $this->kernel->getContainer()->get('typographyrender.model');
    }
    /**
     * @name            renderBreadcrumbNavigation()
     *                  Renders breadcrumb navigation
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Can Berkol
     *
     * @param           array           $breadcrumbItems
     * @param           array           $core
     *
     * @return          array           $response
     */
    public function renderBreadcrumbNavigation($breadcrumbItems, $core = null){
        $this->resetRenderResponse();
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'crumbs'       => $breadcrumbItems,
        );

        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Widgets:breadcrumb.html.smarty', $vars);

        return $this->response;
    }
    /**
     * @name            renderCenteredLoginForm()
     *                  Renders centered login form
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           string          $action
     * @param           string          $method
     * @param           array           $app
     * @param           array           $link
     * @param           array           $settings
     * @param           array           $msg
     * @param           array           $core
     *
     * @return          array           $response
     */
    public function renderCenteredLoginForm($action = null, $method = 'post', $app = array(), $link = array(), $settings = array(), $msg = array(), $core = null){
        $this->resetRenderResponse();
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        $defaultAction = $this->url['base_l'].'/process/account/login';
        if(is_null($action)){
            $action = $defaultAction;
        }
        $defaultApp = array(
            'link'      => $core['link']['base_l'],
            'logo'      => $core['link']['base_l'].'/cdn/site/logo.png',
            'name'      => $core['site']->getTitle(),
        );
        $app = array_merge($defaultApp, $app);
        $defaultLink = array(
            'register'      => $core['link']['base_l'].'/account/register',
            'reset'         => $core['link']['base_l'].'/account/reset',
        );
        $link = array_merge($defaultLink, $link);
        $defaultSettings = array(
            'register'      => true,
            'remember'      => true,
            'reset'         => true,
        );
        $settings = array_merge($defaultSettings, $settings);
        /**
         * Prepare $vars
         */
        $vars = array(
            'action'            => $action,
            'app'               => $app,
            'link'              => $link,
            'locale'            => $core['locale'],
            'method'            => $method,
            'msg'               => $msg,
            'settings'          => $settings,
        );

        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Widgets:centeredLoginForm.html.smarty', $vars);
        $this->response['head']['css'][] = 'plugins/form-toggle/toggles.css';
        $this->response['head']['js'] = array(
            '/js/jquery-1.10.2.min.js',
            '/js/jqueryui-1.10.3.min.js',
            '/js/bootstrap.min.js',
            '/plugins/form-validation/jquery.validate.min.js'
        );
        $this->response['body']['classes'] = array('focusedform');
        $this->response['body']['js'] = array(
            '/plugins/form-toggle/toggle.min.js',
        );

        return $this->response;
    }
    /**
     * @name            renderCenteredRegistrationForm()
     *                  Renders centered registration form
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @param           string          $action
     * @param           string          $method
     * @param           array           $app
     * @param           array           $link
     * @param           array           $settings
     * @param           array           $msg
     * @param           array           $core
     *
     * @return          array           $response
     */
    public function renderCenteredRegistrationForm($action = null, $method = 'post', $app = array(), $link = array(), $settings = array(), $msg = array(), $core = null){
        $this->resetRenderResponse();
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        $defaultAction = $this->url['base_l'].'/process/account/reset';
        if(is_null($action)){
            $action = $defaultAction;
        }
        $defaultApp = array(
            'link'      => $core['link']['base_l'],
            'logo'      => $core['link']['base_l'].'/cdn/site/logo.png',
            'name'      => $core['site']->getTitle(),
        );
        $app = array_merge($defaultApp, $app);
        $defaultLink = array(
            'login'      => $core['link']['base_l'].'/account/login',
        );
        $link = array_merge($defaultLink, $link);
        $defaultSettings = array(
            'agreement'     => true,
        );
        $settings = array_merge($defaultSettings, $settings);
        /**
         * Prepare $vars
         */
        $vars = array(
            'action'            => $action,
            'app'               => $app,
            'link'              => $link,
            'locale'            => $core['locale'],
            'method'            => $method,
            'msg'               => $msg,
            'settings'          => $settings,
        );

        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Widgets:centeredRegistrationForm.html.smarty', $vars);
        $this->response['head']['css'][] = 'plugins/form-toggle/toggles.css';
        $this->response['head']['js'] = array(
            '/js/jquery-1.10.2.min.js',
            '/js/jqueryui-1.10.3.min.js',
            '/js/bootstrap.min.js',
            '/plugins/form-validation/jquery.validate.min.js'
        );
        $this->response['body']['classes'] = array('focusedform');
        $this->response['body']['js'] = array(
            'plugins/form-toggle/toggle.min.js',
        );

        return $this->response;
    }
    /**
     * @name            renderCenteredResetPasswordForm()
     *                  Renders centered password reset form
     *
     * @since           1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @param           string          $action
     * @param           string          $method
     * @param           array           $app
     * @param           array           $link
     * @param           array           $msg
     * @param           array           $core
     * @param           array           $settings
     *
     * @return          array           $response
     */
    public function renderCenteredResetPasswordForm($action = null, $method = 'post', $app = array(), $link = array(), $msg = array(), $core = null, $settings = array()){
        $this->resetRenderResponse();
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        $defaultAction = $this->url['base_l'].'/process/account/reset';
        if(is_null($action)){
            $action = $defaultAction;
        }
        $defaultApp = array(
            'link'      => $core['link']['base_l'],
            'logo'      => $core['link']['base_l'].'/cdn/site/logo.png',
            'name'      => $core['site']->getTitle(),
        );
        $app = array_merge($defaultApp, $app);
        $defaultLink = array(
            'login'      => $core['link']['base_l'].'/account/login',
        );
        $link = array_merge($defaultLink, $link);

        /**
         * Prepare $vars
         */
        $vars = array(
            'action'            => $action,
            'app'               => $app,
            'link'              => $link,
            'locale'            => $core['locale'],
            'method'            => $method,
            'msg'               => $msg,
            'settings'          => $settings,
        );

        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Widgets:centeredResetPasswordForm.html.smarty', $vars);
        $this->response['head']['css'][] = 'plugins/form-toggle/toggles.css';
        $this->response['head']['js'] = array(
            '/js/jquery-1.10.2.min.js',
            '/js/jqueryui-1.10.3.min.js',
            '/js/bootstrap.min.js',
            '/plugins/form-validation/jquery.validate.min.js'
        );
        $this->response['body']['classes'] = array('focusedform');
        $this->response['body']['js'] = array(
            '/plugins/form-toggle/toggle.min.js',
        );

        return $this->response;
    }
    /**
     * @name            renderCorePreview()
     *                  Renders code preview area
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           string          $code
     * @param           bool            $inline         if true code else pre
     * @param           bool            $prettify       true, false
     * @param           bool            $showLineNumbers true, false
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          array           $response
     */
    public function renderCodePreview($code, $inline = false, $prettify = true, $showLineNumbers = true, $core = null){
        $this->resetRenderResponse();
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        $codeBlock = '';
        if($inline){
            $codeBlock =  $this->render->typography->renderCodeBlock($code, $core);
        }
        else{
            $classes = array();
            if($prettify){
                $classes[] = 'prettyprint';
            }
            if($showLineNumbers){
                $classes[] = 'linenums';
            }
            $codeBlock =  $this->render->typography->renderPreBlock($code, $classes, $core);
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'code'                      => $codeBlock,
            'prettify'                  => $prettify,
            'showLineNumbers'           => $showLineNumbers,
        );

        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Widgets:codePreview.html.smarty', $vars);
        if($prettify){
            $this->response['head']['css'][] = '/plugins/codeprettifier/prettify.css';
            $this->response['body']['js'][] = '/plugins/codeprettifier/prettify.js';
        }
        return $this->response;
    }
    /**
     * @name            renderFooterItems()
     *                  Renders footer item list.
     *
     * @since           1.0.4
     * @version         1.0.4
     * @author          Can Berkol
     *
     * @param           array           $footerItems
     * @param           array           $core
     *
     * @return          array           $response
     */
    public function renderFooterItems($footerItems = array(), $core = null){
        $this->resetRenderResponse();
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        $vars = array(
            'footerItems'   => $footerItems
        );
        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Widgets:footerItems.html.smarty', $vars);
        return $this->response;
    }
    /**
     * @name            renderLeftMenuTrigger()
     *                  Renders left menu trigger for headerbar.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @param           array           $core
     *
     * @return          array           $response
     */
    public function renderLeftMenuTrigger($core = null){
        $this->resetRenderResponse();
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        $vars = array();
        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Widgets:leftMenuTrigger.html.smarty', $vars);
        $this->response['body']['js'] = array(
            '/js/jquery-1.10.2.min.js',
            '/js/bootstrap.min.js',
            '/plugins/form-toggle/toggle.min.js',
        );
        return $this->response;
    }
    /**
     * @name            renderMlsForm()
     *                  Renders MLS Form.
     *
     * @since           1.0.6
     * @version         1.0.6
     * @author          Can Berkol
     *
     * @param           string          $groupCode          Group code to collect elements in one array.
     * @param           array           $languages          List of languages to be shown in MLS form
     * @param           array           $switch             Switch details.
     * @param           array           $response           Holds response messages.
     * @param           string          $xss                Security code.
     * @param           array           $settings           Holds widget settings.
     * @param           array           $form               Holds form details.
     * @param           array           $core
     *
     * @return          array           $response
     */
    public function renderMlsForm($groupCode, $languages, $switch = array(), $response = array(), $xss = '', array $settings = array(), array $form = array(), $core = null){
        $this->resetRenderResponse();
        $formRenderer = $core['kernel']->getContainer()->get('formrender.model');
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }

        $code = $this->generateUid('mlsWidget-');

        /** Preparing settings */
        $defaultSettings = array(
            'defaultState'  => 'closed',
            'hasCkEditor'   => false,
            'hasFile'       => false,
            'postAjax'      => false,
            'showSwitch'    => true,
            'standalone'    => true,
        );

        $settings = array_merge($defaultSettings, $settings);

        /** Setting up form */
        $formDefaults = array(
            'action'    => '',
            'defaultLanguage'       => 'English',
            'defaultLanguageCode'   => 'en',
            'inputs'    => array(),
            'method'    => 'post',
            'xss'       => $xss,
        );
        $form = array_merge($formDefaults, $form);

        /** Setting up languages */
        $defaultLanguages = array();
        $languages = array_merge($defaultLanguages, $languages);

        /** Prepare inputs */
        $modifiedInputs = array();
        $count = 1;
        foreach($languages as $language){
            $inputs = array();
            foreach($form['inputs'] as $input){
                $renderMethod = 'render'.$input['type'];
                $input['id'] = $input['id'] . '-' . $language['code'] . '-' . $code;
                $input['name'] = $groupCode.'.local.'.$language['code'].'.'.$input['name'];
                if(isset($input['values'])){
                    $input['value'] = $input['values'][$language['code']];
                }
                else{
                    $inputValue = '';
                    if(isset($input['value'])){
                        $inputValue = $input['value'];
                    }
                    $input['value'] = $inputValue;
                    unset($inputValue);
                }
                if ($count > 1 && isset($input['attributes'])) {
                    unset($input['attributes']);
                }
                $renderResponse = $formRenderer->$renderMethod($input);
                $inputs[] = $renderResponse['html'];
                if(isset($renderResponse['head']) && is_array($renderResponse['head']['css'])){
                    $this->response['css']['js'] = array_merge($this->response['head']['css'], $renderResponse['head']['css']);
                }
                if(isset($renderResponse['body']) && is_array($renderResponse['head']['js'])){
                    $this->response['head']['js'] = array_merge($this->response['head']['js'], $renderResponse['head']['js']);
                }
                if(isset($renderResponse['body']) && is_array($renderResponse['body']['js'])){
                    $this->response['body']['js'] = array_merge($this->response['body']['js'], $renderResponse['body']['js']);
                }
                unset($renderResponse);
            }
            $modifiedInputs[$language['code']] = $inputs;
            unset($inputs);
            $count++;
        }
        unset($count);
        $form['inputs'] = $modifiedInputs;
        /** Setting up switch details */
        $switchDefaults = array(
            'actions'       => array(),
        );
        $switch = array_merge($switchDefaults, $switch);

        /** Setting up response messages */
        $responseDefaults = array(
            'error'     => 'error',
            'success'   => 'success',
        );
        $response = array_merge($responseDefaults, $response);

        $vars = array(
            'module'    => array(
                'code'          => $code,
                'form'          => $form,
                'languages'     => $languages,
                'response'      => $response,
                'settings'      => $settings,
                'switch'        => $switch,
            )
        );

        $this->response['renderCode'] = $code;
        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Widgets:mlsForm.html.smarty', $vars);
        $this->response['head']['css'][] = '/plugins/form-toggle/toggles.css';
        $this->response['head']['css'][] = '/plugins/bootstrapswitch/bootstrap-switch.css';
        $this->response['head']['js'][] = '/js/jquery-1.10.2.min.js';
        $this->response['head']['js'][] = '/js/jqueryui-1.10.3.min.js';
        $this->response['head']['js'][] = '/js/bootstrap.min.js';
        $this->response['head']['js'][] = '/js/application.js';
        $this->response['head']['js'][] = '/plugins/form-toggle/toggle.min.js';
        $this->response['head']['js'][] = '/plugins/bootstrapswitch/bootstrap-switch.min.js';
        $this->response['head']['js'][] = '/plugins/form-validation/jquery.validate.min.js';
        $this->response['head']['js'][] = '/plugins/form2js/form2js.js';
        $this->response['head']['js'][] = '/plugins/form2js/jquery.toObject.js';
        $this->response['head']['js'][] = '/plugins/form2js/js2form.js';

        return $this->response;
    }
    /**
     * @name            renderPanel()
     *                  Renders panel.
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Can Berkol
     *
     * @param           string          $title              Optional. Will to be ommited if tabs are used as pageHeading.
     * @param           string          $titleIcon          Only needed if you want to add an icon to title.
     * @param           string          $panelBody          Holds panel body details. The contents may be compositions of other renders.
     * @param           string          $panelType          primary etc.
     * @param           array           $panelTabs          If panel needs to be run as tabbed content area.
     * @param           array           $panelOptions       If panel heading needs to provide other options and controls.
     * @param           array           $footerElements     The contents of the footer section - mainly rendered buttons and other similar controls.
     * @param           array           $settings
     * @param           array           $core
     *
     * @return          array           $response
     */
    public function renderPanel($title = null, $titleIcon = null, $panelBody = '', $panelType = '', $panelTabs = array(), $panelOptions = array(), $footerElements = array(), $settings = array(), $core = null){
        $this->resetRenderResponse();
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        $defaultSettings = array(
            'hasTabs'       => false,
            'isCollapsible' => false,
            'showFooter'    => true,
            'showHeader'    => true,
            'showOptions'   => false,
            'tablePlacement'=> 'options',
        );
        $settings = array_merge($defaultSettings, $settings);
        if(count($panelTabs) > 0){
            $settings['hasTabs'] = true;
        }
        if(count($panelOptions) > 0){
            $settings['showOptions'] = true;
        }
        if(count($footerElements) > 0){
            $settings['showFooter'] = true;
        }
        if($settings['showOptions'] || $settings['hasTabs']){
            $settings['showHeader'] = true;
        }
        $defaultPanelBody = array(
            'content'       => '',
            'contentBottom' => '',
            'contentTop'    => '',
        );
        $panelBody = array_merge($defaultPanelBody, $panelBody);
        /**
         * Prepare $vars
         */
        $vars = array(
            'panelBody'         => $panelBody,
            'panelType'         => $panelType,
            'settings'          => $settings,
        );
        if(isset($title) && !is_null($title)){
            $vars['title'] = $title;
        }
        if(isset($titleIcon) && !is_null($titleIcon)){
            $vars['titleIcon'] = $titleIcon;
        }
        if(count($panelOptions) > 0){
            $vars['panelOptions'] = $panelOptions;
        }
        if(count($panelTabs) > 0){
            $vars['panelTabs'] = $panelTabs;
        }
        if(count($panelOptions) > 0){
            $vars['panelOptions'] = $panelOptions;
        }
        if(count($footerElements) > 0){
            $vars['footerElements'] = $footerElements;
        }
        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Widgets:panel.html.smarty', $vars);

        return $this->response;
    }
    /**
     * @name            renderPageHeader()
     *                  Render page header
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Can Berkol
     *
     * @param           array           $page
     * @param           array           $settings
     * @param           array           $options
     * @param           array           $core
     *
     * @return          array
     */
    public function renderPageHeader($page, $settings = array(), $options = array(), $core = null){
        $this->resetRenderResponse();
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        $defaultSettings = array(
            'hasOptions'    => false,
        );
        $settings = array_merge($defaultSettings, $settings);
        /**
         * Prepare $vars
         */
        $vars = array(
            'page'                      => $page,
            'settings'                  => $settings,
            'options'                   => $options,
        );

        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Widgets:pageHeader.html.smarty', $vars);

        return $this->response;
    }
    /**
     * @name            renderQuickScrollButton()
     *                  Renders quick scroll button
     *
     * @since           1.0.5
     * @version         1.0.5
     * @author          Can Berkol
     *
     * @param           array           $core
     *
     * @return          array           $response
     */
    public function renderQuickScrollButton($core = null){
        $this->resetRenderResponse();
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        $vars = array();
        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Widgets:quickScrollButton.html.smarty', $vars);
        return $this->response;
    }
    /**
     * @name            renderRightMenuTrigger()
     *                  Renders right menu trigger for headerbar.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @param           array           $core
     *
     * @return          array           $response
     */
    public function renderRightMenuTrigger($core = null){
        $this->resetRenderResponse();
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        $vars = array();
        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Widgets:rightMenuTrigger.html.smarty', $vars);
        $this->response['body']['js'] = array(
            '/js/jquery-1.10.2.min.js',
            '/js/bootstrap.min.js',
            '/plugins/form-toggle/toggle.min.js',
        );
        return $this->response;
    }
    /**
     * @name            renderSidebarLogo()
     *                  Renders sidebar logo div.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @param           string          $logo
     * @param           string          $smallLogo
     * @param           string          $siteName
     * @param           string          $siteUrl
     * @param           array           $core
     *
     * @return          array           $response
     */
    public function renderSidebarLogo($logo, $smallLogo, $siteName, $siteUrl, $core = null){
        $this->resetRenderResponse();
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        $vars = array(
            'logoUrl'       => $logo,
            'siteName'      => $siteName,
            'siteUrl'       => $siteUrl,
            'smallLogoUrl'  => $smallLogo,
        );
        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Widgets:sidebarLogo.html.smarty', $vars);
        return $this->response;
    }
    /**
     * @name            renderSidebarNavigation()
     *                  Renders sidebar navigation.
     *
     * @since           1.0.3
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @param           array           $navigationItems        see /Widgets:sidebarNavigation.html.smarty for more information
     * @param           array           $settings
     * @param           array           $core
     *
     * @return          array           $response
     */
    public function renderSidebarNavigation($navigationItems, $settings = array(), $core = null){
        $this->resetRenderResponse();
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        $defaultSettings = array(
            'search'    => true,
            'theme'     => $core['theme'],
        );
        $settings = array_merge($defaultSettings, $settings);
        $vars = array(
            'locale'            => $core['locale'],
            'navigationItems'   => $navigationItems,
            'settings'          => $settings,
        );
        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Widgets:sidebarNavigation.html.smarty', $vars);
        $this->response['head']['js'] = array(
            '/js/jquery-1.10.2.min.js',
            '/js/jqueryui-1.10.3.min.js',
            '/js/bootstrap.min.js',
            '/js/enquire.js',
            '/js/jquery.cookie.js',
            '/js/jquery.nicescroll.min.js'
        );
        $this->response['body']['js'] = array(
            '/js/application.js'
        );
        return $this->response;
    }
    /**
     * @name            renderToggleButton()
     *                  Renders toggle button.
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           string          $name
     * @param           array           $toggleItems
     *                                          type (item, divider)
     *                                          name
     *                                          link
     * @param           string          $style          default, danger, warning, primary, success, inverse
     * @param           bool            $alternative
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          array           $response
     */
    public function renderToggleButton($name, $toggleItems, $style = 'default', $alternative = false, $core = null){
        $this->resetRenderResponse();
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'alternative'               => $alternative,
            'name'                      => $name,
            'toggleItems'               => $toggleItems,
        );

        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Widgets:toggleButton.html.smarty', $vars);

        $this->response['head']['css'][] = '/plugins/form-toggle/toggles.css';
        $this->response['body']['js'][] = '/plugins/form-toggle/toggle.min.js';

        return $this->response;
    }
    /**
     * @name            renderTopNavigation()
     *                  Renderstop navigation.
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @param           array           $member
     * @param           array           $userLinks
     * @param           array           $settings
     * @param           array           $core
     *
     * @return          array           $response
     */
    public function renderTopNavigation($member, $userLinks = array(), $settings = array(), $core = null){
        $this->resetRenderResponse();
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        $defaultSettings = array(
            'hasLangSwitch'     => false,
            'hasQuickLinks'     => false,
            'link'              => array(
                'logout'        => '/account/process/logout',
            ),
            'showUserLinks'     => true,
        );
        $settings = array_merge($defaultSettings, $settings);
        $vars = array(
            'locale'        => $core['locale'],
            'member'        => $member,
            'settings'      => $settings,
            'userLinks'     => $userLinks
        );
        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Widgets:topNavigation.html.smarty', $vars);
        $this->response['head']['js'] = array(
            '/js/jquery-1.10.2.min.js',
            '/js/bootstrap.min.js',
            '/plugins/form-toggle/toggle.min.js',
        );
        return $this->response;
    }
}

/**
 * Change Log:
 * **************************************
 * v1.0.5                      Can Berkol
 * 27.05.2014
 * **************************************
 * A renderMlsForm()
 *
 * **************************************
 * v1.0.5                      Can Berkol
 * 02.05.2014
 * **************************************
 * A renderBreadcrumbNavigation()
 * A renderPageHeader()
 * A renderPanel()
 *
 * **************************************
 * v1.0.4                      Can Berkol
 * 01.05.2014
 * **************************************
 * A renderFooterItems()
 * A renderQuickScrollButton()
 *
 * **************************************
 * v1.0.3                      Can Berkol
 * 29.04.2014
 * **************************************
 * A renderSidebarNavigation()
 *
 * **************************************
 * v1.0.2                      Can Berkol
 * 29.04.2014
 * **************************************
 * A renderLeftMenuTrigger()
 * A renderRightMenuTrigger()
 * A renderSidebarLogo()
 * A renderTopNavigation()
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * 27.04.2014
 * **************************************
 * A renderCenteredRegistrationForm()
 * A renderCenteredResetPasswordForm()
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 23.04.2014
 * **************************************
 * A renderCenteredLoginForm()
 * A renderCodePreview()
 * A renderToggleButton()
 */