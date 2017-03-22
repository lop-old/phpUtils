<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


final class Arrays {
	private final function __construct() {}



	public static function TrimFlatMerge($glue, ...$data) {
		$glue = (string) $glue;
		self::TrimFlat($data);
		return \implode(
			$glue,
			$data
		);
	}



	public static function Flatten(...$data) {
		$result = [];
		\array_walk_recursive(
			$data, // @codeCoverageIgnore
			function($arr) use (&$result) {
				$result[] = $arr;
			}
		);
		return $result;
	}



	public static function TrimFlat(&$data) {
		if ($data === NULL)
			return;
		$data = self::Flatten($data);
		self::Trim($data);
	}



	public static function Trim(&$data) {
		if ($data === NULL) {
			return;
		}
		$temp = self::MakeContain($data);
		foreach ($temp as $k => $v) {
			if ($v === NULL || $v === '') {
				unset($data[$k]);
			}
		}
	}



	// make array if not already
	public static function MakeContain($data) {
		if ($data === NULL) {
			return NULL;
		}
		if (\is_array($data)) {
			return $data;
		}
		$str = (string) $data;
		if (empty($str)) {
			return [];
		}
		return [ $str ];
	}
	// explode() with multiple delimiters
	public static function toArray($data, ...$delims) {
		if (\is_array($data)) {
			return $data;
		}
		if (count($delims) == 0) {
			$delims = [ ' ', ',', ';', "\t", "\r", "\n" ];
		}
		$data = (string) $data;
		$first_delim = NULL;
		foreach ($delims as $v) {
			if (empty($v)) continue;
			$first_delim = $v;
			break;
		}
		if (empty($first_delim)) {
			throw \NullPointerException('Delim argument is required!');
		}
		foreach ($delims as $v) {
			if (empty($v)) continue;
			if ($v == $first_delim) continue;
			$data = \str_replace($v, $first_delim, $data);
		}
		return \explode($first_delim, $data);
	}



}
