<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\portal\pages;


class page_404 {



	public function getPageContents() {
		return
'<center>
	<h1>404 - Page Not Found!</h1>
	<h3>{{FailedPage}}</h3>
</center>
';
	}



}
