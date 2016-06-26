<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;


final class Numbers {
	private final function __construct() {}



	/**
	 * This function is a more specific test of a value. The native
	 * \is_numeric() function also returns true for hex values.
	 * @param string $value
	 * @return boolean Returns true if only contains number characters after
	 *         being trimmed.
	 */
	public static function isNumber($value) {
		if ($value === NULL)
			return FALSE;
		$value = \trim( (string)$value );
		if ($value === '')
			return FALSE;
		$value = Strings::TrimFront($value, '0');
		if ($value == '')
			return TRUE;
		$i = ((string) ((int)$value) );
		return ($value === $i);
	}



	##########
	## Math ##
	##########



	/**
	 * Check the min and max of a value and return the result.
	 * @param int $value -
	 * @param int $min -
	 * @param int $max -
	 * @return int value
	 */
	public static function MinMax($value, $min=FALSE, $max=FALSE) {
		if ($min !== FALSE && $max !== FALSE && $min > $max)
			throw new \Exception('Min must be less than Max!');
		if ($min !== FALSE && $value < $min)
			return $min;
		if ($max !== FALSE && $value > $max)
			return $max;
		return $value;
	}



	############
	## Format ##
	############



	public static function Round($value, $places=0) {
		if ($places == 0)
			return \round($value);
		$pow = \pow(10, $places);
		return self::PadZeros(
			\round($value * $pow) / $pow,
			$places
		);
	}
	public static function Floor($value, $places=0) {
		if ($places == 0)
			return \floor($value);
		$pow = \pow(10, $places);
		return self::PadZeros(
			\floor($value * $pow) / $pow,
			$places
		);
	}
	public static function Ceil($value, $places=0) {
		if ($places == 0)
			return \ceil($value);
		$pow = \pow(10, $places);
		return self::PadZeros(
			\ceil($value * $pow) / $pow,
			$places
		);
	}
	public static function PadZeros($value, $places) {
		$str = (string) (double) $value;
		if ($places <= 0)
			return $str;
		$pos = \mb_strrpos($str, '.');
		if ($pos === FALSE)
			return $str.'.'.\str_repeat('0', $places);
		$pos = \mb_strlen($str) - ($pos + 1);
		if ($pos < $places)
			return $str.\str_repeat('0', $places - $pos);
		return $str;
	}



	/**
	 * Convert bytes to human readable format.
	 * @param int $size - Integer in bytes to convert.
	 * @return string - Formatted size.
	 */
	public static function FormatBytes($size) {
		if (empty($size))
			return NULL;
		$size = \trim((string) $size);
		if (\mb_strtolower(\mb_substr($size, -1, 1)) == 'b') {
			$size = \trim(\mb_substr($size, 0, -1));
		}
		switch ( \mb_strtolower(\mb_substr($size, -1, 1)) ) {
			case 'k':
				$size = ((double) $size) * Defines::KB;
				break;
			case 'm':
				$size = ((double) $size) * Defines::MB;
				break;
			case 'g':
				$size = ((double) $size) * Defines::GB;
				break;
			case 't':
				$size = ((double) $size) * Defines::TB;
				break;
			default:
				$size = (double) $size;
				break;
		}
		if ($size < 0)
			return NULL;
		if ($size < Defines::KB)
			return \round($size, 0).'B';
		if ($size < Defines::MB)
			return \round($size / Defines::KB, 2).'KB';
		if ($size < Defines::GB)
			return \round($size / Defines::MB, 2).'MB';
		if ($size < Defines::TB)
			return \round($size / Defines::GB, 2).'GB';
		return \round($size / Defines::TB, 2).'TB';
	}



	/**
	 * Convert a number to roman numerals.
	 * @param int $value -
	 * @param int $max -
	 * @return string - Roman numerals string representing input number.
	 */
	public static function FormatRoman($value, $max=FALSE) {
		$value = (int) $value;
		if ( ($max !== FALSE && $value > $max) || $value < 0) {
			return ((string) $value);
		}
		$result = '';
		$lookup = array(
			'M' => 1000,
			'CM'=> 900,
			'D' => 500,
			'CD'=> 400,
			'C' => 100,
			'XC'=> 90,
			'L' => 50,
			'XL'=> 40,
			'X' => 10,
			'IX'=> 9,
			'V' => 5,
			'IV'=> 4,
			'I' => 1
		);
		foreach ($lookup as $roman => $num) {
			$matches = \intval($value / $num);
			$result .= \str_repeat($roman, $matches);
			$value = $value % $num;
		}
		return $result;
	}



	##########
	## Time ##
	##########



	/**
	 * String to seconds.
	 * @param string $text - String to convert.
	 * @return int seconds
	 */
	public static function StringToSeconds($text) {
		$str = '';
		$value = 0;
		for ($i = 0; $i < \mb_strlen($text); $i++) {
			$s = \mb_substr($text, $i, 1);
			if (self::isNumber($s)) {
				$str .= $s;
				continue;
			}
			if ($s === ' ') continue;
			$val = (int) $str;
			$str = '';
			if ($val == 0) continue;
			switch (\mb_strtolower($s)) {
				case 'n':
					$value += ($val * Defines::S_MS);
					break;
				case 's';
					$value += ($val * Defines::S_SECOND);
					break;
				case 'm':
					$value += ($val * Defines::S_MINUTE);
					break;
				case 'h':
					$value += ($val * Defines::S_HOUR);
					break;
				case 'd':
					$value += ($val * Defines::S_DAY);
					break;
				case 'w':
					$value += ($val * Defines::S_WEEK);
					break;
				case 'o':
					$value += ($val * Defines::S_MONTH);
					break;
				case 'y':
					$value += ($val * Defines::S_YEAR);
					break;
				default:
					continue;
			}
		}
		return $value;
	}
	/**
	 * Seconds to formatted string.
	 * @param int $seconds - Integer to convert.
	 * @return string
	 */
	public static function SecondsToString($seconds, $shorthand=TRUE, $maxParts=FALSE, $deviance=1.0) {
		$maxParts =
			($maxParts == FALSE)
			? FALSE
			: (int) $maxParts;
		$result = array();
		// years
		if ($seconds >= (Defines::S_YEAR * $deviance)) {
			$v = \ceil(\floor($seconds / Defines::S_YEAR / $deviance) * $deviance);
			$seconds -= $v * Defines::S_YEAR;
			$result[] = $v.(
				$shorthand
				? 'yr'
				: ' Year'.($v > 1 ? 's' : '')
			);
		}
		// months
		if ($deviance !== 1.0) {
		if ($maxParts == FALSE || count($result) < $maxParts) {
		if ($seconds >= (Defines::S_MONTH * $deviance)) {
			$v = \ceil(\floor($seconds / Defines::S_MONTH / $deviance) * $deviance);
			$seconds -= $v * Defines::S_MONTH;
			$result[] = $v.(
				$shorthand
				? 'mon'
				: ' Month'.($v > 1 ? 's' : '')
			);
		}}}
		// days
		if ($maxParts == FALSE || count($result) < $maxParts) {
		if ($seconds >= (Defines::S_DAY * $deviance)) {
			$v = \ceil(\floor($seconds / Defines::S_DAY / $deviance) * $deviance);
			$seconds -= $v * Defines::S_DAY;
			$result[] = $v.(
				$shorthand
				? 'day'
				: ' Day'.($v > 1 ? 's' : '')
			);
		}}
		// hours
		if ($maxParts == FALSE || count($result) < $maxParts) {
		if ($seconds >= (Defines::S_HOUR * $deviance)) {
			$v = \ceil(\floor($seconds / Defines::S_HOUR / $deviance) * $deviance);
			$seconds -= $v * Defines::S_HOUR;
			$result[] = $v.(
				$shorthand
				? 'hr'
				: ' Hour'.($v > 1 ? 's' : '')
			);
		}}
		// minutes
		if ($maxParts == FALSE || count($result) < $maxParts) {
		if ($seconds >= (Defines::S_MINUTE * $deviance)) {
			$v = \ceil(\floor($seconds / Defines::S_MINUTE / $deviance) * $deviance);
			$seconds -= $v * Defines::S_MINUTE;
			$result[] = $v.(
				$shorthand
				? 'min'
				: ' Minute'.($v > 1 ? 's' : '')
			);
		}}
		// seconds
		if ($maxParts == FALSE || count($result) < $maxParts) {
		if ($seconds > 0) {
			$result[] = $seconds.(
				$shorthand
				? 'sec'
				: ' Second'.
				($seconds > 1 ? 's' : '')
			);
		}}
		if (\count($result) == 0)
			return '--';
		if ($shorthand)
			return \implode(' ', $result);
		return \implode('  ', $result);
	}
	public static function SecondsToText($seconds, $shorthand=TRUE, $maxParts=FALSE, $deviance=1.0) {
		// future
		if ( $seconds < 0 ) {
			$seconds = 0 - $seconds;
			if ( ($seconds * $deviance) < Defines::S_HOUR ) {
				return 'Soon';
			}
			if ( ($seconds * $deviance) < Defines::S_DAY ) {
				return 'Soon Today';
			}
			if ( ($seconds * $deviance) < (Defines::S_DAY * 2) ) {
				return 'Tomorrow';
			}
			return self::SecondsToString($seconds, $shorthand, $maxParts, $deviance).' from now';
		}
		// now
		if ( $seconds * $deviance < Defines::S_HOUR ) {
			return 'Now';
		}
		// past
		if ( ($seconds * $deviance) < Defines::S_DAY ) {
			return 'Today';
		}
		if ( ($seconds * $deviance) < (Defines::S_DAY * 2) ) {
			return 'Yesterday';
		}
		return self::SecondsToString($seconds, $shorthand, $maxParts, $deviance).' ago';
	}



}
