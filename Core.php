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
use Symfony\Component\HttpFoundation\RequestStack;

class Core{
    protected $error        = [];
    public $timezone        = 'Europe/Istanbul';
    protected $kernel;
    protected $requestStack;

	use DebugTrait;

    /**
     * Core constructor.
     *
     * @param                                                     $kernel
     * @param \Symfony\Component\HttpFoundation\RequestStack|null $requestStack
     */
    public function __construct($kernel, RequestStack $requestStack = null){
        $this->kernel = $kernel;
        $this->requestStack = $requestStack;
    }
}