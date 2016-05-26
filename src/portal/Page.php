<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\portal;


abstract class Page {

	protected $website = NULL;



	public function __construct() {
		$this->website = \pxn\phpUtils\portal\Website::get();
	}



	public abstract function getPageContents();

	public abstract function getTplFileName();



	public function getTpl() {
		return $this->website->getTpl(
			$this->getTplFileName()
		);
	}



}
