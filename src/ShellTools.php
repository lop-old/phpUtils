<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


final class ShellTools {
	private final function __construct() {}

	const ALLOW_SHORT_FLAG_VALUES = FALSE;

	private static $inited = FALSE;

	public static $ANSI_COLOR_ENABLED = NULL;

	private static $flags = NULL;
	private static $args  = NULL;



	public static function init() {
		if (self::$inited)
			return;
		self::initConsoleVars();
		self::$inited = TRUE;
		// ansi color enabled
		if (self::hasFlag('--ansi')) {
			Strings::setAnsiColorEnabled(TRUE);
		}
		// ansi color disabled
		if (self::hasFlag('--no-ansi')) {
			Strings::setAnsiColorEnabled(FALSE);
		}
	}
	private static function initConsoleVars() {
		if (self::$inited)
			return;
		if (!System::isShell()) {
			return FALSE;
		}
		if (self::$flags !== NULL || self::$args !== NULL) {
			return FALSE;
		}
		global $argv;
		self::$flags = [];
		self::$args  = [];
		$allArgs = FALSE;
		for ($index=1; $index<count($argv); $index++) {
			$arg = $argv[$index];
			if (empty($arg)) continue;
			// --
			if ($allArgs) {
				self::$args[] = $arg;
				continue;
			}
			if ($arg == '--') {
				$allArgs = TRUE;
				continue;
			}
			// --flag
			if (Strings::StartsWith($arg, '--')) {
				// --flag=value
				$pos = \mb_strpos($arg, '=');
				if ($pos !== FALSE) {
					$val = \mb_substr($arg, $pos);
					$arg = \mb_substr($arg, 0, $pos);
					self::$flags[$arg] = $val;
					//$_GET[$arg] = $val;
					continue;
				}
				// --flag value
				if (isset($argv[$index+1])) {
					$val = $argv[$index+1];
					if (!Strings::StartsWith($val, '-')) {
						$index++;
						self::$flags[$arg] = $val;
						//$_GET[$arg] = $val;
						continue;
					}
				}
				// --flag
				if (!isset(self::$flags[$arg])) {
					self::$flags[$arg] = TRUE;
					//$_GET[$arg] = TRUE;
				}
				continue;
			}
			// -flag
			if (Strings::StartsWith($arg, '-')) {
				// attached value
				$len = \mb_strlen($arg);
				if ($len > 2) {
					$val = \mb_substr($arg, 2);;
					$arg = \mb_substr($arg, 0, 2);
					self::$flags[$arg] = $val;
					//$_GET[$arg] = $val;
					continue;
				}
				// -f value
				if (self::ALLOW_SHORT_FLAG_VALUES) {
					if (isset($argv[$index+1])) {
						$val = $argv[$index+1];
						if (!Strings::StartsWith($val, '-')) {
							$index++;
							self::$flags[$arg] = $val;
							//$_GET[$arg] = $val;
							continue;
						}
					}
				}
				// -f
				if (!isset(self::$flags[$arg])) {
					self::$flags[$arg] = TRUE;
					//$_GET[$arg] = TRUE;
				}
				continue;
			}
			// not flag, must be argument
			self::$args[] = $arg;
		}
		return TRUE;
	}



	// get all as array
	public static function getFlags() {
		return self::$flags;
	}
	public static function getArgs() {
		return self::$args;
	}



	// get one
	public static function getFlag(... $keys) {
		if (\count($keys) == 0) {
			return NULL;
		}
		foreach ($keys as $key) {
			$val = self::getFlag_Single($key);
			if ($val != NULL) {
				return $val;
			}
		}
		return NULL;
	}
	private static function getFlag_Single($key) {
		if (empty($key)) {
			return NULL;
		}
		if (isset(self::$flags[$key])) {
			// don't allow -x value
			if (self::ALLOW_SHORT_FLAG_VALUES != TRUE) {
				if (!Strings::StartsWith($key, '--')) {
					return TRUE;
				}
			}
			return self::$flags[$key];
		}
		return NULL;
	}



	// flag exists
	public static function hasFlag(... $keys) {
		if (\count($keys) == 0) {
			return NULL;
		}
		foreach ($keys as $key) {
			$result = self::hasFlag_Single($key);
			if ($result === TRUE) {
				return TRUE;
			}
		}
		return FALSE;
	}
	private static function hasFlag_Single($key) {
		if (empty($key)) {
			return NULL;
		}
		return isset(self::$flags[$key]);
	}



	// has -h or --help flag
	public static function isHelp() {
		return self::hasFlag('-h') ||
			self::hasFlag('--help');
	}



	############
	## Format ##
	############



	public static function isAnsiColorEnabled() {
		if (self::$ANSI_COLOR_ENABLED === NULL) {
			return TRUE;
		}
		return (self::$ANSI_COLOR_ENABLED != FALSE);
	}
	public static function setAnsiColorEnabled($enabled) {
		self::$ANSI_COLOR_ENABLED = $enabled;
	}



	public static function FormatString($str) {
		return \preg_replace_callback(
			'#\{[a-z0-9,=]+\}#i',
			[ __CLASS__, 'FormatString_Callback' ],
			$str
		);
	}
	public static function FormatString_Callback(array $matches) {
		$match = \reset($matches);
		if (!self::isAnsiColorEnabled()) {
			return $match;
		}
		if (!Strings::StartsWith($match, '{') || !Strings::EndsWith($match, '}')) {
			return $match;
		}
		$match = \mb_substr($match, 1, -1);
		$codes = [];
		$bold = NULL;
		$parts = \explode(
			',',
			\mb_strtolower($match)
		);
		foreach ($parts as $part) {
			if (empty($part)) continue;
			$pos = \mb_strpos($part, '=');
			// {tag}
			if ($pos === FALSE) {
				switch ($part) {
				case 'bold':
					$bold = TRUE;
					break;
				case 'reset':
					return "\033[0m";
				}
			// {tag=value}
			} else {
				$key = \mb_substr($part, 0, $pos);
				$val = \mb_substr($part, $pos+1);
				if ($key == 'color') {
					switch ($val) {
					// dark colors
					case 'black':
						$codes[] = 30;
						$bold = ($bold === NULL ? FALSE : $bold);
						break;
					case 'red':
						$codes[] = 31;
						$bold = ($bold === NULL ? FALSE : $bold);
						break;
					case 'green':
						$codes[] = 32;
						$bold = ($bold === NULL ? FALSE : $bold);
						break;
					case 'orange':
						$codes[] = 33;
						$bold = ($bold === NULL ? FALSE : $bold);
						break;
					case 'blue':
						$codes[] = 34;
						$bold = ($bold === NULL ? FALSE : $bold);
						break;
					case 'magenta':
						$codes[] = 35;
						$bold = ($bold === NULL ? FALSE : $bold);
						break;
					case 'cyan':
						$codes[] = 36;
						$bold = ($bold === NULL ? FALSE : $bold);
						break;
					case 'lightgray':
						$codes[] = 37;
						$bold = ($bold === NULL ? FALSE : $bold);
						break;
					// light colors
					case 'gray':
						$codes[] = 30;
						$bold = ($bold === NULL ? TRUE : $bold);
						break;
					case 'lightred':
						$codes[] = 31;
						$bold = ($bold === NULL ? TRUE : $bold);
						break;
					case 'lime':
						$codes[] = 32;
						$bold = ($bold === NULL ? TRUE : $bold);
						break;
					case 'yellow':
						$codes[] = 33;
						$bold = ($bold === NULL ? TRUE : $bold);
						break;
					case 'lightblue':
						$codes[] = 34;
						$bold = ($bold === NULL ? TRUE : $bold);
						break;
					case 'pink':
						$codes[] = 35;
						$bold = ($bold === NULL ? TRUE : $bold);
						break;
					case 'lightcyan':
						$codes[] = 36;
						$bold = ($bold === NULL ? TRUE : $bold);
						break;
					case 'white':
						$codes[] = 37;
						$bold = ($bold === NULL ? TRUE : $bold);
						break;
					}
				} else // end color tag
				if ($key == 'bg' || $key == 'bgcolor' || $key == 'back') {
					switch ($val) {
					// dark colors
					case 'black':
						$codes[] = 40;
						$bold = ($bold === NULL ? FALSE : $bold);
						break;
					case 'red':
						$codes[] = 41;
						$bold = ($bold === NULL ? FALSE : $bold);
						break;
					case 'green':
						$codes[] = 42;
						$bold = ($bold === NULL ? FALSE : $bold);
						break;
					case 'orange':
						$codes[] = 43;
						$bold = ($bold === NULL ? FALSE : $bold);
						break;
					case 'blue':
						$codes[] = 44;
						$bold = ($bold === NULL ? FALSE : $bold);
						break;
					case 'magenta':
						$codes[] = 45;
						$bold = ($bold === NULL ? FALSE : $bold);
						break;
					case 'cyan':
						$codes[] = 46;
						$bold = ($bold === NULL ? FALSE : $bold);
						break;
					case 'lightgray':
						$codes[] = 47;
						$bold = ($bold === NULL ? FALSE : $bold);
						break;
					// light colors
					case 'gray':
						$codes[] = 40;
						$bold = ($bold === NULL ? TRUE : $bold);
						break;
					case 'lightred':
						$codes[] = 41;
						$bold = ($bold === NULL ? TRUE : $bold);
						break;
					case 'lime':
						$codes[] = 42;
						$bold = ($bold === NULL ? TRUE : $bold);
						break;
					case 'yellow':
						$codes[] = 43;
						$bold = ($bold === NULL ? TRUE : $bold);
						break;
					case 'lightblue':
						$codes[] = 44;
						$bold = ($bold === NULL ? TRUE : $bold);
						break;
					case 'pink':
						$codes[] = 45;
						$bold = ($bold === NULL ? TRUE : $bold);
						break;
					case 'lightcyan':
						$codes[] = 46;
						$bold = ($bold === NULL ? TRUE : $bold);
						break;
					case 'white':
						$codes[] = 47;
						$bold = ($bold === NULL ? TRUE : $bold);
						break;
					}
				} // end bgcolor tag
			} // end {tag=value}
		} // end for
		if ($bold !== NULL) {
			\array_unshift(
				$codes,
				($bold !== FALSE ? 1 : 0)
			);
		}
		if (\count($codes) > 0) {
			$code = \implode(';', $codes);
			return "\033[{$code}m";
		}
		return '{'.$match.'}';
	}



}
