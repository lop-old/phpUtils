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
	const DEBUG_ENABLED           = Defines::KEY_CFG_DEBUG;
	const ANSI_COLOR_ENABLED      = Defines::KEY_CFG_ANSI_COLOR_ENABLED;
	const ALLOW_SHORT_FLAG_VALUES = Defines::KEY_CFG_ALLOW_SHORT_FLAG_VALUES;
	const DISPLAY_MODE            = Defines::KEY_CFG_DISPLAY_MODE;

	protected static $cfg = NULL;



	public static function init() {
		if (self::$cfg != NULL) {
			return FALSE;
		}
		self::$cfg = Config::get(self::CONFIG_GROUP);

		// debug mode
		self::$cfg->setDefault(     self::DEBUG_ENABLED, FALSE);
		self::$cfg->setValidHandler(self::DEBUG_ENABLED, 'bool');

		// ansi color enabled (in shell)
		self::$cfg->setValidHandler(self::ANSI_COLOR_ENABLED, 'bool');

		// allow short flag values (shell commands)
		self::$cfg->setDefault(     self::ALLOW_SHORT_FLAG_VALUES, FALSE);
		self::$cfg->setValidHandler(self::ALLOW_SHORT_FLAG_VALUES, 'bool');

		// display mode (shell or web)
		self::$cfg->setValidHandler(self::DISPLAY_MODE, 'string');

		return TRUE;
	}



	// debug mode
	public static function isDebug() {
		return self::$cfg->getBool(
			self::DEBUG_ENABLED
		);
	}
	public static function notDebug() {
		$value = self::isDebug();
		if ($value === NULL) {
			return NULL;
		}
		return ($value == FALSE);
	}
	public static function setDebug($enabled=TRUE, $msg=NULL) {
		$last = self::$cfg->getValue(self::DEBUG_ENABLED);
		self::$cfg->setValue(
			self::DEBUG_ENABLED,
			$enabled
		);
		$value = self::$cfg->getValue(self::DEBUG_ENABLED);
		if ($value !== $last) {
			$enabled = self::$cfg->getBool(self::DEBUG_ENABLED);
			$isShell = System::isShell();
			// debug mode enabled
			if ($enabled) {
				\error_reporting(\E_ALL | \E_STRICT);
				\ini_set('display_errors', 'On');
				\ini_set('html_errors',    'On');
				\ini_set('log_errors',     'Off');
				$msg = (empty($msg) ? '' : ": $msg");
				if ($isShell) {
					echo "Debug mode enabled{$msg}\n";
				}
			// debug mode disabled
			} else {
				if ($last == NULL && $msg != 'default') {
					$msg = (empty($msg) ? '' : ": $msg");
					if ($isShell) {
						echo "Debug mode disabled{$msg}\n";
					}
				}
				\error_reporting(\E_ERROR | \E_WARNING | \E_PARSE | \E_NOTICE);
				\ini_set('display_errors', 'Off');
				\ini_set('log_errors',     'On');
			}
		}
	}
	public static function setDebugRef(&$value) {
		self::$cfg->setRef(
			self::DEBUG_ENABLED,
			$value
		);
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
