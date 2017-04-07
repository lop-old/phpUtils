<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2017
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


class ConfigGeneral {
	private function __construct() {}

	const CONFIG_GROUP = Defines::KEY_CONFIG_GROUP_GENERAL;
	const ANSI_COLOR_ENABLED      = Defines::KEY_CFG_ANSI_COLOR_ENABLED;
	const ALLOW_SHORT_FLAG_VALUES = Defines::KEY_CFG_ALLOW_SHORT_FLAG_VALUES;
	const DISPLAY_MODE            = DefinesPortal::KEY_CFG_DISPLAY_MODE;

	protected static $cfg = NULL;



	public static function init() {
		if (self::$cfg != NULL) {
			return FALSE;
		}
		self::$cfg = Config::get(self::CONFIG_GROUP);

		// ansi color enabled (in shell)
		self::$cfg->setValidHandler(self::ANSI_COLOR_ENABLED, 'bool');

		// allow short flag values (shell commands)
		self::$cfg->setDefault(     self::ALLOW_SHORT_FLAG_VALUES, FALSE);
		self::$cfg->setValidHandler(self::ALLOW_SHORT_FLAG_VALUES, 'bool');

		// display mode (shell or web)
		self::$cfg->setValidHandler(self::DISPLAY_MODE, 'string');

		return TRUE;
	}



	// ansi color enabled (in shell)
	public static function isAnsiColorEnabled() {
		return self::$cfg->getBool(
			self::ANSI_COLOR_ENABLED
		);
	}
	public static function setAnsiColorEnabled($enabled) {
		self::$cfg->setValue(
			self::ANSI_COLOR_ENABLED,
			$enabled
		);
	}
	public static function defaultAnsiColorEnabled($enabled) {
		self::$cfg->setDefault(
			self::ANSI_COLOR_ENABLED,
			$enabled
		);
	}



	// allow short flag values (shell commands)
	public static function getAllowShortFlagValues() {
		return self::$cfg->getBool(
			self::ALLOW_SHORT_FLAG_VALUES
		);
	}
	public static function setAllowShortFlagValues($enabled) {
		self::$cfg->setValue(
			self::ALLOW_SHORT_FLAG_VALUES,
			$enabled
		);
	}



	// display mode (shell or web)
	public static function getDisplayMode() {
		return self::$cfg->getString(
			self::DISPLAY_MODE
		);
	}
	public static function setDisplayMode($mode) {
		self::$cfg->setValue(
			self::DISPLAY_MODE,
			$mode
		);
	}



}
