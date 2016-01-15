<?php
/**
 * @author		Can Berkol
 * @author		Said İmamoğlu
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        10.12.2015
 */
namespace BiberLtd\Bundle\CoreBundle;
use BiberLtd\Bundle\CoreBundle\Exceptions\LocalizationNotFoundException;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

class CoreLocalizableEntity extends CoreEntity{
    /**
     * @var ArrayCollection
     */
    protected $localizations;

    /**
     * CoreLocalizableEntity constructor.
     */
    public function __construct(){
        parent::__construct();
        $this->setLocalizations(new ArrayCollection());
        $this->localized = true;
    }

    /**
     * @param string     $lang
     * @param bool|false $internal
     *
     * @return bool
     * @throws \BiberLtd\Bundle\CoreBundle\Exceptions\LocalizationNotFoundException
     */
    public function getLocalization(string $lang, bool $internal = false){
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
            throw new LocalizationNotFoundException($this);
        }
    }

	/**
	 * @param $localizations
	 *
	 * @return $this
	 */
    public function setLocalizations($localizations){
        if(!$this->setModified('localizations', $localizations)->isModified()) {
            return $this;
        }
        $this->localizations = $localizations;
        return $this;
    }

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection|array
	 */
     public function getLocalizations(){
        return $this->localizations;
    }
}