<?php
/**
 * HTMLRenderController
 *
 * This class is used to render individual HTML elements.
 *
 * @vendor      BiberLtd
 * @package	    Core\Bundles\CoreBundle
 * @subpackage	Controller
 * @name	    HTMLController
 *
 * @author      Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.1.0
 * @date        15.04.2014
 *
 */

namespace BiberLtd\Bundle\CoreBundle\Controller;

use BiberLtd\Bundle\CoreBundle\CoreController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

class HTMLRenderController extends  CoreController{
    private $templating;
    private $theme;

    /**
     * @name            __construct()
     *                  Constructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           EngineInterface         $templating
     *
     */
    public  function __construct(EngineInterface $templating){
        $this->templating = $templating;
    }

    /**
     * @name            __destruct()
     *                  Destructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     */
    public function __destruct() {
        foreach ($this as $property => $value) {
            $this->$property = null;
        }
    }
    /**
     * @name 			renderBasicAjaxInput()
     *  				Renders a basic ajax file input that allows ajax file upload.
     *
     * @since			1.0.9
     * @version         1.0.9
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Form\field.input.upload_ajax_simple.html.smarty
     *
     * @param           array               $inputDetails
     *                                          string          $id
     *                                          string          $name
     *                                          integer         $size                   width, see bootstrap documentation for more information.
     *                                          string          $value
     *                                          string          $label
     *                                          array           $settings               showLabel => true,false & theme => 'cms'
     *                                          array           $classes                Array of strings that consists of class names.
     *                                          array           attributes
     * @return          string
     */
    public function renderBasicAjaxInput($inputDetails = array()){
        if(!isset($inputDetails['id']) || !isset($inputDetails['name'])){
            return '';
        }
        $settingsDefault = array(
            'acceptFileTypes'   => '/(\.|\/)(gif|jpe?g|png)$/i',
            'autoUpload'        => true,
            'concurrentUploads' => 1,
            'dataType'          => 'json',
            'disableImagePreview'   => true,
            'limitMultiFileUploads' => 1,
            'maxFileSize'       => 50000000, // 50 MB
            'maxNumberOfFiles'  => 1,
            'removable'         => false,
            'rowSize'           => 12,
            'showLabel'         => true,
            'singleFileUploads' => true,
            'theme'             => 'cms',
            'wrapInRow'         => true,
        );
        if(isset($inputDetails['settings'])){
            $settingsDefault = array_merge($settingsDefault, $inputDetails['settings']);
            unset($inputDetails['settings']);
        }
        $inputDefaults = array(
            'attributes'        => array(),
            'classes'           => array(),
            'txt'               => array(
                'cancel'            => '',
                'delete'            => '',
                'processing'        => '',
                'selectFiles'       => '',
                'uploadFailed'      => '',
            ),
            'label'             => '',
            'settings'          => $settingsDefault,
            'size'              => 12,
        );

        $inputDetails = array_merge($inputDefaults, $inputDetails);

        /**
         * Prepare $vars
         */
        $vars = array(
            'field' => array(
                'attributes'    => $inputDetails['attributes'],
                'txt'           => $inputDetails['txt'],
                'classes'       => $inputDetails['classes'],
                'id'            => $inputDetails['id'],
                'lbl'           => $inputDetails['label'],
                'name'          => $inputDetails['name'],
                'settings'      => array_merge($inputDefaults['settings'], $inputDetails['settings']),
                'size'          => $inputDetails['size'],
                'uploadUrl'     => $inputDetails['uploadUrl'],
                'value'         => $inputDetails['value'],
            ),
        );
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:'.$vars['field']['settings']['theme'].'/Form:field.input.upload_ajax_simple.html.smarty', $vars);
    }
    /**
     * @name 			renderCheckedMultiSelect()
     *  				Renders <select> element.
     *
     * @since			1.0.5
     * @version         1.0.5
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Form\field.checked_multiselect.html.smarty
     *
     * @param           array           $inputDetails
     *                                      string  id,
     *                                      string  name,
     *                                      integer size,
     *                                      string  type,
     *                                      array   options,
     *                                          string  name
     *                                          bool    selected
     *                                          string  value
     *                                      string  label,
     *                                      array   settings,
     *                                      array   classes
     *
     * @return          string
     */
    public function renderCheckedMultiSelect($inputDetails = array()){
        if(!isset($inputDetails['id']) || !isset($inputDetails['name'])){
            return '';
        }
        
        $settingsDefault = array(
            'maxHeight'         => 150,
            'showLabel'         => true,
            'theme'             => 'cms',
            'wrapInRow'         => true,
        );
        $inputDefaults = array(
            'attributes'    => array(),
            'classes'       => array(),
            'size'          => 12,
            'txt'           => array(),
            'label'         => '',
            'options'       => array(),
            'settings'      => array_merge($settingsDefault, $inputDetails['settings']),
        );
        unset($inputDetails['settings']);
        $inputDetails = array_merge($inputDefaults, $inputDetails);
        $templates = array();
        if(isset($inputDetails['templates'])){
            $templates = $inputDetails['templates'];
            unset($inputDetails['templates']);
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'field' => array(
                'attributes'    => $inputDetails['attributes'],
                'classes'       => $inputDetails['classes'],
                'id'            => $inputDetails['id'],
                'lbl'           => $inputDetails['label'],
                'name'          => $inputDetails['name'],
                'settings'      => $inputDetails['settings'],
                'size'          => $inputDetails['size'],
                'txt'           => $inputDetails['txt'],
                'options'       => $inputDetails['options'],
                'templates'     => $templates,
            ),
        );
        if (isset($inputDetails['grouppedOptions'])) {
            $vars['field']['grouppedOptions'] = $inputDetails['grouppedOptions'];
        }
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:'.$inputDetails['settings']['theme'].'/Form:field.checked_multiselect.html.smarty', $vars);
    }
    /**
     * @name 			renderCkEditorArea()
     *  				Renders <textarea> element with ckeditor class.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Form\field.textarea.html.smarty
     *
     * @use             $this->renderTextArea()
     *
     * @param           array           $inputDetails
     *
     * @return          string
     */
    public function renderCkEditorArea($inputDetails = array()){
        if(!isset($inputDetails['id']) || !isset($inputDetails['name'])){
            return '';
        }
        $inputDefaults = array(
            'classes'       => array('ckeditor'),
            'height'        => 9,
            'label'         => '',
            'settings'      => array(),
            'size'          => 12,
            'type'          => 'ckeditor',
            'value'         => '',
        );
        $inputDetails = array_merge($inputDefaults, $inputDetails);

        return $this->renderTextArea($inputDetails);
    }
    /**
     * @name 			renderDatePicker()
     *  				Renders <input> element with class = datepicker.
     *
     * @since			1.0.4
     * @version         1.0.4
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Form\field.input.html.smarty
     *
     * @use             $this->renderInput()
     *
     * @param           array           $inputDetails
     *
     * @return          string
     */
    public function renderDatePicker($inputDetails = array()){
        $defaultClasses = array('datepicker');
        $inputDetails['type'] = 'text';
        $inputDetails['specialType'] = 'datePicker';
        if(isset($inputDetails['classes'])){
            $inputDetails['classes'] = array_merge($defaultClasses, $inputDetails['classes']);
        }
        else{
            $inputDetails['classes'] = $defaultClasses;
        }
        return $this->renderInput($inputDetails);
    }
    /**
     * @name 			renderDropDown()
     *  				Renders <select> element.
     *
     * @since			1.0.3
     * @version         1.0.8
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Form\field.dropdown.html.smarty
     *
     * @param           array           $inputDetails
     *                                      string  id,
     *                                      string  name,
     *                                      integer size,
     *                                      string  type,
     *                                      array   options,
     *                                          string  name
     *                                          bool    selected
     *                                          string  value
     *                                      string  label,
     *                                      array   settings,
     *                                      array   classes
     *
     * @return          string
     */
    public function renderDropDown($inputDetails = array()){
        if(!isset($inputDetails['id']) || !isset($inputDetails['name'])){
            return '';
        }
        $settingsDefault = array(
            'showLabel'         => true,
            'theme'             => 'cms',
            'wrapInRow'         => true,
        );
        if(isset($inputDetails['settings'])){
            $settingsDefault = array_merge($settingsDefault, $inputDetails['settings']);
        }
        $inputDefaults = array(
            'attributes'    => array(),
            'classes'       => array(),
            'size'          => 12,
            'label'         => '',
            'options'       => array(),
            'settings'      => $settingsDefault,
        );
        unset($inputDetails['settings']);
        $inputDetails = array_merge($inputDefaults, $inputDetails);

        /**
         * Prepare $vars
         */
        $vars = array(
            'field' => array(
                'attributes'    => $inputDetails['attributes'],
                'classes'       => $inputDetails['classes'],
                'id'            => $inputDetails['id'],
                'lbl'           => $inputDetails['label'],
                'name'          => $inputDetails['name'],
                'settings'      => $inputDetails['settings'],
                'size'          => $inputDetails['size'],
                'options'       => $inputDetails['options'],
            ),
        );
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:'.$inputDetails['settings']['theme'].'/Form:field.dropdown.html.smarty', $vars);
    }
    /**
     * @name 			renderHiddenInput()
     *  				Renders <input> element with type = hidden.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Form\field.input.html.smarty
     *
     * @use             $this->renderInput()
     *
     * @param           array           $inputDetails
     *
     * @return          string
     */
    public function renderHiddenInput($inputDetails = array()){
        $inputDetails['type'] = 'hidden';
        return $this->renderInput($inputDetails);
    }
    /**
     * @name 			renderInput()
     *  				Renders <input> element.
     *
     * @since			1.0.0
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Form\field.input.html.smarty
     *
     * @param           array           $inputDetails
     *                                      string  id,
     *                                      string  name,
     *                                      integer size,
     *                                      string  type,
     *                                      string  value,
     *                                      string  label,
     *                                      array   settings,
     *                                      array   classes
     *
     * @return          string
     */
    public function renderInput($inputDetails = array()){
        if(!isset($inputDetails['id']) || !isset($inputDetails['name'])){
            return '';
        }
        $settingsDefault = array(
            'showLabel'         => true,
            'theme'             => 'cms',
            'wrapInRow'         => true,
        );
        if(isset($inputDetails['settings'])){
            $settingsDefault = array_merge($settingsDefault, $inputDetails['settings']);
        }
        $inputDefaults = array(
            'attributes'    => array(),
            'classes'       => array(),
            'size'          => 12,
            'label'         => '',
            'placeHolder'   => '',
            'settings'      => $settingsDefault,
            'specialType'   => '',
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
                'attributes'    => $inputDetails['attributes'],
                'classes'       => $inputDetails['classes'],
                'id'            => $inputDetails['id'],
                'lbl'           => $inputDetails['label'],
                'name'          => $inputDetails['name'],
                'placeHolder'   => $inputDetails['placeHolder'],
                'settings'      => $inputDetails['settings'],
                'size'          => $inputDetails['size'],
                'type'          => $inputDetails['type'],
                'specialType'   => $inputDetails['specialType'],
                'value'         => $inputDetails['value'],
            ),
        );
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:'.$inputDetails['settings']['theme'].'/Form:field.input.html.smarty', $vars);
    }
    /**
     * @name 			renderLocationPicker()
     *  				Renders a google map based location picker.
     *
     * @since			1.0.7
     * @version         1.0.7
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Form\field.locationpicker.html.smarty
     *
     * @use             $this->renderInput()
     *
     * @param           array           $inputDetails
     *
     * @return          string
     */
    public function renderLocationPicker($inputDetails = array()){
        if(!isset($inputDetails['id']) || !isset($inputDetails['name'])){
            return '';
        }
        $defaultLocation = array(
            'address'           => '',
            'lat'               => 39.92077,
            'lon'               => 32.85410999999999,
        );
        if((!isset($inputDetails['defaultLocation']['lat']) || !is_null($inputDetails['defaultLocation']['lat'])) && (!isset($inputDetails['defaultLocation']['lon']) || !is_null($inputDetails['defaultLocation']['lon']))){
        $defaultLocation = array_merge($defaultLocation, $inputDetails['defaultLocation']);
        }

        $settingsDefault = array(
            'autoComplete'      => true,
            'height'            => '400px',
            'radius'            => 0,
            'reverseGeocode'    => true,
            'rowSize'           => 12,
            'searchEnabled'     => true,
            'scrollWheel'       => true,
            'showLabel'         => true,
            'theme'             => 'cms',
            'wrapInRow'         => true,
            'zoom'              => 14,
        );
        if(isset($inputDetails['settings'])){
            $settingsDefault = array_merge($settingsDefault, $inputDetails['settings']);
        }
        $settingsDefault['defaultLocation'] = $defaultLocation;
        $inputDefaults = array(
            'attributes'        => array(),
            'classes'           => array(),
            'label'             => '',
            'settings'          => $settingsDefault,
            'size'              => 12,
            'value'             => '',
        );
        $inputDetails = array_merge($inputDefaults, $inputDetails);

        /**
         * Prepare $vars
         */
        $vars = array(
            'field' => array(
                'attributes'    => $inputDetails['attributes'],
                'classes'       => $inputDetails['classes'],
                'id'            => $inputDetails['id'],
                'lbl'           => $inputDetails['label'],
                'name'          => $inputDetails['name'],
                'settings'      => array_merge($inputDefaults['settings'], $inputDetails['settings']),
                'size'          => $inputDetails['size'],
                'type'          => $inputDetails['type'],
                'value'         => $inputDetails['value'],
            ),
        );

        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:'.$vars['field']['settings']['theme'].'/Form:field.locationpicker.html.smarty', $vars);
    }
    /**
     * @name 			renderPasswordInput()
     *  				Renders <input> element with type = password.
     *
     * @since			1.0.4
     * @version         1.0.4
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Form\field.input.html.smarty
     *
     * @use             $this->renderInput()
     *
     * @param           array           $inputDetails
     *
     * @return          string
     */
    public function renderPasswordInput($inputDetails = array()){
        $inputDetails['type'] = 'password';
        return $this->renderInput($inputDetails);
    }
    /**
     * @name 			renderSingleImageInput()
     *  				Renders an advanced file input that allows single file upload and shows file preview.
     *
     * @since			1.0.6
     * @version         1.1.0
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Form\field.input.upload_preview.html.smarty
     *
     * @param           array               $inputDetails
     *                                          string          $id
     *                                          string          $name
     *                                          integer         $size                   width, see bootstrap documentation for more information.
     *                                          string          $value
     *                                          string          $label
     *                                          array           $settings               showLabel => true,false & theme => 'cms'
     *                                          array           $classes                Array of strings that consists of class names.
     *                                          array           attributes
     * @return          string
     */
    public function renderSingleImageInput($inputDetails = array()){
        if(!isset($inputDetails['id']) || !isset($inputDetails['name'])){
            return '';
        }
        $settingsDefault = array(
            'removable'         => false,
            'rowSize'           => 12,
            'theme'             => 'cms',
            'wrapInRow'         => true,
        );
        if(isset($inputDetails['settings'])){
            $settingsDefault = array_merge($settingsDefault, $inputDetails['settings']);
        }
        $inputDefaults = array(
            'attributes'        => array(),
            'classes'           => array(),
            'btn'               => array(
                'change'            => '',
                'select'            => '',
                'remove'            => '',
            ),
            'height'            => 9,
            'label'             => '',
            'settings'          => $settingsDefault,
            'size'              => 12,
            'type'              => 'textInput',
            'value'             => '',
        );

        $inputDetails = array_merge($inputDefaults, $inputDetails);

        /**
         * Prepare $vars
         */
        $vars = array(
            'field' => array(
                'attributes'    => $inputDetails['attributes'],
                'btn'           => $inputDetails['btn'],
                'classes'       => $inputDetails['classes'],
                'id'            => $inputDetails['id'],
                'height'        => $inputDetails['height'],
                'lbl'           => $inputDetails['label'],
                'name'          => $inputDetails['name'],
                'settings'      => array_merge($inputDefaults['settings'], $inputDetails['settings']),
                'size'          => $inputDetails['size'],
                'type'          => $inputDetails['type'],
                'value'         => $inputDetails['value'],
            ),
        );
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:'.$vars['field']['settings']['theme'].'/Form:field.input.upload_preview.html.smarty', $vars);
    }
    /**
     * @name 			renderTagInput()
     *  				Renders <input> element with type = text & class = tags-input.
     *
     * @since			1.0.0
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Form\field.input.html.smarty
     *
     * @use             $this->renderInput()
     *
     * @param           array           $inputDetails
     *
     * @return          string
     */
    public function renderTagInput($inputDetails = array()){
        $defaultClasses = array('tags-input');
        $inputDetails['type'] = 'text';
        $inputDetails['specialType'] = 'tagsInput';
        if(isset($inputDetails['classes'])){
            $inputDetails['classes'] = array_merge($defaultClasses, $inputDetails['classes']);
        }
        else{
            $inputDetails['classes'] = $defaultClasses;
        }
        
        return $this->renderInput($inputDetails);
    }
    /**
     * @name 			renderTextArea()
     *  				Renders <textarea> element.
     *
     * @since			1.0.0
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @see             BiberLtd\Bundle\CoreBundle\Resources\views\cms\Form\field.textarea.html.smarty
     *
     * @param           array               $inputDetails
     *                                          string          $id
     *                                          string          $name
     *                                          integer         $size                   width, see bootstrap documentation for more information.
     *                                          integer         $height
     *                                          string          $value
     *                                          string          $label
     *                                          array           $settings               showLabel => true,false & theme => 'cms'
     *                                          array           $classes                Array of strings that consists of class names.
     *
     * @return          string
     */
    public function renderTextArea($inputDetails = array()){
        if(!isset($inputDetails['id']) || !isset($inputDetails['name'])){
            return '';
        }
        $settingsDefault = array(
            'showLabel'         => true,
            'theme'             => 'cms',
            'wrapInRow'         => true,
        );
        if(isset($inputDetails['settings'])){
            $settingsDefault = array_merge($settingsDefault, $inputDetails['settings']);
        }
        $inputDefaults = array(
            'attributes'        => array(),
            'classes'           => array(),
            'height'            => 9,
            'label'             => '',
            'size'              => 12,
            'type'              => 'textarea',
            'value'             => '',
        );
        $inputDetails = array_merge($inputDefaults, $inputDetails);
        /**
         * Prepare $vars
         */
        $vars = array(
            'field' => array(
                'attributes'    => $inputDetails['attributes'],
                'classes'       => $inputDetails['classes'],
                'id'            => $inputDetails['id'],
                'height'        => $inputDetails['height'],
                'lbl'           => $inputDetails['label'],
                'name'          => $inputDetails['name'],
                'settings'      => $settingsDefault,
                'size'          => $inputDetails['size'],
                'type'          => $inputDetails['type'],
                'value'         => $inputDetails['value'],
            ),
        );
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:'.$vars['field']['settings']['theme'].'/Form:field.textarea.html.smarty', $vars);
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
     * @param           array            $inputDetails          that contains the following information:
     *                                                          $id, $name, $size = 12, $value = '', $label = '', $settings = array(), $classes = array()
     *
     * @return          string
     */
    public function renderTextInput($inputDetails = array()){
        $inputDetails['type'] = 'text';
        return $this->renderInput($inputDetails);
    }

}

/**
 * 
 * Change Log
 * **************************************
 * v1.1.0                      Can Berkol
 * 11.04.2014
 * **************************************
 * U renderSingleImageInput()
 *
 * **************************************
 * v1.0.9                      Can Berkol
 * 11.04.2014
 * **************************************
 * A renderBasicAjaxInput()
 *
 * **************************************
 * v1.0.8                      Can Berkol
 * 16.03.2014
 * **************************************
 * B renderDropDown()
 *
 * **************************************
 * v1.0.7                      Can Berkol
 * 04.03.2014
 * **************************************
 * A renderLocationPicker()
 *
 * **************************************
 * v1.0.6                      Can Berkol
 * 17.02.2014
 * **************************************
 * A renderSingleImageInput()
 *
 * **************************************
 * v1.0.5                      Can Berkol
 * 17.02.2014
 * **************************************
 * A renderCheckedMultiSelect()
 *
 * **************************************
 * v1.0.4                      Can Berkol
 * 05.02.2014
 * **************************************
 * A renderDatePicker()
 * A renderPasswordInput()
 *
 * **************************************
 * v1.0.3                      Can Berkol
 * 29.01.2014
 * **************************************
 * A renderDropDown()
 *
 * **************************************
 * v1.0.2                      Can Berkol
 * 28.01.2014
 * **************************************
 * U renderInput()
 * U renderTextArea()
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * 20.01.2014
 * **************************************
 * B renderTagInput()
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 16.01.2014
 * **************************************
 * A __construct()
 * A __destruct()
 * A renderCkEditorArea()
 * A renderHiddenInput()
 * A renderInput()
 * A renderTagInput()
 * A renderTextArea()
 * A renderTextInput()
 */
    