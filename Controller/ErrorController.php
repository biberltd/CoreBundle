<?php
/**
 * ErrorController
 *
 * This class is used to RENDER HTTP error messages
 *
 * @package	    Core\Bundles\CoreBundle
 * @subpackage	Controller
 * @name	    ErrorController
 *
 * @author      Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.2
 *
 */

namespace BiberLtd\Bundle\CoreBundle\Controller;
use BiberLtd\Bundle\CoreBundle\CoreController;
use Symfony\Component\HttpKernel\Exception,
    Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ErrorController extends CoreController{
    /**
     * @name            error404Action()
     *                  DOMAIN/{_locale}/manage/error/404
     *
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.2
     *
     * @return          \Symfony\Component\HttpFoundation\Response
     */
    public function error404Action() {
        /** START INITILALIZATION */
        $this->init(1, 'cmsE404');
        /** Redirect if not a manager */
        $response = $this->ifNotManager('login');
        if($response instanceof RedirectResponse){
            return $response;
        }
        /** Redirect if page not found */
        if(!$this->page){
            $this->sm->logAction('page.visit.fail.404', 1, array('route' => '/manage/error/404'));
            $this->redirect('404');
        }
        $this->sm->logAction('page.visit', 1, array('route' => '/manage/error/404'));
        unset($response);
        /** get sidebar & topbar information */
        $sidebar = $this->prepareManagementSidebar();
        $topbar = $this->prepareManagementTopbar();
        /** END :: INITIALIZATION */

        /** Get current language */
        $MLS = $this->get('multilanguagesupport.model');
        $response = $MLS->listAllLanguages();
        $languages = array();
        if(!$response['error']){
            $languages = $response['result']['set'];
        }
        unset($response);
        $response = $MLS->getLanguage($this->locale, 'iso_code');
        $language = false;
        if (!$response['error']) {
            $language = $response['result']['set'];
        }
        unset($response);

        /** Get site */
        $siteModel = $this->get('sitemanagement.model');
        $response = $siteModel->getSite(1, 'id');
        $site = false;
        if (!$response['error']) {
            $site = $response['result']['set'];
        }
        unset($response);
        /** Get page */
        $pageCode = 'cmsE404';
        $cmsModel = $this->get('cms.model');
        $response = $cmsModel->getPage($pageCode, 'code');

        if ($response['error']) {
            $this->sm->logAction('page.visit.fail.404', 1, array('route' => '/manage/error/404'));
            echo '404 page is not found in database.';
        }
        $this->sm->logAction('page.visit', 1, array('route' => '/manage/error/404'));
        $currentPage = $response['result']['set'];
        unset($response);

        /** Get Page modules grouped by Section */
        $response = $cmsModel->listModulesOfPageLayoutsGroupedBySection($currentPage, array('sort_order' => 'asc'));
        if ($response['error']) {
            /** Show error if no modules can be loaded */
            echo $this->translator->trans('error.page.load', array(), 'sys');
            exit;
        }
        $blocks = $response['result']['set'];
        unset($response);

        /**
         * Get core render model and prepare core information
         */
        $coreRender = $this->get('corerender.model');
        $core = array(
            'locale' => $this->get('session')->get('_locale'),
            'theme' => $currentPage->getLayout()->getTheme()->getFolder(),
            'url' => $this->url,
        );
        /** Get top navigation */
        $response = $cmsModel->listItemsOfNavigation('cms_nav_top', 'top', array('sort_order' => 'asc'));
        $topNavItems = array();
        if (!$response['error']) {
            $topNavItems = $response['result']['set'];
        }
        $topNavigation = $coreRender->renderQuickActionsNavigation($topNavItems, $core);
        unset($topNavItems, $response);

        /** Get project logo */
        $siteSettings = json_decode($site->getSettings());
        $projectLogoUrl = $this->url['cdn'] . '/site/logo/' . $siteSettings->logo;
        $dashboardSettings = array(
            'link' => $this->url['base_l'] . '/manage/dashboard',
            'title' => $this->translator->trans('dashboard.title', array(), 'admin'),
        );
        $renderedProjectLogo = $coreRender->renderProjectLogo($projectLogoUrl, $site->getTitle(), $core, $dashboardSettings);
        unset($siteSettings, $projectLogoUrl, $dashboardSettings);
        /** Prepare sidebar separator */
        $renderedSidebarSeparator = $coreRender->renderSidebarSeparator($core);

        /** Get sidebar navigation */
        $response = $cmsModel->listItemsOfNavigation('cms_nav_main', 'top', array('sort_order' => 'asc'));
        $sideNavItems = array();
        if (!$response['error']) {
            $sideNavItems = $response['result']['set'];
        }
        unset($response);
        $navCollection = array();
        foreach ($sideNavItems as $navItem) {
            $response = $cmsModel->listNavigationItemsOfParent($navItem, array('sort_order' => 'asc'));
            $childItems = array();
            $hasChildren = false;
            $selectedParent = false;
            if (!$response['error']) {
                $hasChildren = true;
                foreach ($response['result']['set'] as $childItem) {
                    $childNavSelected = false;
                    if ($childItem->getPage()->getId() == $currentPage->getId()) {
                        $childNavSelected = true;
                        $selectedParent = $childItem->getParent()->getId();
                    }
                    $childItems[] = array(
                        'entity' => $childItem,
                        'selected' => $childNavSelected,
                    );
                }
            }
            $navSelected = false;
            if ($navItem->getId() == $selectedParent) {
                $navSelected = true;
            }
            $navCollection[] = array(
                'children' => $childItems,
                'code' => time(),
                'entity' => $navItem,
                'hasChildren' => $hasChildren,
                'selected' => $navSelected,
            );
            unset($response, $childItems);
        }
        unset($sideNavItems);

        $renderedSidebarNavigation = $coreRender->renderSidebarNavigation($navCollection, $core);
        /**
         * 4. REQUIRED :: PREPARE TEMPLATE TAGS
         */

        $vars = array(
            'languages' => $languages,
            'link' => $this->url,
            'renderedProjectLogo'   => $sidebar['projectLogo'],
            'renderedSidebarNavigation' => $sidebar['navigation'],
            'renderedSidebarSeparator' => $sidebar['separator'],
            'renderedTopNavigation' => $topbar['navigation'],
            'theme' => 'elitegray',
            'today' => new \DateTime('now', new \DateTimeZone($this->get('kernel')->getContainer()->getParameter('app_timezone'))),
        );

        $css = array('css/style.css');
        $js = array(
            'js/libs/jquery-1.10.2.js',
            'js/plugins/validate/jquery.validate.1.11.1.js',
            'js/libs/bootstrap.js',
            'js/plugins/collapsible/collapsible.js',
        );

        return $this->renderPage($vars, $css, $js);
    }

}
/**
 * **************************************
 * v1.0.2                      Can Berkol
 * 21.04.2014
 * **************************************
 * Comments have been fixed.
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 27.01.2014
 * **************************************
 * A error404Action()
 */
    