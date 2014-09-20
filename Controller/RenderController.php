<?php
/**
 * RenderController
 *
 * Used to render standard core bundle views..
 *
 * @vendor         BiberLtd
 * @package        CoreBundle
 * @subpackage     Controller
 * @name           RenderController
 *
 * @author         Can Berkol
 *
 * @copyright      Biber Ltd. (www.biberltd.com)
 *
 * @version        1.1.4
 * @date           08.05.2014
 *
 */

namespace BiberLtd\Bundle\CoreBundle\Controller;

use BiberLtd\Bundle\CoreBundle\CoreController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

class RenderController extends  CoreController{

    private $templating;
    private $theme;

    public function __construct(EngineInterface $templating) {
        $this->templating = $templating;
    }

    /**
     * @name            renderAnalyticsWidget()
     *                  Renders Google Analytics Widget.
     *
     * @since           1.0.7
     * @version         1.0.7
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.dashboard_analytics.html.smarty
     *
     * @param           array  $content                See generic_form widget documentation.
     * @param           array  $core                   Holds core controller information.
     * @param           string $icon
     * @param           string $title                  widget title.
     * @param           array  $settings               See generic_form widget documentation.
     *
     * @return          string
     */
    public function renderAnalyticsWidget($content, $core, $title = '', $icon = '', $settings = array()) {
        $this->url = $core['url'];
        $code = rand(1, time());
        /**
         * Settings
         */
        $settingsDefault = array(
            'size' => 12,
            'token' => array(
                'link' => '',
                'text' => '',
            ),
            'wrapInRow' => true,
        );
        $settingsDefault['token'] = array_merge($settingsDefault['token'], $settings['token']);
        unset($settings['token']);
        /**
         * Prepare $vars
         */
        $vars = array(
            'module' => array(
                'code' => $code,
                'content' => $content,
                'icon' => $icon,
                'settings' => array_merge($settingsDefault, $settings),
                'title' => $title,
            ),
        );
        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.dashboard_analytics.html.smarty', $vars);
    }
    /**
     * @name            renderChartWidget()
     *                  Renders generic charting widget.
     *
     * @since           1.0.7
     * @version         1.0.7
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.dashboard_analytics.html.smarty
     *
     * @param           array  $content                See generic_form widget documentation.
     * @param           array  $core                   Holds core controller information.
     * @param           string $icon
     * @param           string $title                  widget title.
     * @param           array  $settings               See generic_form widget documentation.
     *
     * @return          string
     */
    public function renderChartWidget($content, $core, $title = '', $icon = '', $settings = array()) {
        $this->url = $core['url'];
        $code = rand(1, time());
        /**
         * Settings
         */
        $settingsDefault = array(
            'size' => 12,
            'wrapInRow' => true,
        );
        /**
         * Prepare $vars
         */
        $vars = array(
            'module' => array(
                'code' => $code,
                'content' => $content,
                'icon' => $icon,
                'settings' => array_merge($settingsDefault, $settings),
                'title' => $title,
            ),
        );
        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.dashboard_chart.html.smarty', $vars);
    }
    /**
     * @name            renderContentEditor()
     *                  Renders content editor view.
     *
     * @since           1.0.8
     * @version         1.0.8
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.content_editor.html.smarty
     *
     * @param           array   $settings       Contains data details.
     * @param           array   $core           Holds core controller information.
     * @param           string  $title          Data table title.
     * @param           string  $icon
     * @param           array   $txt            Holds localization strings (btn, lbl)
     * @param           array   $settings
     *
     * @return          string
     */
    public function renderContentEditor($settings, $core, $title = '', $icon = '', $txt = array()) {
        $translator = new Translator($core['locale']);

        $this->url = $core['url'];
        $settingsDefaults = array(
            'imageEnabled'  => true,
            'jsFolder'      => '',
            'selfContained' => false,
            'textEnabled'   => true,
            'url'           => array(
                'getContent'        => '',
                'saveContent'       => '',
                'uploadImage'       => '',
            ),
            'wrapInRow'     => false,
        );

        $txtDefaults = array(
            'btn' => array(
                'add_image'     => $translator->trans('btn.add_image', array(), 'content_editor'),
                'add_text'      => $translator->trans('btn.add_text', array(), 'content_editor'),
                'change'        => $translator->trans('btn.change', array(), 'content_editor'),
                'delete'        => $translator->trans('btn.delete', array(), 'content_editor'),
                'no'            => $translator->trans('btn.no', array(), 'content_editor'),
                'save'          => $translator->trans('btn.save', array(), 'content_editor'),
                'yes'           => $translator->trans('btn.yes', array(), 'content_editor'),
            ),
            'lbl' => array(
                'confirm'       => array(
                    'delete'        => $translator->trans('lbl.find', array(), 'content_editor'),
                ),
                'save'          => array(
                    'error'         => $translator->trans('lbl.error', array(), 'content_editor'),
                    'success'       => $translator->trans('lbl.success', array(), 'content_editor'),
                )
            ),
        );
        $txtDefaults = array_merge($txtDefaults, $txt);
        /**
         * Merge values and set template variables.
         */
        $vars = array(
            'module' => array(
                'btn'           => $txtDefaults['btn'],
                'code'          => rand(0, time()),
                'lbl'           => $txtDefaults['lbl'],
                'settings'      => array_merge($settingsDefaults, $settings),
                'icon'          => $icon,
                'title'         => $title,
            ),
        );
        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.content_editor.html.smarty', $vars);
    }
    /**
     * @name                  renderDashboardStatistics ()
     *                                                  Renders project logo widget.
     *
     * @since            1.0.0
     * @version          1.0.2
     * @author           Can Berkol
     *
     * @see              BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.dashboard_statistics.html.smarty
     *
     * @param           array $buttons                  Statistics data.
     * @param           array $core                     Holds core controller information.
     *
     * @return          string
     */
    public function renderDashboardButtons($buttons, $core) {
        $this->url = $core['url'];
        /**
         * Settings
         */
        /**
         * Prepare $vars
         */
        $vars = array(
            'module' => array(
                'core' => $core,
                'buttons' => $buttons,
                'link' => $this->url,
            ),
        );

        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.dashboard_buttons.html.smarty', $vars);
    }

    /**
     * @name           renderDashboardStatistics ()
     *                 Renders project logo widget.
     *
     * @since           1.0.0
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.dashboard_statistics.html.smarty
     *
     * @param           array $statistics               Statistics data.
     * @param           array $core                     Holds core controller information.
     * @param           array $settings                 Render settings
     *
     * @return          string
     */
    public function renderDashboardStatistics($statistics, $core, $settings = array()) {
        $this->url = $core['url'];
        /**
         * Settings
         */
        $settingsDefault = array(
            'showChange' => false,
        );
        /**
         * Prepare $vars
         */
        $vars = array(
            'module' => array(
                'core' => $core,
                'link' => $this->url,
                'settings' => array_merge($settingsDefault, $settings),
                'statistics' => $statistics,
            ),
        );

        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.dashboard_statistics.html.smarty', $vars);
    }

    /**
     * @name                    renderDataTable ()
     *                                          Renders data table / list view widget.
     *
     * @since            1.0.0
     * @version          1.0.0
     * @author           Can Berkol
     *
     * @see              BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.datatable.html.smarty
     *
     * @param           array   $data           Contains data details.
     * @param           array   $core           Holds core controller information.
     * @param           string  $title          Data table title.
     * @param           array   $txt            Holds localization strings (btn, lbl)
     * @param           array   $settings
     * @param           integer $tabIndex       88788. Required for keyboard access to datatable.
     *
     * @return          string
     */
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

        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.datatable.html.smarty', $vars);
    }

    /**
     * @name            renderGenericFormWidget ()
     *                  Renders a simple widget with configurable forms..
     *
     * @since           1.0.6
     * @version         1.1.4
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.generic_form.html.smarty
     *
     * @param           array  $actions                See generic_form widget documentation.
     * @param           array  $content                See generic_form widget documentation.
     * @param           array  $core                   Holds core controller information.
     * @param           string $title                  widget title.
     * @param           string $icon
     * @param           array  $settings               See generic_form widget documentation.
     *
     * @return          string
     */
    public function renderGenericFormWidget($actions, $content, $core, $title = '', $icon = '', $settings = array()) {
        $this->url = $core['url'];
        $code = rand(1, time());
        /**
         * Settings
         */
        $settingsDefault = array(
            'actionsEnabled' => true,
            'mainFormEnabled' => false,
            'size' => 12,
            'wrapInRow' => true,
        );
        /**
         * Content Defaults
         */
        $htmlRenderer = $core['kernel']->getContainer()->get('htmlrender.model');
        $modifiedContent = array();
        foreach ($content as $contentItem) {
            $modifiedRows = array();
            foreach ($contentItem['form']['rows'] as $row) {
                $modifiedInputs = array();
                foreach ($row['inputs'] as $input) {
                    $renderMethod = 'render' . ucfirst($input['type']);
                    $input['id'] = $input['id'] . '-' . $code;
                    if(isset($settings['hasFile']) && $settings['hasFile'] && strtolower($input['type']) == 'singleimageinput'){
                        $input['name'] = $input['name'];
                    }
                    else{
                        $input['name'] = $contentItem['groupCode'] . '[].' . $input['name'];
                    }
                    $modifiedInputs[] = $htmlRenderer->$renderMethod($input);
                }
                if(!isset($row['classes'])){
                    $row['classes'] = array();
                }
                $modifiedRows[] = array('size' => $row['size'], 'inputs' => $modifiedInputs, 'classes' =>$row['classes']);
            }
            $contentItem['form']['rows'] = $modifiedRows;
            $modifiedContent[] = $contentItem;
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'module' => array(
                'actions' => $actions,
                'code' => $code,
                'content' => $modifiedContent,
                'icon' => $icon,
                'settings' => array_merge($settingsDefault, $settings),
                'title' => $title,
            ),
        );

        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.generic_form.html.smarty', $vars);
    }

    /**
     * @name            renderLanguageDropdown()
     *                  Renders a quick action navigation widget.
     *
     * @since           1.1.1
     * @version         1.1.1
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.lang_dropdown.html.smarty
     *
     * @param           BiberLtd\BundleMultiLanguageSupportBundle\Entity\Language           $currentLanguage
     * @param           array               $otherLanguages
     * @param            array               $core
     * @param           string              $path
     *
     * @return          string
     */
    public function renderLanguageDropdown($currentLanguage, $otherLanguages, $core, $path = '') {
        $this->url = $core['url'];
        /**
         * Prepare $vars
         */
        $vars = array(
            'module' => array(
                'currentLanguage'   => $currentLanguage,
                'otherLanguages'    => $otherLanguages,
                'link'              => $this->url,
                'locale'            => $core['locale'],
                'path'              => $path,
            ),
        );

        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.lang_dropdown.html.smarty', $vars);
    }

    /**
     * @name            renderModalBoxView ()
     *                  Renders simple modal box view.
     *
     * @since           1.0.6
     * @version         1.0.6
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.modal_box.html.smarty
     *
     * @param           array  $actions             See simpletable view documentation
     * @param           array  $core                Holds core controller information.
     * @param           string $id                  widget title.
     * @param           string $title
     * @param           string $msg
     * @param           array  $settings            See simpletable view documentation
     *
     * @return          string
     */
    public function renderModalBoxView($actions, $core, $id = '', $title = '', $msg = '', $settings = array()) {
        $translator = new Translator($core['locale']);
        $this->url = $core['url'];
        if (empty($id)) {
            $code = rand(1, time());
        } else {
            $code = $id;
        }
        /**
         * Settings
         */
        $settingsDefault = array(
            'confirmType' => 'a',
        );
        /**
         * Action defaults
         */
        $cancelDefaults = array(
            'text' => $translator->trans('lbl.option.off', array(), 'modalbox'),
        );
        $confirmDefaults = array(
            'text' => $translator->trans('lbl.option.off', array(), 'modalbox'),
        );
        /**
         * Prepare $vars
         */
        $vars = array(
            'module' => array(
                'btn' => array(
                    'cancel' => array_merge($cancelDefaults, $actions['cancel']),
                    'confirm' => array_merge($confirmDefaults, $actions['confirm']),
                ),
                'core' => $core,
                'id' => $code,
                'msg' => $msg,
                'settings' => array_merge($settingsDefault, $settings),
                'title' => $title,
            ),
        );
        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.modal_box.html.smarty', $vars);
    }

    /**
     * @name                   renderMultiLanguageWidget    ()
     *                                                      Renders simultaneously translatable multi-lang widget.
     *
     * @since            1.0.0
     * @version          1.0.9
     * @author           Can Berkol
     *
     * @see              BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.multilang_view.html.smarty
     *
     * @param           string $title                       Title of the module
     * @param           string $groupCode                   i.e. page, memberdetails etc.
     * @param           string $icon                        Path of the icon file to be shown.
     * @param           array  $languages                   'code', 'name'
     * @param           array  $core                        array that contains core controller information.
     * @param           array  $settings                    that hold module settings
     * @param           array  $formDetails                 'inputs' key must have array of form element attributes and corresponding values
     *                                                      $formDetails = array(
     *                                                      type => must be equal to HTMLrenderController method names without "render" prefix
     *                                                      id
     *                                                      {classes}
     *                                                      [height]
     *                                                      label
     *                                                      name
     *                                                      [size]
     *                                                      {settings}
     *                                                      value
     *                                                      );
     * @param           array  $switchDetails               that holds switch details
     *
     * @return          string
     */
    public function renderMultiLanguageWidget($title, $groupCode, $icon, $languages, $core, $settings = array(), $formDetails = array(), $switchDetails = array()) {
        $htmlRenderer = $core['kernel']->getContainer()->get('htmlrender.model');
        $translator = new Translator($core['locale']);
        $code = rand(1, time());

        $this->url = $core['url'];
        /**
         * Settings
         */
        $settingsDefault = array(
            'defaultLanguageCode' => $core['locale'],
            'hasCkEditor' => false,
            'hasFile'  => false,
            'showActions' => false,
            'showSwitch' => true,
            'showSwitchInfo' => false,
            'postAjax' => false,
            'postWidget' => false,
            'widgetSize' => 12,
            'wrapInRow' => true,
        );
        /**
         * Switch Defaults
         */
        $switchDefaults = array(
            'actions' => array(),
            'info' => $translator->trans('lbl.switch.info', array(), 'multilang'),
            'lbl' => array(
                'title' => $translator->trans('lbl.switch.title', array(), 'multilang'),
            ),
            'option' => array(
                'off' => array(
                    'name' => $translator->trans('lbl.option.off', array(), 'multilang'),
                    'value' => 'off',
                ),
                'on' => array(
                    'name' => $translator->trans('lbl.option.on', array(), 'multilang'),
                    'value' => 'on',
                ),
            ),
            'state' => 'off',
        );
        /**
         * Form Defaults
         */
        $formDefaults = array(
            'csfr' => '',
            'inputs' => array(),
            'title' => array(
                'defaultLanguage' => $translator->trans('lbl.default_language', array(), 'multilang'),
            )
        );
        /**
         * Render form inputs
         */
        $inputs = array();
        $modifiedInputs = array();
        $count = 1;
        foreach ($languages as $language) {
            foreach ($formDetails['inputs'] as $input) {
                $renderMethod = 'render' . ucfirst($input['type']);
                $input['id'] = $input['id'] . '-' . $language['code'] . '-' . $code;
                $input['name'] = $groupCode . '.local.' . $language['code'] . '.' . $input['name'];
                if (isset($input['values'])) {
                    $input['value'] = $input['values'][$language['code']];
                } else {
                    $inputValue = '';
                    if (isset($input['value'])) {
                        $inputValue = $input['value'];
                    }
                    $input['value'] = $inputValue;
                    unset($inputValue);

                }
                if ($count > 1 && isset($input['attributes'])) {
                    unset($input['attributes']);
                }
                $inputs[] = $htmlRenderer->$renderMethod($input);
            }
            $modifiedInputs[$language['code']] = $inputs;
            unset($inputs);
            $count++;
        }
        unset($count);
        $formDetails['inputs'] = $modifiedInputs;

        if (isset($formDetails['extraInputs'])) {
            $modifiedInputs = array();
            $count = 0;
            foreach ($formDetails['extraInputs'] as $input) {
                $renderMethod = 'render' . $input['type'];
                $input['id'] = $input['id'] . '-' . $code;
                if(!isset($input['isFile']) || !$input['isFile']){
                    $input['name'] = $groupCode . '.' . $input['name'];
                }
                else{
                    unset($input['isFile']);
                }
                $modifiedInputs[$count]['input'] = $htmlRenderer->$renderMethod($input);
                $modifiedInputs[$count]['type'] = $input['type'];
                $count++;
            }
            $formDetails['extraInputs'] = $modifiedInputs;
        }

        unset($modifiedInputs);
        /**
         * Prepare $vars
         */
        $vars = array(
            'module' => array(
                'code' => $code,
                'core' => $core,
                'form' => array_merge($formDefaults, $formDetails),
                'icon' => $icon,
                'languages' => $languages,
                'response' => array(
                    'success' => 'success',
                    'error' => 'error',
                ),
                'settings' => array_merge($settingsDefault, $settings),
                'switch' => array_merge($switchDefaults, $switchDetails),
                'title' => $title,
            ),
        );

        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.multilang_view.html.smarty', $vars);
    }
    /**
     * @name            renderMultiUpload ()
     *                  Renders multi upload widget.
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.multi_upload.html.smarty
     *
     * @param           array   $core                          Holds core controller information.
     * @param           array   $cropSettings                  Holds crop settings.
     * @param           array   $formSettings                  Holds form settings.
     * @param           array   $uploadSettings                Holds upload settings
     * @param           array   $widgetSettings                Holds widget settings.
     * @param           array   $sortSettings
     * @param           array   $txt
     *
     * @return          string
     */
    public function renderMultiUpload($core, $cropSettings, $formSettings, $uploadSettings, $widgetSettings, $sortSettings = array(), $txt = array()) {
        $translator = new Translator($core['locale']);
        $defaultSortSettings = array(
            'action'    => '',
            'method'    => '',
        );
        $sortSettings = array_merge($defaultSortSettings, $sortSettings);
        $defaultCropSettings = array(
            'action' => '/manage/upload/0',
            'crops' => array(
                array(
                    'height' => '',
                    'name' => '',
                    'width' => '',
                )
            ),
            'method' => 'POST',
        );
        $defaultFormSettings = array(
            'action' => '#',
            'csfr' => '',
            'enctype' => 'multipart/form-data',
            'method' => 'POST',
        );
        $defaultUploadSettings = array(
            'acceptFileTypes' => '/(\.|\/)(gif|jpe?g|png)$/i',
            'autoUpload' => true,
            'crop' => false,
            'dataType' => 'json',
            'dragDrop' => true,
            'dragDropIcon' => '',
            'maxFileSize' => '20000000',
            'postAjax' => true,
            'sorting' => true,
            'uploadController' => '',
            'getImagesController' => '',
            'uploadType' => 'multipart/form-data',
            'wrapInRow' => true,
        );
        $defaultWidgetSettings = array(
            'code' => 'multi_file_upload',
            'icon' => '',
            'title' => $translator->trans('lbl.widget.title', array(), 'multiupload'),
        );
        $defaultTxt = array(
            'dragDropIcon' => '',
            'lbl' => array(
                'error' => $translator->trans('lbl.error', array(), 'multiupload'),
                'empty' => $translator->trans('lbl.empty', array(), 'multiupload'),
                'file_name' => $translator->trans('lbl.file_name', array(), 'multiupload'),
                'file_size' => $translator->trans('lbl.file_size', array(), 'multiupload'),
                'crop' => $translator->trans('lbl.crop', array(), 'multiupload'),
                'dragdrop_info' => $translator->trans('lbl.dragdrop_info', array(), 'multiupload'),
                'preview' => $translator->trans('lbl.preview', array(), 'multiupload'),
                'sort_order' => $translator->trans('lbl.sort_order', array(), 'multiupload'),
                'options' => $translator->trans('lbl.options', array(), 'multiupload'),
            ),
            'btn' => array(
                'add_files' => $translator->trans('btn.add_files', array(), 'multiupload'),
                'cancel' => $translator->trans('btn.cancel', array(), 'multiupload'),
                'delete' => $translator->trans('btn.delete', array(), 'multiupload'),
                'start' => $translator->trans('btn.start', array(), 'multiupload'),
            ),
            'crop' => array(
                'icon' => '',
                'btn' => array(
                    'add_crop' => $translator->trans('crop.btn.add_crop', array(), 'multiupload'),
                    'close' => $translator->trans('crop.btn.close.', array(), 'multiupload'),
                    'save' => $translator->trans('crop.btn.save', array(), 'multiupload'),
                ),
                'lbl' => array(
                    'title' => $translator->trans('crop.lbl.title.', array(), 'multiupload'),
                    'info' => $translator->trans('crop.lbl.info.', array(), 'multiupload'),
                    'select_crop' => $translator->trans('crop.lbl.select_crop.', array(), 'multiupload'),
                ),
            ),
        );
        $txt = array_merge($defaultTxt, $txt);
        $vars = array(
            'module' => array(
                'cropSettings' => array_merge($defaultCropSettings,$cropSettings),
                'formSettings' => array_merge($defaultFormSettings,$formSettings),
                'sortSettings' => $sortSettings,
                'txt' => $txt,
                'uploadSettings' => array_merge($defaultUploadSettings,$uploadSettings),
                'widgetSettings' => array_merge($defaultWidgetSettings,$widgetSettings),
            )
        );
        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.multi_upload.html.smarty', $vars);
    }
    /**
     * @name            renderProjectLogo ()
     *                  Renders project logo widget.
     *
     * @since           1.0.0
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.project_logo.html.smarty
     *
     * @param           string $projectLogo      Project logo URL.
     * @param           string $projectName      Project name.
     * @param           array  $core             Holds core controller information.
     * @param           array  $dashboard
     *
     * @return          string
     */
    public function renderProjectLogo($projectLogo, $projectName, $core, $dashboard = array()) {
        $dashboardDefaults = array(
            'link' => $core['url']['base_l'] . '/manage/dashboard',
            'title' => '',
        );
        /**
         * Prepare $vars
         */
        $vars = array(
            'module' => array(
                'dashboard' => array_merge($dashboardDefaults, $dashboard),
                'project' => array(
                    'logo' => $projectLogo,
                    'name' => $projectName,
                )
            ),
        );

        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.project_logo.html.smarty', $vars);
    }

    /**
     * @name            renderSimpleMatrixTable ()
     *                  Renders simple matrix table view.
     *
     * @since           1.0.0
     * @version         1.1.0
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.simpletable.html.smarty
     *
     * @param           array  $data             See simpletalble view documentation
     * @param           array  $core             Holds core controller information.
     * @param           string $title            widget title.
     * @param           string $icon             Widget icon
     * @param           array  $settings         See simpletalble view documentation
     * @param           array  $actions          See simpletalble view documentation
     * @param           array  $txt             Holds translations.
     *
     * @return          string
     */
    public function renderSimpleMatrixTable($data, $core, $title = '', $icon = '', $settings = array(), $actions = array(), $txt = array()) {
        $translator = new Translator($core['locale']);
        $code = rand(1, time());
        $this->url = $core['url'];
        /**
         * Settings
         */
        $settingsDefault = array(
            'actionColWidth'        => '8%',
            'ajax'                  => array(
                'dataType'              => 'json',
                'method'                => 'post',
                'url'                   => '',
            ),
            'selfContained'         => false,
            'uniqueIdentifier'      => false,
            'uniqueIdentifierCheckIcon' => '',
            'wrapInRow'             => true,
        );
        $txtDefaults = array(
            'btn'       => array(
                'add'       => $translator->trans('btn.add', array(), 'matrixtable'),
                'save'      => $translator->trans('btn.save', array(), 'matrixtable'),
            ),
        );
        /**
         * Prepare $vars
         */
        $vars = array(
            'module' => array(
                'actions'   => $actions,
                'code'      => $code,
                'core'      => $core,
                'data'      => $data,
                'icon'      => $icon,
                'settings'  => array_merge($settingsDefault, $settings),
                'title'     => $title,
                'txt'       => array_merge($txtDefaults, $txt),
            ),
        );

        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.matrixtable.html.smarty', $vars);
    }

    /**
     * @name            renderSimpleTable()
     *                  Renders simple table view.
     *
     * @since           1.0.0
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.simpletable.html.smarty
     *
     * @param           array  $data             See simpletalble view documentation
     * @param           array  $core             Holds core controller information.
     * @param           string $title            widget title.
     * @param           array  $settings         See simpletalble view documentation
     * @param           array  $actions          See simpletalble view documentation
     *
     * @return          string
     */
    public function renderSimpleTable($data, $core, $title = '', $settings = array(), $actions = array()) {
        $this->url = $core['url'];
        /**
         * Settings
         */
        $settingsDefault = array(
            'showActions' => false,
            'showHeader' => true,
        );
        /**
         * Prepare $vars
         */
        $vars = array(
            'module' => array(
                'actions' => $actions,
                'core' => $core,
                'data' => $data,
                'link' => $this->url,
                'settings' => array_merge($settingsDefault, $settings),
                'title' => $title,
            ),
        );

        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.simpletable.html.smarty', $vars);
    }

    /**
     * @name                  renderQuickActionsNavigation ()
     *                                                     Renders a quick action navigation widget.
     *
     * @since            1.0.0
     * @version          1.0.0
     * @author           Can Berkol
     *
     * @see              BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.navigation_quick_actions.html.smarty
     *
     * @param           array $navigationItems             Holds a mixed array that includes navigation details.
     * @param           array $core                        Holds core controller information.
     *
     * @return          string
     */
    public function renderQuickActionsNavigation($navigationItems, $core) {
        $this->url = $core['url'];
        /**
         * Prepare $vars
         */
        $vars = array(
            'module' => array(
                'core' => $core,
                'link' => $this->url,
                'locale' => $core['locale'],
                'navigationItems' => $navigationItems,
            ),
        );

        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.navigation_quick_actions.html.smarty', $vars);
    }

    /**
     * @name                  renderSidebarNavigation ()
     *                                                Renders a sidebar navigation widget.
     *
     * @since            1.0.0
     * @version          1.0.0
     * @author           Can Berkol
     *
     * @see              BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.navigation_sidebar.html.smarty
     *
     * @param           array $navigationItems        Holds a mixed array that includes navigation details.
     * @param           array $core                   Holds core controller information.
     *
     * @return          string
     */
    public function renderSidebarNavigation($navigationItems, $core) {
        $this->url = $core['url'];
        /**
         * Prepare $vars
         */
        $vars = array(
            'module' => array(
                'core' => $core,
                'link' => $this->url,
                'locale' => $core['locale'],
                'navigationItems' => $navigationItems,
            ),
        );

        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.navigation_sidebar.html.smarty', $vars);
    }

    /**
     * @name                  renderSidebarSeparator ()
     *                                               Renders a seperator line for sidebar widget.
     *
     * @since            1.0.0
     * @version          1.0.0
     * @author           Can Berkol
     *
     * @see              BiberLtd\Bundle\CoreBundle\Resources\views\cms\Modules\widget.sidebar_separator.html.smarty
     *
     * @param           array $core                  Holds core controller information.
     *
     * @return          string
     */
    public function renderSidebarSeparator($core) {
        /**
         * Prepare $vars
         */
        $vars = array();

        return $this->templating->render('BiberLtdBundleCoreBundle:' . $core['theme'] . '/Modules:widget.sidebar_separator.html.smarty', $vars);
    }

}

/**
 * Change Log
 * **************************************
 * v1.1.4                      Can Berkol
 * 10.04.2014
 * **************************************
 * B renderGenericFormWidget()
 *
 * **************************************
 * v1.1.3                      Can Berkol
 * 10.04.2014
 * **************************************
 * U renderGenericFormWidget()
 *
 * **************************************
 * v1.1.2                      Can Berkol
 * 10.04.2014
 * **************************************
 * A renderLanguageDropdown()
 *
 * **************************************
 * v1.1.0                      Can Berkol
 * 17.02.2014
 * **************************************
 * A renderSimpleMatrixTable()
 *
 * **************************************
 * v1.0.9                      Can Berkol
 * 12.02.2014
 * **************************************
 * U renderMultiLanguageWidget()  setting postWidget added.
 *
 * **************************************
 * v1.0.8                      Can Berkol
 * 06.02.2014
 * **************************************
 * A renderContentEditor()
 *
 * **************************************
 * v1.0.7                      Can Berkol
 * 31.01.2014
 * **************************************
 * A renderMultiUpload()
 *
 * Change Log:
 * **************************************
 * v1.0.7                      Can Berkol
 * 31.01.2014
 * **************************************
 * A renderAnalyticsWidget()
 *
 * **************************************
 * v1.0.6                      Can Berkol
 * 28.01.2014
 * **************************************
 * A renderGenericFormWidget()
 * A renderModalBoxWidget()
 * U renderMultiLanguageWidget()
 *
 * **************************************
 * v1.0.5                      Can Berkol
 * 27.01.2014
 * **************************************
 * U renderMultiLanguageWidget()
 *
 * *************************************
 * v1.0.4                      Can Berkol
 * 20.01.2014
 * **************************************
 * B renderMultiLanguageWidget()
 *
 * **************************************
 * v1.0.3                      Can Berkol
 * 18.01.2014
 * **************************************
 * A renderMultiLanguageWidget()
 *
 * **************************************
 * v1.0.2                      Can Berkol
 * 08.01.2014
 * **************************************
 * A renderDashboardButtons()
 * A renderDashboardStatistics()
 * A renderSimpleTable()
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * 05.01.2014
 * **************************************
 * A renderProjectLogo()
 * A renderQuickActionsNavigation()
 * A renderSidebarNavigation()
 * A renderSidebarSeparator()
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 04.01.2014
 * **************************************
 * A renderDataTable()
 *
 */