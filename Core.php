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

use BiberLtd\Bundle\CoreBundle\CoreTraits\DebugTrait;

class Core{
    protected $error        = array();
    public $timezone        = 'Europe/Istanbul';
    protected $kernel;

	use DebugTrait;

    public function __construct($kernel){
        $this->kernel = $kernel;
    }


}