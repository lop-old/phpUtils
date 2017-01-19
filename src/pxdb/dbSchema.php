<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\pxdb;


abstract class dbSchema {

	protected $fields = NULL;



	public function __construct() {
		$this->fields = $this->initFields();
	}
	public abstract function initFields();



	public function getFields() {
		return $this->fields;
	}



}
