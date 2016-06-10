<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\cache;


abstract class cacher {

	protected $expireSeconds = 120;



	public function __construct($expireSeconds=NULL) {
		if ($expireSeconds != NULL)
			$expireSeconds = (int) $expireSeconds;
			if ($expireSeconds > 0) {
				$this->expireSeconds = $expireSeconds;
			}
		}
	}



	public abstract function hasEntry(...$context);
	public abstract function getEntry($source, ...$context);
	public abstract function setEntry($value,  ...$context);



}
