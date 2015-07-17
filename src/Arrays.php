<?php
/*
 * PoiXson phpUtils - Utilities for PoiXson PHP projects
 *
 * @copyright 2004-2015
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;

final class Arrays {
	private final function __construct() {}



	public static function TrimFlat(&$data) {
		if($data === NULL) return;
		$data = self::Flatten($data);
		self::Trim($data);
	}



	public static function Flatten($data) {
		if($data === NULL) return NULL;
		if(!\is_array($data))
			return [ (string) $data ];
		$result = [];
		\array_walk_recursive(
				$data, // @codeCoverageIgnore
				function($arr) use (&$result) {
					$result[] = $arr;
				}
		);
		return $result;
	}



	public static function Trim(&$data) {
		if($data === NULL) return;
		if(!\is_array($data)) {
			$data = [ $data ];
			return;
		}
		$temp = $data;
		foreach($temp as $k => $v) {
			if($v === NULL || $v === '')
				unset($data[$k]);
		}
	}



}
