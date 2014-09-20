<?php
/**
 * TableRenderController.php
 *
 *
 * @vendor         BiberLtd
 * @package        CoreBundle
 * @subpackage     Controller
 * @name           TableRenderController
 *
 * @author         Selimcan Sakar
 *
 * @copyright      Biber Ltd. (www.biberltd.com)
 *
 * @version        1.0.0
 * @date           05.05.2014
 *
 */

namespace BiberLtd\Bundle\CoreBundle\Controller;

use BiberLtd\Bundle\CoreBundle\CoreController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

class TableRenderController extends CoreController{

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
     * @name            renderTable()
     *                  Renders tables
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Selimcan Sakar
     *
     * @param           array           $tableContent
     * @param           array           $core
     *
     * @return          array           $response
     */
    public function renderTable($tableContent, $core = null){
        $this->resetRenderResponse();
        if(is_null($core) || !isset($core['theme'])){
            $core['theme'] = 'bibercrm';
        }
        $defaultClasses = array();
        if(!isset($tableContent['classes'])){
            $tableContent['classes'] = $defaultClasses;
        }
        $defaultSettings = array('responsive' => true);
        if(!isset($tableContent['settings'])){
            $tableContent['settings'] = $defaultSettings;
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'table'       => $tableContent,
        );

        $this->response['html'] = $this->templating->render('BiberLtdCoreBundlesCoreBundle:' . $core['theme'] . '/HtmlParts:table.html.smarty', $vars);

        return $this->response;
    }

    public function renderDataTable($data, $core, $title = '', $txt = array(), $settings = array(), $tabIndex = 88788) {
        $translator = new Translator($core['locale']);

        $this->url = $core['url'];
        $settingsDefaults = array(
            'ajax' => 'true',
            'column' => array(
                'width' => '10px',
            ),
            'editable' => 'true',
            'info' => 'true',
            'filter' => 'true',
            'lengthChange' => 'true',
            'link' => array(
                'order' => $this->url['base_l'] . '/',
                'source' => $this->url['base_l'] . '/',
            ),
            'method' => 'POST',
            'orderWithAjax' => 'true',
            'paginate' => 'true',
            'paginationType' => 'bootstrap',
            'processing' => 'true',
            'row' => array(
                'count' => 100,
                'show' => 10,
                'start' => 1,
            ),
            'rowReordering' => false,
            'security' => md5(md5('bbr0609')),
            'sorting' => 'true',
            'sortingDefault' => '[]',
            'state' => 'true',
            'table' => array(
                'height' => '',
                'scrollCollapse' => 'true',
                'width' => '',
            ),
            'viewport' => false,
        );

        $txtDefaults = array(
            'btn' => array(
                'edit' => $translator->trans('btn.edit', array(), 'datatable'),
            ),
            'lbl' => array(
                'find' => $translator->trans('lbl.find', array(), 'datatable'),
                'first' => $translator->trans('lbl.first', array(), 'datatable'),
                'info' => $translator->trans('lbl.info', array(), 'datatable'),
                'last' => $translator->trans('lbl.last', array(), 'datatable'),
                'limit' => $translator->trans('lbl.limit', array(), 'datatable'),
                'next' => $translator->trans('lbl.next', array(), 'datatable'),
                'prev' => $translator->trans('lbl.prev', array(), 'datatable'),
                'processing' => $translator->trans('lbl.processing', array(), 'datatable'),
                'recordNotFound' => $translator->trans('lbl.not_found', array(), 'datatable'),
                'noRecords' => $translator->trans('lbl.no_records', array(), 'datatable'),
                'numberOfRecords' => $translator->trans('lbl.number_of_records', array(), 'datatable'),
            ),
        );
        /**
         * Merge values and set template variables.
         */
        $vars = array(
            'module' => array(
                'data' => $data,
                'code' => rand(0, time()),
                'settings' => array_merge($settingsDefaults, $settings),
                'tabIndex' => $tabIndex,
                'title' => $title,
                'txt' => array_merge($txtDefaults, $txt),
            ),
        );

        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:' . $core['theme'] . '/HtmlParts:datatable.html.smarty', $vars);
    }
}

/**
 * Change Log:
 * **************************************
 * v1.0.5                 Selimcan Sakar
 * 05.05.2014
 * **************************************
 * A TableRenderController()
 *
 */