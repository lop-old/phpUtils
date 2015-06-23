<?php
/**
 * PoiXson phpUtils
 *
 * @copyright 2004-2015
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;

final class Strings {
	private final function __construct() {}



	public static function Trim($text, ...$remove) {
		if(!\is_array($remove) || \count($remove) == 0)
			$remove = [ ' ', "\t", "\r", "\n" ];
		$allshort = TRUE;
		foreach($remove as $str) {
			if(\strlen($str) > 1) {
				$allshort = FALSE;
				break;
			}
		}
		if($allshort) {
			while(\in_array(\substr($text, 0, 1), $remove))
				$text = \substr($text, 1);
			while(\in_array(\substr($text, -1, 1), $remove))
				$text = \substr($text, 0, -1);
		} else {
			do {
				$more = FALSE;
				foreach($remove as $str) {
					$len = \strlen($str);
					if($len == 0) continue;
					while(\substr($text, 0, $len) == $str) {
						$text = \substr($text, $len);
						$more = TRUE;
					}
					while(\substr($text, 0 - $len, $len) == $str) {
						$text = \substr($text, 0, 0 - $len);
						$more = TRUE;
					}
					if($more) break;
				}
			} while($more);
		}
		return $text;
	}
	public static function TrimFront($text, ...$remove) {
		if(!\is_array($remove) || \count($remove) == 0)
			$remove = [ ' ', "\t", "\r", "\n" ];
		$allshort = TRUE;
		foreach($remove as $str) {
			if(\strlen($str) > 1) {
				$allshort = FALSE;
				break;
			}
		}
		if($allshort) {
			while(\in_array(\substr($text, 0, 1), $remove))
				$text = \substr($text, 1);
		} else {
			do {
				$more = FALSE;
				foreach($remove as $str) {
					$len = \strlen($str);
					if($len == 0) continue;
					while(\substr($text, 0, $len) == $str) {
						$text = \substr($text, $len);
						$more = TRUE;
					}
					if($more) break;
				}
			} while($more);
		}
		return $text;
	}
	public static function TrimEnd($text, ...$remove) {
		if(!\is_array($remove) || \count($remove) == 0)
			$remove = [ ' ', "\t", "\r", "\n" ];
		$allshort = TRUE;
		foreach($remove as $str) {
			if(\strlen($str) > 1) {
				$allshort = FALSE;
				break;
			}
		}
		if($allshort) {
			while(\in_array(\substr($text, -1, 1), $remove))
				$text = \substr($text, 0, -1);
		} else {
			do {
				$more = FALSE;
				foreach($remove as $str) {
					$len = \strlen($str);
					if($len == 0) continue;
					while(\substr($text, 0 - $len, $len) == $str) {
						$text = \substr($text, 0, 0 - $len);
						$more = TRUE;
					}
					if($more) break;
				}
			} while($more);
		}
		return $text;
	}



	/**
	 * Removes paired quotes from a string.
	 * @param string $text - String in which to remove quotes.
	 * @return string - String with ' and " quotes removed.
	 */
	public static function TrimQuotes($text) {
		while(\strlen($text) > 2) {
			$f = \substr($text, 0, 1);
			$e = \substr($text, -1, 1);
			// trim ' quotes
			if($f == Defines::QUOTE_S && $e == Defines::QUOTE_S) {
				$text = \substr($text, 1, -1);
			} else
			// trim " quotes
			if($f == Defines::QUOTE_D && $e == Defines::QUOTE_D) {
				$text = \substr($text, 1, -1);
			} else
			if($f == Defines::ACCENT && $e == Defines::ACCENT) {
				$text = \substr($text, 1, -1);
			} else {
				break;
			}
		}
		return $text;
	}



}
