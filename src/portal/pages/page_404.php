<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\portal\pages;

use pxn\phpUtils\portal\Website;
use pxn\phpUtils\Defines;


class page_404 {



	public function getPageContents() {
		$FailedPage = Website::get()->getArg(Defines::KEY_FAILED_PAGE);
		return
"<center>
	<h1>404 - Page Not Found!</h1>
	<h3>{$FailedPage}</h3>
</center>
";
	}



}
