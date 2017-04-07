<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2017
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;

use pxn\phpUtils\Defines;


class ConfigDAO {

	const KEY_DAO_VALUE        = 'value';
	const KEY_DAO_DEFAULT      = 'default';
	const KEY_DAO_SUPER        = 'super';

	protected $group = NULL;
	protected $key   = NULL;

	protected $value = NULL;
	protected $def   = NULL;
	protected $sup   = NULL;

	protected $validHandler = NULL;
	protected $validValue   = NULL;



	public function __construct($group, $key,
			$value=NULL, $def=NULL, $sup=NULL, $handler=NULL) {
		$this->group = $group;
		$this->key   = $key;
		$this->value = $value;
		$this->def   = $def;
		$this->sup   = $sup;
		$this->validHandler = $handler;
	}



	public function getValue() {
		// return cached validated value
		if (!empty($this->validValue)) {
			return $this->validValue;
		}
		// super value
		if (!empty($this->sup)) {
			$value = self::runValidHandler(
				$this->validHandler,
				$this->sup,
				self::KEY_DAO_SUPER
			);
			if (!empty($value)) {
				$this->validValue = $value;
				return $value;
			}
		}
		// stored value
		if (!empty($this->value)) {
			$value = self::runValidHandler(
				$this->validHandler,
				$this->value,
				self::KEY_DAO_VALUE
			);
			if (!empty($value)) {
				$this->validValue = $value;
				return $value;
			}
		}
		// default value
		if (!empty($this->def)) {
			$value = self::runValidHandler(
				$this->validHandler,
				$this->def,
				self::KEY_DAO_DEFAULT
			);
			if (!empty($value)) {
				$this->validValue = $value;
				return $value;
			}
		}
		// no value set
		return NULL;
	}
	// set store value
	public function setValue($value) {
		$this->value      = $value;
		$this->validValue = NULL;
	}
	// set default value
	public function setDefault($value) {
		$this->def        = $value;
		$this->validValue = NULL;
	}
	// set super value
	public function setSuper($value) {
		$this->sup        = $value;
		$this->validValue = NULL;
	}



	// validate handler
	public function getValidHandler() {
		return $this->validHandler;
	}
	public function setValidHandler($handler) {
		$this->validValue   = NULL;
		if ($this->validHandler == NULL) {
			$this->validHandler = $handler;
		} else
		if (\is_array($this->validHandler)) {
			$this->validHandler[] = $handler;
		} else {
			$this->validHandler = [
				$this->validHandler,
				$handler
			];
		}
	}
	// return value validated by handler
	public static function runValidHandler($handler, $value, $type) {
		if (\is_array($handler)) {
			foreach ($handler as $h) {
				$value = runValidHandler(
					$h,
					$value,
					$type
				);
			}
			return $value;
		}
		// no handler set
		if ($handler == NULL) {
			return $value;
		}
		// validate by type
		$handlerType = \gettype($handler);
		if ($handlerType == 'string') {
			return General::castType(
				$value,
				\mb_strtolower( (string)$handler )
			);
		}
		// run closure function
		if ($handlerType == 'object') {
			return $handler(
				$value,
				$type
			);
		}
		// unknown handler type
		fail("Unknown config validate handler: $handler",
			Defines::EXIT_CODE_INTERNAL_ERROR);
	}



}
