<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\cache;

use pxn\phpUtils\Arrays;
use pxn\phpUtils\Strings;
use pxn\phpUtils\General;
use pxn\phpUtils\Defines;


class cacher_filesystem extends cacher {

	protected $cachePath = NULL;



	public function __construct($expireSeconds=NULL, $cachePath=NULL) {
		parent::__construct($expireSeconds);
		$this->cachePath = $cachePath;
	}



	public function hasEntry(...$context) {
		$context = self::BuildContext($context);
		$filePath = $this->_getFilePath($context);
		return \file_exists($filePath);
	}



	public function getEntry($source, ...$context) {
		return $this->_getEntry(
			$source,
			self::BuildContext($context)
		);
	}
	public function setEntry($value, ...$context) {
		return $this->_setEntry(
			$value,
			self::BuildContext($context)
		);
	}



	protected function _getEntry($source, $context) {
		// cache disabled
		if (\debug()) {
			return $source();
		}
		// existing entry
		$filePath = $this->_getFilePath($context);
		if (empty($filePath)) {
			return NULL;
		}
		if (\file_exists($filePath)) {
			// read cache file
			$data = \file_get_contents($filePath);
			$result = $this->ValidateData($data, $context);
			if ($result !== NULL) {
				// not expired
				if ($result['expired'] == FALSE
				&& isset($result['value'])
				&& !empty($result['value']) ) {
					return $result['value'];
				}
			}
		}
		// set new entry
		$value = $source();
		$this->_setEntry($value, $context);
		return $value;
	}
	protected function _setEntry($value, $context) {
		$timestamp = General::getTimestamp(0);
		$filePath = $this->_getFilePath($context);
		if (empty($filePath)) {
			return FALSE;
		}
		// write cache file
		$result = \file_put_contents(
			$filePath,
			"Timestamp: {$timestamp}\n\n{$value}",
			\LOCK_EX
		);
		if ($result === FALSE) {
			return FALSE;
		}
		return TRUE;
	}



	protected function ValidateData($data, $context) {
		$pos = \mb_strpos($data, "\n\n");
		if ($pos === FALSE) {
			return NULL;
		}
		list($meta, $data) = \explode("\n\n", $data, 2);
		$timestamp = NULL;
		foreach(\explode("\n", $meta) as $line) {
			$line = \trim($line);
			if (empty($line)) continue;
			list($key, $val) = \explode(':', $line, 2);
			$key = \mb_strtolower(\trim($key));
			switch ($key) {
			case 'timestamp':
				$timestamp = (int) $val;
				continue;
			// unknown key
			default:
				fail("Unknown tag in cache file: $context - $line",
					Defines::EXIT_CODE_UNAVAILABLE);
				break;
			}
		}
		// timestamp not set or invalid
		if ($timestamp == NULL || $timestamp <= 0) {
			return NULL;
		}
		// timestamp expired
		$timeNow = General::getTimestamp(0);
		$timeSince = $timeNow - $timestamp;
		if ($timeSince > $this->expireSeconds) {
			return [
				'expired' => TRUE
			];
		}
		// cache value good
		return [
			'expired' => FALSE,
			'value'   => $data
		];
	}



	protected function _getFilePath($context) {
		if (empty($this->cachePath)) {
			return NULL;
		}
		return Strings::BuildPath(
			$this->cachePath,
			".cache__{$context}"
		);
	}
	protected static function BuildContext(...$context) {
		if (empty($context)) {
			return NULL;
		}
		return Arrays::TrimFlatMerge(
			'__',
			$context
		);
	}



}
