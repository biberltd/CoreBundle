<?php
/**
 * EmailRenderController.php
 *
 * Handles system related email letter creations.
 *
 * @vendor         BiberLtd
 * @package        CoreBundle
 * @subpackage     Controller
 * @name           EmailRenderController
 *
 * @author         Can Berkol
 *
 * @copyright      Biber Ltd. (www.biberltd.com)
 *
 * @version        1.0.0
 * @date           28.04.2014
 *
 */

namespace BiberLtd\Bundle\CoreBundle\Controller;

use BiberLtd\Bundle\CoreBundle\CoreController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

class EmailRenderController extends CoreController{

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
     * @name            renderSystemNotification()
     *                  Renders system notification
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           string          $title
     * @param           string          $message
     * @param           string          $copyright
     * @param           array           $settings
     * @param           array           $core
     * @param           array           $colors
     * @param           integer         $siteId
     *
     * @return          array           $response
     */
    public function renderSystemNotification($title, $message, $copyright, $settings, $core, $colors = array(), $siteId = 1){
        $defaultColors = array(
            'bg'    => array(
                'body'              => '#FAFAFA',
                'bodycontent'       => '#505050',
                'preheader'         => '#FAFAFA',
                'templatebody'      => '#FFFFFF',
                'templatefooter'    => '#FDFDFD',
                'templateheader'    => '#FFFFFF',
            ),
            'link'  => array(
                'bodycontent'       => '#ff9e47',
                'footercontent'     => '#FFFFFF',
                'headercontent'     => '#0300eb',
                'preheadercontent'  => '#ff9e47',
            ),
            'text'  => array(
                'bodycontent'       => '#444444',
                'footercontent'     => '#ff9e47',
                'headercontent'     => '#ff9e47',
                'h1'                => '#202020',
                'h2'                => '#202020',
                'h3'                => '#202020',
                'h4'                => '#202020',
                'preheadercontent'  => '#505050',
                'templateheader'    => '#444444',
            )
        );
        $colors = array_merge($defaultColors, $colors);
        $defaultSettings = array(
            'logo'  => '/cdn/site/logo.png',
            'name'  => 'Biber Ltd.',
            'url'   => $this->url['base_l'],
        );
        $settings = array_merge($defaultSettings, $settings);
        $vars = array(
            'colors'        => $colors,
            'copyright'     => $copyright,
            'message'       => $message,
            'settings'      => $settings,
            'title'         => $title,
        );
        $html = $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Email:systemNotification.html.smarty', $vars);
        return $html;
    }
}

/**
 * Change Log:
 * **************************************
 * v1.0.0                      Can Berkol
 * 28.04.2014
 * **************************************
 * A renderSystemNotification()
 */