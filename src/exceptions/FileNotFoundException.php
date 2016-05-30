<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\exceptions;


class FileNotFoundException extends \Exception {



	public function __construct($msg) {
		parent::__construct('File not found: '.$msg);
	}



}
