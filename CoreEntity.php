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
 * @date        12.06.2015
 *
 */

namespace BiberLtd\Bundle\CoreBundle;

use Doctrine\ORM\Mapping AS ORM;

class CoreEntity {

    public $date_added;/** @var \DateTime Date when the entry is created. */
    public $date_updated;/** @var \DateTime Date when the entry is updated. */
    public $date_removed;/** @var \DateTime Date when the entry is removed. */
    protected $new = false;/** @var bool Marks the object as new. */
    protected $modified = false;/** @var bool Marks the object as modified or not modified */
    protected $localized = false;/** @var bool Marks the object as localizable. */
    protected $timezone = 'Europe/Istanbul';/** @var string  application timezone */
	protected $now;

    /**
     * @name            __construct()
     *                  Initializes entity.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.0.0
     */
    public function __construct($timezone = 'Europe/Istanbul') {
        $new = true;
        foreach ($this as $key => $value) {
            if ($this->date_added !== NULL ) {
                $new = false;
                break 1;
            }
        }

        $this->timezone = $timezone;
        $this->new = $new;
        $this->modified = false;
		$this->now = new \DateTime('now', new \DateTimeZone($this->timezone));
        if ($this->new) {
            $this->setDateAdded();
        }
    }

    /**
     * @name            isLocalized()
     *                  Checks if the object is localizable.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          bool
     */
    public function isLocalized() {
        return $this->localized;
    }

    /**
     * @name            isModified()
     *                  Checks if the object is modified.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          bool
     */
    public function isModified() {
        return $this->modified;
    }

    /**
     * @name            isNew()
     *                  Checks if this is a new entity.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          bool
     */
    public function isNew() {
        return $this->new;
    }

    /**
     * @name            setModified()
     *                  Sets the modified property to true or false.
     *
     * @author          Can Berkol
     * @since           1.0.0
     * @version         1.0.1
     *
     * @param           string          $property       Name of property to be checked.
     * @param           mixed           $value          Value to be set.
     *
     * @return          object          $this
     */
    public function setModified($property, $value) {
        $explodedProp = explode('_', $property);
        $ucFirstProp = '';
        foreach ($explodedProp as $prop) {
            $ucFirstProp .= ucfirst($prop);
        }
        $get = 'get' . $ucFirstProp;
        if ($this->$get() !== $value) {
            $this->modified = true;
            $this->setDateUpdated();
        }
        return $this;
    }

    /**
     * @name            getDateAdded()
     *  				Returns the creation date of the history entry.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.0.0
     *
     * @return          \DateTime          $this->date_added
     */
    public function getDateAdded() {
        return $this->date_added;
    }

    /**
     * @name            setDateAdded()
     *                  Sets the object creation date.
     *
     * @author          Can Berkol
     * @since           1.0.1
     * @version         1.0.2
     *
     * @return          object          $this
     */
    public function setDateAdded() {
		$this->now = new \DateTime('now', new \DateTimeZone($this->timezone));
        $this->date_added = $this->now;
        return $this;
    }

    /**
     * @name            setDateRemoved()
     *  				Sets the date when the entry is removed.
     *                  NOTE: Removal means setting Date Removed column to a date. Actual removing will not occur unless
     *                  specifically instructed within the code.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since			1.0.1
     * @version         1.0.1
     *
     * @param           \DateTime       $date_removed
     *
     * @return          object          $this
     */
    public function setDateRemoved($date_removed) {
        if(!$this->setModified('date_removed', $date_removed)->isModified()){
            return $this;
        }
        $this->date_removed = $date_removed;
        return $this;
    }

    /**
     * @name            getDateRemoved()
     *  				Returns the date when the entry is deleted.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.0.0
     *
     * @return          mixed             null | \DateTime
     */
    public function getDateRemoved() {
        return $this->date_removed;
    }

    /**
     * @name            getDateUpdated()
     *  				Gets the date when the entry is last updated.
     *
     * @author          Can Berkol
     *
     * @since			1.0.0
     * @version         1.0.0
     *
     * @return          \DateTime          $this->date_updated
     */
    public function getDateUpdated() {
        return $this->date_updated;
    }

    /**
     * @name            setDateUpdated()
     *                  Sets the object update date.
     *
     * @author          Can Berkol
     * @since           1.0.1
     * @version         1.0.2
     *
     * @return          object          $this
     */
    public function setDateUpdated() {
		$this->now = new \DateTime('now', new \DateTimeZone($this->timezone));
        $this->date_updated = $this->now;
        return $this;
    }

}

/**
 * Change Log
 * **************************************
 * v1.0.3                      Can Berkol
 * 12.06.2012
 * **************************************
 * BF :: setDateUpdated & setDateAdded was setting a null value. Fixed.
 *
 * **************************************
 * v1.0.2                      01.05.2015
 * Can Berkol
 * **************************************
 * CR :: $now property added to handle to obtain a unique timestamp.
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * 16.12.2013
 * **************************************
 * Methods are now camelCase.
 * A getDateAdded()
 * A getDateRemoved()
 * A getDateUpdated()
 * A setDateAdded()
 * A setDateRemoved()
 * A setDateUpdated()
 * U __construct()
 * U __setModified()
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 08.09.2013
 * **************************************
 * A __construct()
 * A is_localized()
 * A is_modified()
 * A is_new()
 * A setModified()
 *
 */