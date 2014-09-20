<?php
/**
 * TypographyRenderController.php
 *
 * Handles all article class related renderings.
 *
 * @vendor         BiberLtd
 * @package        CoreBundle
 * @subpackage     Controller
 * @name           TypographyRenderController
 *
 * @author         Can Berkol
 *
 * @copyright      Biber Ltd. (www.biberltd.com)
 *
 * @version        1.0.0
 * @date           23.04.2014
 *
 */

namespace BiberLtd\Bundle\CoreBundle\Controller;

use BiberLtd\Bundle\CoreBundle\CoreController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

class TypographyRenderController extends  CoreController{

    private $templating;
    private $theme;

    public function __construct(EngineInterface $templating) {
        $this->templating = $templating;
    }
    /**
     * @name            renderAddress()
     *                  Renders <address>Some text</address>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           string          name            Full name of person or company name
     * @param           array           $address        Address information
     *                                                  Keys accepted: city, country, state, street, zip, taxOffice, taxId
     * @param           array           $contactInformation
     *                                                  Holds arrays with the following keys: full, initial, value
     *                                                  array(array('full' => 'E-Mail', 'initial' => 'I', 'value' => 'info@biberltd.com'))
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderAddress($name, $address, $contactInformation = array(), $core = null){
        /**
         * Prepare $vars
         */
        $vars = array(
            'address'                   => $address,
            'contactInformation'        => $contactInformation,
            'name'                      => $name,
        );
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:' . $core['theme'] . '/HtmlParts:address.html.smarty', $vars);
    }
    /**
     * @name            renderBlockquote()
     *                  Renders <blockquote> </blockquote>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           string          $author
     * @param           string          $quote
     * @param           mixed           $citation       if not null then array with keys description, and title.
     * @param           mixed           $core           array or null
     *
     * @return          string
     */
    public function renderBlockquote($author, $quote, $citation = null, $classes = array(), $core = null){
        if(is_null($core)){
            $core['theme'] = 'bibercrm';
        }
        if(!is_array($classes)){
            $classes = array();
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'author'        => $author,
            'classes'       => $classes,
            'quote'         => $quote,
        );
        if(!is_null($citation)){
            $vars['citation'] = $citation;
        }
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:' . $core['theme'] . '/HtmlParts:blockquote.html.smarty', $vars);
    }
    /**
     * @name            renderBlockquoteAlignedToRight()
     *                  Renders <blockquote class="pull-right"> </blockquote>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderBlockquote()
     *
     * @param           string          $author
     * @param           string          $quote
     * @param           mixed           $citation       if not null then array with keys description, and title.
     * @param           mixed           $core           array or null
     *
     * @return          string
     */
    public function renderBlockquoteAlignedToRight($author, $quote, $citation = null, $classes = array(), $core = null){
        if(!is_array($classes)){
            $classes = array();
        }
        $defaultClasses = array('pull-right');
        $classes = array_merge($defaultClasses, $classes);
        return $this->renderBlockquote($author, $quote, $citation, $classes, $core);
    }
    /**
     * @name            renderCodeBlock()
     *                  Renders <code> </code>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           string              $content            Any string
     * @param           mixed               $core
     *
     * @return          string
     */
    public function renderCodeBlock($content, $core = null){
        if(is_null($core)){
            $core['theme'] = 'bibercrm';
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'content'       => $content,
        );
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:' . $core['theme'] . '/HtmlParts:code.html.smarty', $vars);
    }
    /**
     * @name            renderDangerParagraph()
     *                  Renders <p class="text-danger">Some text</p>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderParagraph()
     * @use             $this->_mergeDefaultClass()
     *
     * @param           string          $content
     * @param           array           $classes        List of class names.
     * @param           array           $attributes     key => value pairs of attributes where key is attribute name
     *                                                  and value is attribute value.
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderDangerParagraph($content, $classes = array(), $attributes = array(), $core = null){
        $classes = $this->_mergeDefaultClass('text-danger', $classes);
        return $this->renderParagraph($content, $classes, $attributes, $core);
    }
    /**
     * @name            renderDescriptionList()
     *                  Renders <dl> </dl>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           array           $descriptionList        Holds array of arrays with keys description, and title.
     * @param           array           $classes                List of class names
     * @param           mixed           $core
     *
     * @return          string
     */
    public function renderDescriptionList($descriptionList, $classes = array(), $core = null){
        if(is_null($core)){
            $core['theme'] = 'bibercrm';
        }
        if(!is_array($classes)){
            $classes = array();
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'classes'           => $classes,
            'descriptionList'   => $descriptionList,
        );
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:' . $core['theme'] . '/HtmlParts:descriptionList.html.smarty', $vars);
    }
    /**
     * @name            renderHeading()
     *                  Renders <h1> <h2> etc..
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           string          $content
     * @param           integer         $level          Heading level - 1 through 6.
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderHeading($content, $level = 1, $core = null){
        if(is_null($core)){
            $core['theme'] = 'bibercrm';
        }
        /** ıf level is not numeric or if it is less than 1 or more than 6 reset level to 1 */
        if(!is_numeric($level)){
            $level = 1;
        }
        if($level < 1 || $level > 6){
            $level = 1;
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'content'       => $content,
            'level'         => $level,
        );
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:' . $core['theme'] . '/HtmlParts:heading.html.smarty', $vars);
    }
    /**
     * @name            renderHorizontalDescriptionList()
     *                  Renders <dl class="dl-horizontal"> </dl>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderDescriptionList()
     * @use             $this->_mergeDefaultClass()
     *
     * @param           array           $descriptionList        Holds array of arrays with keys description, and title.
     * @param           array           $classes                List of class names
     * @param           mixed           $core
     *
     * @return          string
     */
    public function renderHorizontalDescriptionList($descriptionList, $classes = array(), $core = null){
        $classes = $this->_mergeDefaultClass('dl-horizontal', $classes);
        return $this->renderDescriptionList($descriptionList, $classes, $core);
    }
    /**
     * @name            renderH1()
     *                  Renders <h1></h1>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderHeading()
     *
     * @param           string          $content
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderH1($content, $core = null){
        return $this->renderHeading($content, 1, $core);
    }
    /**
     * @name            renderH2()
     *                  Renders <h2></h2>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderHeading()
     *
     * @param           string          $content
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderH2($content, $core = null){
        return $this->renderHeading($content, 2, $core);
    }
    /**
     * @name            renderH3()
     *                  Renders <h3></h3>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           string          $content
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderH3($content, $core = null){
        return $this->renderHeading($content, 3, $core);
    }
    /**
     * @name            renderH4()
     *                  Renders <h4></h4>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderHeading()
     *
     * @param           string          $content
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderH4($content, $core = null){
        return $this->renderHeading($content, 4, $core);
    }
    /**
     * @name            renderH5()
     *                  Renders <h5></h5>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderHeading()
     *
     * @param           string          $content
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderH5($content, $core = null){
        return $this->renderHeading($content, 5, $core);
    }
    /**
     * @name            renderH6()
     *                  Renders <h6></h6>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderHeading()
     *
     * @param           string          $content
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderH6($content, $core = null){
        return $this->renderHeading($content, 6, $core);
    }
    /**
     * @name            renderInfoParagraph()
     *                  Renders <p class="text-info">Some text</p>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderParagraph()
     * @use             $this->_mergeDefaultClass()
     *
     * @param           string          $content
     * @param           array           $classes        List of class names.
     * @param           array           $attributes     key => value pairs of attributes where key is attribute name
     *                                                  and value is attribute value.
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderInfoParagraph($content, $classes = array(), $attributes = array(), $core = null){
        $classes = $this->_mergeDefaultClass('text-info', $classes);
        return $this->renderParagraph($content, $classes, $attributes, $core);
    }
    /**
     * @name            renderItalicText()
     *                  Renders <em>Some text</em>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           string          $content
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderItalicText($content, $core = null){
        if(is_null($core)){
            $core['theme'] = 'bibercrm';
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'content'       => $content,
        );
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:' . $core['theme'] . '/HtmlParts:em.html.smarty', $vars);
    }
    /**
     * @name            renderLeadParagraph()
     *                  Renders <p class="lead">Some text</p>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderParagraph()
     * @use             $this->_mergeDefaultClass()
     *
     * @param           string          $content
     * @param           array           $classes        List of class names.
     * @param           array           $attributes     key => value pairs of attributes where key is attribute name
     *                                                  and value is attribute value.
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderLeadParagraph($content, $classes = array(), $attributes = array(), $core = null){
        $classes = $this->_mergeDefaultClass('lead', $classes);
        return $this->renderParagraph($content, $classes, $attributes, $core);
    }
    /**
     * @name            renderMutedParagraph()
     *                  Renders <p>Some text</p>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderParagraph()
     * @use             $this->_mergeDefaultClass()
     *
     * @param           string          $content
     * @param           array           $classes        List of class names.
     * @param           array           $attributes     key => value pairs of attributes where key is attribute name
     *                                                  and value is attribute value.
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderMutedParagraph($content, $classes = array(), $attributes = array(), $core = null){
        $classes = $this->_mergeDefaultClass('text-muted', $classes);
        return $this->renderParagraph($content, $classes, $attributes, $core);
    }
    /**
     * @name            renderOrderedList()
     *                  Renders <ol> </ol>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           array               $items              List items
     * @param           array               $classes            List of class names
     * @param           bool                $styled             if false a class named list-unstyled added.
     * @param           mixed               $core
     *
     * @return          string
     */
    public function renderOrderedList($items, $classes = array(), $styled = true, $core = null){
        if(is_null($core)){
            $core['theme'] = 'bibercrm';
        }
        if(!is_array($classes)){
            $classes = array();
        }
        if(!$styled){
            $classes[] = 'list-unstyled';
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'classes'       => $classes,
            'listItems'     => $items,
            'ordered'       => true,
        );
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:' . $core['theme'] . '/HtmlParts:list.html.smarty', $vars);
    }
    /**
     * @name            renderParagraph()
     *                  Renders <p>Some text</p>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           string          $content
     * @param           array           $classes        List of class names.
     * @param           array           $attributes     key => value pairs of attributes where key is attribute name
     *                                                  and value is attribute value.
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderParagraph($content, $classes = array(), $attributes = array(), $core = null){
        if(is_null($core)){
            $core['theme'] = 'bibercrm';
        }
        /** If $classes and $attributes are not array reset them to an empty array */
        if(!is_array($classes)){
            $classes = array();
        }
        if(!is_array($attributes)){
            $attributes = array();
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'attributes'    => $attributes,
            'classes'       => $classes,
            'content'       => $content,
        );
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:' . $core['theme'] . '/HtmlParts:paragraph.html.smarty', $vars);
    }
    /**
     * @name            renderParagraphAlignedToCenter()
     *                  Renders <p class="text-center">Some text</p>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderParagraph()
     * @use             $this->_mergeDefaultClass()
     *
     * @param           string          $content
     * @param           array           $classes        List of class names.
     * @param           array           $attributes     key => value pairs of attributes where key is attribute name
     *                                                  and value is attribute value.
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderParagraphAlignedToCenter($content, $classes = array(), $attributes = array(), $core = null){
        $classes = $this->_mergeDefaultClass('text-center', $classes);
        return $this->renderParagraph($content, $classes, $attributes, $core);
    }
    /**
     * @name            renderParagraphAlignedToLeft()
     *                  Renders <p class="text-left">Some text</p>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderParagraph()
     * @use             $this->_mergeDefaultClass()
     *
     * @param           string          $content
     * @param           array           $classes        List of class names.
     * @param           array           $attributes     key => value pairs of attributes where key is attribute name
     *                                                  and value is attribute value.
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderParagraphAlignedToLeft($content, $classes = array(), $attributes = array(), $core = null){
        $classes = $this->_mergeDefaultClass('text-left', $classes);
        return $this->renderParagraph($content, $classes, $attributes, $core);
    }
    /**
     * @name            renderParagraphAlignedToRight()
     *                  Renders <p class="text-right">Some text</p>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderParagraph()
     * @use             $this->_mergeDefaultClass()
     *
     * @param           string          $content
     * @param           array           $classes        List of class names.
     * @param           array           $attributes     key => value pairs of attributes where key is attribute name
     *                                                  and value is attribute value.
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderParagraphAlignedToRight($content, $classes = array(), $attributes = array(), $core = null){
        $classes = $this->_mergeDefaultClass('text-right', $classes);
        return $this->renderParagraph($content, $classes, $attributes, $core);
    }
    /**
     * @name            renderPreBlock()
     *                  Renders <pre> </pre>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           string              $content            Any string
     * @param           arrray              $classes
     * @param           mixed               $core
     *
     * @return          string
     */
    public function renderPreBlock($content, $classes = array(), $core = null){
        if(is_null($core)){
            $core['theme'] = 'bibercrm';
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'classes'       => $classes,
            'content'       => $content,
        );
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:' . $core['theme'] . '/HtmlParts:pre.html.smarty', $vars);
    }
    /**
     * @name            renderPrimaryParagraph()
     *                  Renders <p class="text-primary">Some text</p>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderParagraph()
     * @use             $this->_mergeDefaultClass()
     *
     * @param           string          $content
     * @param           array           $classes        List of class names.
     * @param           array           $attributes     key => value pairs of attributes where key is attribute name
     *                                                  and value is attribute value.
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderPrimaryParagraph($content, $classes = array(), $attributes = array(), $core = null){
        $classes = $this->_mergeDefaultClass('text-primary', $classes);
        return $this->renderParagraph($content, $classes, $attributes, $core);
    }
    /**
     * @name            renderSmallText()
     *                  Renders <small>Some text</small>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           string          $content
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderSmallText($content, $core = null){
        if(is_null($core)){
            $core['theme'] = 'bibercrm';
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'content'       => $content,
        );
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:' . $core['theme'] . '/HtmlParts:small.html.smarty', $vars);
    }
    /**
     * @name            renderStrongText()
     *                  Renders <strong>Some text</strong>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           string          $content
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderStrongText($content, $core = null){
        if(is_null($core)){
            $core['theme'] = 'bibercrm';
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'content'       => $content,
        );
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:' . $core['theme'] . '/HtmlParts:strong.html.smarty', $vars);
    }
    /**
     * @name            renderSuccessParagraph()
     *                  Renders <p class="text-success">Some text</p>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderParagraph()
     * @use             $this->_mergeDefaultClass()
     *
     * @param           string          $content
     * @param           array           $classes        List of class names.
     * @param           array           $attributes     key => value pairs of attributes where key is attribute name
     *                                                  and value is attribute value.
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderSuccessParagraph($content, $classes = array(), $attributes = array(), $core = null){
        $classes = $this->_mergeDefaultClass('text-success', $classes);
        return $this->renderParagraph($content, $classes, $attributes, $core);
    }
    /**
     * @name            renderUnorderedList()
     *                  Renders <ul> </ul>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @param           array               $items              List items
     * @param           array               $classes            List of class names
     * @param           bool                $styled             if false a class named list-unstyled added.
     * @param           mixed               $core
     *
     * @return          string
     */
    public function renderUnorderedList($items, $classes = array(), $styled = true, $core = null){
        if(is_null($core)){
            $core['theme'] = 'bibercrm';
        }
        if(!is_array($classes)){
            $classes = array();
        }
        if(!$styled){
            $classes[] = 'list-unstyled';
        }
        /**
         * Prepare $vars
         */
        $vars = array(
            'classes'       => $classes,
            'listItems'     => $items,
            'ordered'       => false,
        );
        return $this->templating->render('BiberLtdCoreBundlesCoreBundle:' . $core['theme'] . '/HtmlParts:list.html.smarty', $vars);
    }
    /**
     * @name            renderWarningParagraph()
     *                  Renders <p class="text-warning">Some text</p>
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @use             $this->renderParagraph()
     * @use             $this->_mergeDefaultClass()
     *
     * @param           string          $content
     * @param           array           $classes        List of class names.
     * @param           array           $attributes     key => value pairs of attributes where key is attribute name
     *                                                  and value is attribute value.
     * @param           array           $core           Holds CoreController related values.
     *
     * @return          string
     */
    public function renderWarningParagraph($content, $classes = array(), $attributes = array(), $core = null){
        $classes = $this->_mergeDefaultClass('text-warning', $classes);
        return $this->renderParagraph($content, $classes, $attributes, $core);
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
 * 23.04.2014
 * **************************************
 * A renderAddress()
 * A renderBlockQuote()
 * A renderBlockquoteAlignedToRight()
 * A renderCodeBlock()
 * A renderDangerParagraph()
 * A renderDescriptionList()
 * A renderHeading()
 * A renderHorizontalDescriptionList()
 * A renderH1()
 * A renderH2()
 * A renderH3()
 * A renderH4()
 * A renderH5()
 * A renderH6()
 * A renderInfoParagraph()
 * A renderItalicText()
 * A renderLeadParagraph()
 * A renderMutedParagraph()
 * A renderOrderedList()
 * A renderParagraph()
 * A renderParagraphAlignedToCenter()
 * A renderParagraphAlignedToLeft()
 * A renderParagraphAlignedToRight()
 * A renderPreBlock()
 * A renderPrimaryParagraph()
 * A renderSmallText()
 * A renderStrongText()
 * A renderSuccessParagraph()
 * A renderUnorderedList()
 * A renderWarningParagraph()
 */