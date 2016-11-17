<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\app\render;

use pxn\phpUtils\System;


abstract class ShellRender extends Render {



	public function doRender() {
		if (!System::isShell()) {
			$name = $this->getName();
			fail("Cannot use a ShellRender class in this mode! {$name}"); ExitNow(1);
		}
	}



}
