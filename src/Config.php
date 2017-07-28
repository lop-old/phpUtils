<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2017
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


class Config {

	protected static $instances = [];

	protected $name = NULL;
	protected $file = NULL;

	protected $daoArray = [];



	public static function get($name) {
		if (isset(self::$instances[$name])) {
			return self::$instances[$name];
		}
		$config = new self($name);
		self::$instances[$name] = $config;
		return $config;
	}
	public static function peak($name) {
		if (isset(self::$instances[$name])) {
			return self::$instances[$name];
		}
		return NULL;
	}



	protected function __construct($name) {
		$this->name = $name;
	}



	public function LoadFile($file) {
		if (empty($file)) {
			fail("File argument is required!",
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		if (!\file_exists($file)) {
			fail("File not found: $file",
				Defines::EXIT_CODE_IO_ERROR);
		}
		$this->file = $file;
		$raw = \file_get_contents($file);
		if ($raw === FALSE) {
			fail("Failed to load file: $file",
				Defines::EXIT_CODE_IO_ERROR);
		}
		$result = $this->LoadString($raw);
		unset ($raw);
		if (!$result) {
			fail("Failed to decode json file: $file",
				Defines::EXIT_CODE_IO_ERROR);
		}
	}
	public function LoadString($data) {
		if (empty($data)) {
			return FALSE;
		}
		$json = \json_decode($data);
		if ($json == NULL) {
			fail("Failed to decode json data!",
				Defines::EXIT_CODE_IO_ERROR);
		}
		foreach ($json as $key => $val) {
			if (empty($key) || empty($val)) {
				continue;
			}
			$dao = new ConfigDAO(
				$this->name,
				$key,
				$val
			);
			$this->daoArray[$key] = $dao;
		}
		return TRUE;
	}



	public function getDAO($key) {
		$dao = $this->peakDAO($key);
		if ($dao == NULL) {
			$dao = new ConfigDAO(
				$this->name,
				$key
			);
			$this->daoArray[$key] = $dao;
		}
		return $dao;
	}
	public function peakDAO($key) {
		$key = self::ValidateKey($key);
		if (isset($this->daoArray[$key])) {
			return $this->daoArray[$key];
		}
		return NULL;
	}



	public function getValue($key) {
		$dao = $this->peakDAO($key);
		if ($dao == NULL) {
			return NULL;
		}
		return $dao->getValue();
	}
	public function getString($key) {
		$value = $this->getValue($key);
		if ($value === NULL) {
			return NULL;
		}
		return (string) $value;
	}
	public function getInt($key) {
		$value = $this->getValue($key);
		if ($value === NULL) {
			return NULL;
		}
		return (integer) $value;
	}
	public function getLong($key) {
		$value = $this->getValue($key);
		if ($value === NULL) {
			return NULL;
		}
		return (integer) $value;
	}
	public function getFloat($key) {
		$value = $this->getValue($key);
		if ($value === NULL) {
			return NULL;
		}
		return (float) $value;
	}
	public function getDouble($key) {
		$value = $this->getValue($key);
		if ($value === NULL) {
			return NULL;
		}
		return (double) $value;
	}
	public function getBool($key) {
		$value = $this->getValue($key);
		if ($value === NULL) {
			return NULL;
		}
		return ($value != FALSE);
	}



	public function peakValue($key) {
		$dao = $this->peakDAO($key);
		if ($dao == NULL) {
			return NULL;
		}
		return $dao->peakValue();
	}
	public function peakSuper($key) {
		$dao = $this->peakDAO($key);
		if ($dao == NULL) {
			return NULL;
		}
		return $dao->peakSuper();
	}



	public function setValue($key, $value) {
		$dao = $this->getDAO($key);
		$dao->setValue($value);
	}
	public function setRef($key, &$value) {
		$dao = $this->getDAO($key);
		$dao->setRef($value);
	}
	public function setDefault($key, $value) {
		$dao = $this->getDAO($key);
		$dao->setDefault($value);
	}
	public function setSuper($key, $value) {
		$dao = $this->getDAO($key);
		$dao->setSuper($value);
	}



	public function setValidHandler($key, $handler) {
		$dao = $this->getDAO($key);
		$dao->setValidHandler($handler);
	}



	protected static function ValidateKey($key) {
		if (empty($key)) {
			fail("Key argument is required!",
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		return \mb_strtolower($key);
	}



}
