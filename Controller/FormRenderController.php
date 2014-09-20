<?php
/**
 * FormRenderController.php
 *
 * Handles all article class related renderings.
 *
 * @vendor         BiberLtd
 * @package        CoreBundle
 * @subpackage     Controller
 * @name           FormRenderController
 *
 * @author         Can Berkol
 *
 * @copyright      Biber Ltd. (www.biberltd.com)
 *
 * @version        1.0.3
 * @date           30.05.2014
 *
 */

namespace BiberLtd\Bundle\CoreBundle\Controller;

use BiberLtd\Bundle\CoreBundle\CoreController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

class FormRenderController extends CoreController{

    private $templating;
    private $theme;

    public function __construct(EngineInterface $templating) {
        $this->templating = $templating;
    }
    /**
     * @name            renderAutoSizeTextArea()
     *                  Renders Autosize Area
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @param           array           $inputDetails
     * @param           array           $core
     *
     * @return          array           $response
     */
    public function renderAutosizeTextArea($inputDetails, $core = null){
        $inputDetails['settings']['autosize'] = true;
        return $this->renderTextArea($inputDetails, $core);
    }
    /**
     * @name            renderCkEditor()
     *                  Renders CkEditor Area
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @param           array           $inputDetails
     * @param           array           $core
     *
     * @return          array           $response
     */
    public function renderCkEditorArea($inputDetails, $core = null){
        $inputDetails['settings']['ckeditor'] = true;
        return $this->renderTextArea($inputDetails, $core);
    }
    /**
     * @name            renderFullscreenTextArea()
     *                  Renders Full screen Area
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @param           array           $inputDetails
     * @param           array           $core
     *
     * @return          array           $response
     */
    public function renderFullScreenTextArea($inputDetails, $core = null){
        $inputDetails['settings']['autosize'] = true;
        return $this->renderTextArea($inputDetails, $core);
    }
    /**
     * @name 			renderImageInput()
     *  				Renders <input> element with type file and image preview capacities.
     *
     * @since			1.0.0
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\bibercrm\FormElements\input.html.smarty
     *
     * @param           array           $inputDetails
     *
     * @return          string
     */
    public function renderImageInput($inputDetails = array()){
        $this->resetRenderResponse();
        if(!isset($inputDetails['id']) || !isset($inputDetails['name'])){
            return '';
        }
        $settingsDefault = array(
            'height'            => 150,
            'showLabel'         => true,
            'showSaveButton'    => false,
            'removable'         => false,
            'width'             => 200,
            'wrapInGroup'       => false,
        );
        if(isset($inputDetails['settings'])){
            $settingsDefault = array_merge($settingsDefault, $inputDetails['settings']);
        }
        $inputDefaults = array(
            'classes'       => array(),
            'label'         => '',
            'settings'      => $settingsDefault,
            'value'         => '',
        );
        unset($inputDetails['settings']);
        $textDefaults = array(
            'select'        => 'Select Image',
            'change'        => 'Change',
            'remove'        => 'Remove',
            'save'          => 'Save',
        );
        $inputDetails = array_merge($inputDefaults, $inputDetails);
        $inputDetails['text'] = array_merge($textDefaults, $inputDetails['text']);
        /**
         * Prepare $vars
         */
        $vars = array(
            'field' => array(
                'classes'       => $inputDetails['classes'],
                'id'            => $inputDetails['id'],
                'label'         => $inputDetails['label'],
                'name'          => $inputDetails['name'],
                'settings'      => $inputDetails['settings'],
                'tabIndex'      => 1,
                'text'          => $inputDetails['text'],
                'value'         => $inputDetails['value'],
            ),
        );
        if(isset($inputDetails['attributes'])){
            $vars['field']['attributes'] = $inputDetails['attributes'];
        }
        $this->response['head']['js'] = array('/plugins/form-jasnyupload/fileinput.min.js');
        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:'.$inputDetails['settings']['theme'].'/FormElements:imageInput.html.smarty', $vars);

        return $this->response;
    }
    /**
     * @name 			renderInput()
     *  				Renders <input> element.
     *
     * @since			1.0.0
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\bibercrm\FormElements\input.html.smarty
     *
     * @param           array           $inputDetails
     *
     * @return          string
     */
    public function renderInput($inputDetails = array()){
        $this->resetRenderResponse();
        if(!isset($inputDetails['id']) || !isset($inputDetails['name'])){
            return '';
        }
        $settingsDefault = array(
            'disabled'          => false,
            'labelPosition'     => 'top',
            'readonly'          => false,
            'showHelpBlock'     => false,
            'showTooltipHelp'   => false,
            'showLabel'         => true,
            'theme'             => 'bibercrm',
            'wrapInGroup'       => false,
        );
        if(isset($inputDetails['settings'])){
            $settingsDefault = array_merge($settingsDefault, $inputDetails['settings']);
        }
        if($settingsDefault['showTooltipHelp']){
            $settingsDefault['showHelpBlock'] = false;
        }
        if($settingsDefault['showHelpBlock']){
            $settingsDefault['showTooltipHelp'] = false;
        }
        $inputDefaults = array(
            'classes'       => array(),
            'label'         => '',
            'placeHolder'   => '',
            'settings'      => $settingsDefault,
            'specialType'   => '',
            'tabIndex'      => 1,
            'value'         => '',
        );
        unset($inputDetails['settings']);
        $inputDetails = array_merge($inputDefaults, $inputDetails);
        /**
         * Prepare $vars
         */
        $vars = array(
            'field' => array(
                'classes'       => $inputDetails['classes'],
                'id'            => $inputDetails['id'],
                'label'         => $inputDetails['label'],
                'name'          => $inputDetails['name'],
                'placeHolder'   => $inputDetails['placeHolder'],
                'settings'      => $inputDetails['settings'],
                'tabIndex'      => $inputDetails['tabIndex'],
                'type'          => $inputDetails['type'],
                'value'         => $inputDetails['value'],
            ),
        );
        if(isset($inputDetails['attributes'])){
            $vars['field']['attributes'] = $inputDetails['attributes'];
        }
        if(isset($inputDetails['helpText'])){
            $vars['field']['helpText'] = $inputDetails['helpText'];
        }
        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:'.$inputDetails['settings']['theme'].'/FormElements:input.html.smarty', $vars);

        return $this->response;
    }
    /**
     * @name 			renderHiddenInput()
     *  				Renders <input> element with type = text.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Form\field.input.html.smarty
     *
     * @use             $this->renderInput()
     *
     * @param           array            $inputDetails
     *
     * @return          string
     */
    public function renderHiddenInput($inputDetails = array()){
        $inputDetails['type'] = 'hidden';
        return $this->renderInput($inputDetails);
    }
    /**
     * @name 			renderPasswordInput()
     *  				Renders <input> element with type = text.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Form\field.input.html.smarty
     *
     * @use             $this->renderInput()
     *
     * @param           array            $inputDetails
     *
     * @return          string
     */
    public function renderPasswordInput($inputDetails = array()){
        $inputDetails['type'] = 'hidden';
        return $this->renderInput($inputDetails);
    }
    /**
     * @name 			renderTagInput()
     *  				Renders an advance <input> element that is used for tagging purposes.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\bibercrm\FormElements\input.html.smarty
     *
     * @param           array           $inputDetails
     *
     * @return          string
     */
    public function renderTagInput($inputDetails = array()){
        $this->resetRenderResponse();
        if(!isset($inputDetails['id']) || !isset($inputDetails['name'])){
            return '';
        }
        $settingsDefault = array(
            'disabled'          => false,
            'predefinedTags'    => array(),
            'readonly'          => false,
            'showHelpBlock'     => false,
            'showTooltipHelp'   => false,
            'showLabel'         => true,
            'tabIndex'          => 1,
            'wrapInGroup'       => false,
        );
        if(isset($inputDetails['settings'])){
            $settingsDefault = array_merge($settingsDefault, $inputDetails['settings']);
        }
        if($settingsDefault['showTooltipHelp']){
            $settingsDefault['showHelpBlock'] = false;
        }
        if($settingsDefault['showHelpBlock']){
            $settingsDefault['showTooltipHelp'] = false;
        }
        $inputDefaults = array(
            'classes'       => array(),
            'label'         => '',
            'placeHolder'   => '',
            'settings'      => $settingsDefault,
            'specialType'   => '',
            'tabIndex'      => 1,
            'value'         => '',
            'values'        => ''
        );
        unset($inputDetails['settings']);
        $inputDetails = array_merge($inputDefaults, $inputDetails);

        /**
         * Prepare $vars
         */
        $vars = array(
            'field' => array(
                'classes'       => $inputDetails['classes'],
                'id'            => $inputDetails['id'],
                'label'         => $inputDetails['label'],
                'name'          => $inputDetails['name'],
                'placeHolder'   => $inputDetails['placeHolder'],
                'settings'      => $inputDetails['settings'],
                'tabIndex'      => 1,
                'value'         => $inputDetails['value'],
            ),
        );
        if(isset($inputDetails['attributes'])){
            $vars['field']['attributes'] = $inputDetails['attributes'];
        }
        if(isset($inputDetails['helpText'])){
            $vars['field']['helpText'] = $inputDetails['helpText'];
        }
        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:'.$inputDetails['settings']['theme'].'/FormElements:tagInput.html.smarty', $vars);
        $this->response['head']['css'] = array('/plugins/form-select2/select2.css');
        $this->response['head']['js'] = array('/plugins/form-select2/select2.min.js');

        return $this->response;
    }
    /**
     * @name 			renderTextInput()
     *  				Renders <input> element with type = text.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Form\field.input.html.smarty
     *
     * @use             $this->renderInput()
     *
     * @param           array            $inputDetails
     *
     * @return          string
     */
    public function renderTextInput($inputDetails = array()){
        $inputDetails['type'] = 'text';
        return $this->renderInput($inputDetails);
    }
    /**
     * @name            renderTextArea()
     *                  Renders <textarea></textarea>
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @param           array           $inputDetails
     *
     * @return          array           $response
     */
    public function renderTextArea($inputDetails = array()){
        $this->resetRenderResponse();
        if(!isset($inputDetails['id']) || !isset($inputDetails['name'])){
            return '';
        }
        $settingsDefault = array(
            'autosize'          => false,
            'ckeditor'          => false,
            'disabled'          => false,
            'fullscreen'        => false,
            'labelPosition'     => 'top',
            'readonly'          => false,
            'showHelpBlock'     => false,
            'showTooltipHelp'   => false,
            'showLabel'         => true,
            'theme'             => 'bibercrm',
            'wrapInGroup'       => false,
        );
        if(isset($inputDetails['settings'])){
            $settingsDefault = array_merge($settingsDefault, $inputDetails['settings']);
        }
        if($settingsDefault['showTooltipHelp']){
            $settingsDefault['showHelpBlock'] = false;
        }
        if($settingsDefault['showHelpBlock']){
            $settingsDefault['showTooltipHelp'] = false;
        }
        if($settingsDefault['ckeditor']){
            $settingsDefault['autosize'] = false;
            $settingsDefault['fullscreen'] = false;
        }
        $inputDefaults = array(
            'attributes'    => array(),
            'classes'       => array(),
            'ckeditor'      => array(),
            'fullscreen'    => array(),
            'helpText'      => '',
            'label'         => '',
            'placeHolder'   => '',
            'settings'      => $settingsDefault,
            'specialType'   => '',
            'tabIndex'      => 1,
            'value'         => ''
        );
        unset($inputDetails['settings']);
        $inputDetails = array_merge($inputDefaults, $inputDetails);

        if(isset($inputDetails['ckeditor']) && isset($inputDetails['ckeditor']['style'])){
            $inputDetails['ckeditor']['style'] = $this->ckEditorToolbarSetup($inputDetails['ckeditor']['style']);
        }
        else{
            $inputDetails['ckeditor']['style'] = $this->ckEditorToolbarSetup('standard');
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'field' => array(
                'classes'       => $inputDetails['classes'],
                'id'            => $inputDetails['id'],
                'label'         => $inputDetails['label'],
                'name'          => $inputDetails['name'],
                'placeHolder'   => $inputDetails['placeHolder'],
                'settings'      => $inputDetails['settings'],
                'tabIndex'      => 1,
                'value'         => $inputDetails['value'],
            ),
        );
        if(isset($inputDetails['attributes'])){
            $vars['field']['attributes'] = $inputDetails['attributes'];
        }
        if(isset($inputDetails['helpText'])){
            $vars['field']['helpText'] = $inputDetails['helpText'];
        }
        if($inputDetails['settings']['ckeditor']){
            $vars['field']['ckeditor'] = $inputDetails['ckeditor'];
        }
        $this->response['html'] = $this->templating->render('BiberLtdBundleCoreBundle:'.$inputDetails['settings']['theme'].'/FormElements:textarea.html.smarty', $vars);

        if($inputDetails['settings']['ckeditor']){
            $this->response['head']['js'] = array('/plugins/form-ckeditor/ckeditor.js');
        }
        if($inputDetails['settings']['fullscreen']){
            $this->response['head']['css'] = array('/plugins/form-fseditor/fseditor.css');
            $this->response['head']['js'] = array('/plugins/form-fseditor/jquery.fseditor-min.js');
        }
        if($inputDetails['settings']['autosize']){
            $this->response['head']['js'] = array('/plugins/form-autosize/jquery.autosize.js');
        }
        return $this->response;
    }

    /**
     * PRIVATE FUNCTIONS
     *
     * These are mainly helper functions.
     */
    /**
     * @name            renderTextArea()
     *                  Renders <textarea></textarea>
     *
     * @since           1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @param           string           $style
     *
     * @return          array           $response
     */
    private function ckEditorToolbarSetup($style = 'standard'){
        $toolbarConfig = array(
            'standard' => "[
                                { name: 'basicstyles', groups: [ 'basicstyles' ], items: [ 'Bold', 'Italic', 'Subscript', 'Superscript' ] },
                                { name: 'paragraph', groups: [ 'list', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                                { name: 'links', items: [ 'Link', 'Unlink'  ] },
                                { name: 'tools', items: [ 'Maximize' ] },
                                { name: 'styles', items: [ 'Format'  ] }
                           ]",
            'loaded'   => '',
            'advanced' => '',
            'all'      => "[
                                { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
                                { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                                { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
                                { name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
                                '/',
                                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
                                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
                                { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                                { name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
                                '/',
                                { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                                { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
                                { name: 'others', items: [ '-' ] },
                                { name: 'about', items: [ 'About' ] }
                          ]"
        );
        return $toolbarConfig[$style];
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.3                      Can Berkol
 * 30.05.2014
 * **************************************
 * - U renderInputImage()
 *
 * **************************************
 * v1.0.2                      Can Berkol
 * 29.05.2014
 * **************************************
 * tabIndex option added to all methods.
 *
 * A ckEditorToolbarSetup()
 * A renderAutoSizeTextArea()
 * A renderCkEditorArea()
 * A renderFullScreenTextArea()
 * A renderTextArea()
 *
 * **************************************
 * v1.0.1                      Can Berkol
 *                         Selimcan Sakar
 * 07.05.2014
 * **************************************
 * A renderImageInput()
 * A renderTagInput()
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 02.05.2014
 * **************************************
 * A renderInput()
 * A renderHiddenInput()
 * A renderPasswordInput()
 * A renderTextInput()
 *
 */