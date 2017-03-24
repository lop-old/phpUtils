<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;

use pxn\phpUtils\Config;
use pxn\phpUtils\Defines;


final class ShellTools {
	private final function __construct() {}

	private static $inited = FALSE;

	public static $ANSI_COLOR_ENABLED = NULL;

	private static $flags = NULL;
	private static $args  = NULL;
	private static $stat  = NULL;



	public static function init() {
		if (self::$inited) {
			return;
		}
		Config::setDefault(Defines::KEY_ALLOW_SHORT_FLAG_VALUES, FALSE);
		self::initConsoleVars();
		self::$inited = TRUE;
		// ansi color enabled
		if (self::hasFlag('--ansi')) {
			self::setAnsiColorEnabled(TRUE);
		}
		// ansi color disabled
		if (self::hasFlag('--no-ansi')) {
			self::setAnsiColorEnabled(FALSE);
		}
		// detect color support
		if (self::$ANSI_COLOR_ENABLED === NULL) {
			self::$ANSI_COLOR_ENABLED = (self::$stat['stdout'] == 'chr');
		}
		// clear screen
		if (self::isAnsiColorEnabled()) {
			echo self::FormatString('{clear}');
		}
	}
	private static function initConsoleVars() {
		if (self::$inited) {
			return;
		}
		if (!System::isShell()) {
			return FALSE;
		}
		if (self::$flags !== NULL || self::$args !== NULL) {
			return FALSE;
		}
		// detect shell state
		self::$stat = [
			'stdin'  => self::getStat(\STDIN),
			'stdout' => self::getStat(\STDOUT),
			'stderr' => self::getStat(\STDERR)
		];
		// parse shell arguments
		$AllowShortFlagValues = Config::get(Defines::KEY_ALLOW_SHORT_FLAG_VALUES);
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
					continue;
				}
				// --flag value
				if (isset($argv[$index+1])) {
					$val = $argv[$index+1];
					if (!Strings::StartsWith($val, '-')) {
						$index++;
						self::$flags[$arg] = $val;
						continue;
					}
				}
				// --flag
				if (!isset(self::$flags[$arg])) {
					self::$flags[$arg] = TRUE;
				}
				continue;
			}
			// -flag
			if (Strings::StartsWith($arg, '-')) {
				// attached value
				$len = \mb_strlen($arg);
				if ($len > 2) {
					$val = \mb_substr($arg, 2);
					$arg = \mb_substr($arg, 0, 2);
					if (\mb_substr($val, 0, 1) == '=') {
						$val = \mb_substr($val, 1);
					}
					self::$flags[$arg] = $val;
					continue;
				}
				// -f value
				if ($AllowShortFlagValues) {
					if (isset($argv[$index+1])) {
						$val = $argv[$index+1];
						if (!Strings::StartsWith($val, '-')) {
							$index++;
							self::$flags[$arg] = $val;
							continue;
						}
					}
				}
				// -f
				if (!isset(self::$flags[$arg])) {
					self::$flags[$arg] = TRUE;
				}
				continue;
			}
			// not flag, must be argument
			self::$args[] = $arg;
		}
		return TRUE;
	}



	public static function getStat($handle) {
		$stat = \fstat($handle);
		$mode = $stat['mode'] & 0170000;
		switch ($mode) {
		case 0010000:
			return 'fifo';
		case 0020000:
			return 'chr';
		case 0040000:
			return 'dir';
		case 0060000:
			return 'blk';
		case 0100000:
			return 'reg';
		case 0120000:
			return 'lnk';
		case 0140000:
			return 'sock';
		}
		return NULL;
	}


	// get all as array
	public static function getArgs() {
		return self::$args;
	}
	public static function getFlags() {
		return self::$flags;
	}



	// get argument (starts at 0)
	public static function getArg($index=NULL) {
		if ($index === NULL) {
			return self::getArg(0);
		}
		$index = (int) $index;
		if (!isset(self::$args[$index])) {
			return NULL;
		}
		return self::$args[$index];
	}
	public static function hasArg($arg) {
		if (empty($arg)) {
			return NULL;
		}
		// match case
		if (\in_array($arg, self::$args)) {
			return TRUE;
		}
		// case-insensitive
		if (\in_array( \strtolower($arg), \array_map('\\strtolower', self::$args) )) {
			return TRUE;
		}
		return FALSE;
	}



	// get one flag
	public static function getFlag(... $keys) {
		if (\count($keys) == 0) {
			return NULL;
		}
		foreach ($keys as $key) {
			$val = self::getFlag_Single($key);
			if ($val !== NULL) {
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
			// don't allow "-x value"
			$AllowShortFlagValues = Config::get(Defines::KEY_ALLOW_SHORT_FLAG_VALUES);
			if (!$AllowShortFlagValues) {
				if (!Strings::StartsWith($key, '--')) {
					return TRUE;
				}
			}
			return self::$flags[$key];
		}
		return NULL;
	}



	// get boolean flag
	public static function getFlagBool(... $keys) {
		if (\count($keys) == 0) {
			return NULL;
		}
		foreach ($keys as $key) {
			$val = self::getFlagBool_Single($key);
			if ($val !== NULL) {
				return $val;
			}
		}
		return NULL;
	}
	private static function getFlagBool_Single($key) {
		if (empty($key)) {
			return NULL;
		}
		return General::toBoolean(
			self::getFlag($key)
		);
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
			return '';
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
				case 'clear':
					return "\033[2J\033[1;1H";
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
