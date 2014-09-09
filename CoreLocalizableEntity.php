<?php
/**
 * Core Entity Class
 *
 * This class provides an abstract foundation to all Biber Ltd. Entity files.
 *
 * @vendor      BiberLtd
 * @package		Core
 * @subpackage
 * @name	    Core
 *
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.3
 * @date        29.01.2014
 *
 */
namespace BiberLtd\Bundle\CoreBundle;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

class CoreLocalizableEntity extends CoreEntity{
    protected $localizations;
    /**
     * @name            __construct()
     *                  Initializes entity.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.0.0
     *
     */
    public function __construct(){
        parent::__construct();
        $this->setLocalizations(new ArrayCollection());
        $this->localized = true;
    }
    /**
     * @name            get_localization()
     *  				Gets a specific language's $localization values.
     *
     * @author          Can Berkol
     * @since			1.0.0
     * @version         1.0.1
     *
     * @param           string          $lang           Language code of localization
     * @param           bool            $internal       for model use only, set true if you don't want to get the default object bu a false if the localization is not found
     *
     * @return          object                          Corresponding entity.
     */
    public function getLocalization($lang, $internal = false){
        $localizations = $this->getLocalizations();
        $found = false;
        foreach($localizations as $localization){
            if ($localization->getLanguage()->getId() == 1) {
                $default = $localization;
            }
            if($localization->getLanguage()->getIsoCode() == $lang){
                $found = true;
                return $localization;
            }
        }
        if (!$found) {
            if($internal){
                return false;
            }
            if(isset($default)){
                return $default;
            }
            return null;
        }
    }
    /**
     * @name            setLocalizations()
     *  				Sets localizations property.
     *
     * @author          Can Berkol
     * @since			1.0.0
     * @version         1.0.0
     *
     * @param           array          localizations
     *
     * @return          object          $this
     */
    public function setLocalizations($localizations){
        if(!$this->setModified('localizations', $localizations)->isModified()) {
            return $this;
        }
        $this->localizations = $localizations;
        return $this;
    }

    /**
     * @name            getLocalizations()
     *  				Gets localizations property.
     * .
     * @author          Can Berkol
     * @since			1.0.0
     * @version         1.0.0
     *
     * @return          array          $this->localizations
     */
    public function getLocalizations(){
        return $this->localizations;
    }
}
/**
 * **************************************
 * Change Log
 * **************************************
 * v1.0.3                      Can Berkol
 * 29.01.2014
 * **************************************
 * U getLocalization()
 *
 * **************************************
 * v1.0.2                      Can Berkol
 * 18.12.2013
 * **************************************
 * A getLocalizations()
 * A setLocalizations()
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * 22.11.2013
 * **************************************
 * U getLocalization() now supports Doctrine\ORM\Persistent\Collection
 *
 * **************************************
 * Change Log
 * **************************************
 * v1.0.0                      Can Berkol
 * 08.09.2013
 * **************************************
 * A __construct()
 * A getLocalization()
 *
 */