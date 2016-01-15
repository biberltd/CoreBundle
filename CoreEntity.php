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

use Doctrine\ORM\Mapping AS ORM;

class CoreEntity {
    /**
     * @var \DateTime
     */
    public $date_added;
    /**
     * @var \DateTime
     */
    public $date_updated;
    /**
     * @var \DateTime
     */
    public $date_removed;
    /**
     * @var bool
     */
    protected $new = false; //  Marks the object as new.
    /**
     * @var bool
     */
    protected $modified = false; //  Marks the object as modified or not modified.
    /**
     * @var bool
     */
    protected $localized = false; // bool Marks the object as localizable.
    /**
     * @var string
     */
    protected $timezone = 'Europe/Istanbul';
    /**
     * @var \DateTime
     */
	protected $now;

    /**
     * CoreEntity constructor.
     *
     * @param string|null $timezone
     */
    public function __construct(string $timezone = null) {
        $timezone = $timezone ?? 'Europe/Istanbul';
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
     * @return bool
     */
    public function isLocalized() {
        return $this->localized;
    }

    /**
     * @return bool
     */
    public function isModified() {
        return $this->modified;
    }

    /**
     * @return bool
     */
    public function isNew() {
        return $this->new;
    }

    /**
     * @param string $property
     * @param mixed $value
     *
     * @return $this
     */
    public function setModified(string $property, $value) {
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
     * @return \DateTime
     */
    public function getDateAdded() {
        return $this->date_added;
    }

    /**
     * @return $this
     */
    public function setDateAdded() {
		$this->now = new \DateTime('now', new \DateTimeZone($this->timezone));
        $this->date_added = $this->now;
        return $this;
    }

    /**
     * @param \DateTime $date_removed
     *
     * @return $this
     */
    public function setDateRemoved(\DateTime $date_removed) {
        if(!$this->setModified('date_removed', $date_removed)->isModified()){
            return $this;
        }
        $this->date_removed = $date_removed;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateRemoved() {
        return $this->date_removed;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated() {
        return $this->date_updated;
    }

    /**
     * @return $this
     */
    public function setDateUpdated() {
		$this->now = new \DateTime('now', new \DateTimeZone($this->timezone));
        $this->date_updated = $this->now;
        return $this;
    }

}