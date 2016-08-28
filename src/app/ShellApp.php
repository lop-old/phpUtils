<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\app;

use pxn\phpUtils\ShellTools;
use pxn\phpUtils\System;


abstract class ShellApp extends App {



	public function __construct() {
		parent::__construct();
		ShellTools::init();
//		// default render types
//		$this->registerRender( new \pxn\phpUtils\app\render\RenderMain($this) );
	}
//	protected function initArgs() {
//	}



	protected function getWeight() {
		return System::isShell()
			? 1000
			: 0;
	}



}
