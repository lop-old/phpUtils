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
	}
	public static function ValidateShell() {
		if (!System::isShell()) {
			$name = $this->getName();
			fail("This ShellApp class can only run as shell! {$name}"); ExitNow(1);
		}
	}



	protected function getWeight() {
		return System::isShell()
			? 1000
			: -1;
	}



	protected function doRender() {
		self::ValidateShell();
		ShellTools::init();
		return TRUE;
	}



}
