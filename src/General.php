<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;

if (!\ini_get('date.timezone')) {      // @codeCoverageIgnore
	\ini_set('date.timezone', 'UTC'); // @codeCoverageIgnore
}                                     // @codeCoverageIgnore


final class General {
	private final function __construct() {}
	public static function init() {}

	private static $consoleFlags = NULL;



	###############
	## Cast Type ##
	###############



	// cast variable type
	public static function castType($data, $type) {
		switch (\mb_strtolower(\mb_substr( (string) $type, 0, 1))) {
			// string
			case 's':
				return ((string) $data);
			// integer
			case 'i':
				return ((integer) $data);
//TODO: php 7 may have support for this?
			// long
			case 'l':
				return ((integer) $data);
			// float
			case 'f':
				return ((float) $data);
			// double
			case 'd':
				return ((double) $data);
			// boolean
			case 'b':
				return self::toBoolean($data);
			default:
				break;
		}
		return $data;
	}
	// convert to boolean
	public static function toBoolean($value) {
		if (\gettype($value) === 'boolean')
			return $value;
		$val = \mb_strtolower(trim( (string) $value ));
		if ($val == 'on')  return TRUE;
		if ($val == 'off') return FALSE;
		switch (mb_substr($val, 0, 1)) {
			case 't': // true
			case 'y': // yes
			case 'a': // allow
			case 'e': // enable
				return TRUE;
			case 'f': // false
			case 'n': // no
			case 'd': // deny/disable
				return FALSE;
		}
		return ((boolean) $value);
	}



	##############
	## URL Vars ##
	##############



	// get,post,cookie (highest priority last)
	/**
	 * Gets a value from a specific list of sources.
	 * @param string $name - Name or key requested.
	 * @param string $type - Casts value to this type.
	 *     Possible values: str, int, float, double, bool
	 * @param array/string $source - String or array of strings. (from least to greatest importance)
	 *     Possible values: get, post, cookie, session
	 * @return object - Returns the requested value, cast to requested type.
	 */
	public static function getVar($name, $type='str', $source=['get','post']) {
		if (!\is_array($source))
			$source = @\explode(',', (string) $source);
		if (!\is_array($source))
			return NULL;
		$value = NULL;
		foreach ($source as $src) {
			$v = NULL;
			switch (\mb_strtolower(\mb_substr(\trim( (string) $src ), 0, 1))) {
				// get
				case 'g':
					$v = self::get($name, $type);
					break;
				// post
				case 'p':
					$v = self::post($name, $type);
					break;
				// cookie
				case 'c':
					$v = self::cookie($name, $type);
					break;
				// session
				case 's':
					$v = self::session($name, $type);
					break;
				default:
					fail('Unknown value source: '.$src);
					exit(1);
			}
			// value found
			if ($v !== NULL) {
				$value = $v;
			}
		}
		return $value;
	}



	// get var
	public static function get($name, $type) {
		if (isset($_GET[$name]))
			return self::castType($_GET[$name], $type);
		return NULL;
	}
	// post var
	public static function post($name, $type) {
		if (isset($_POST[$name]))
			return self::castType($_POST[$name], $type);
		return NULL;
	}
	// cookie var
	public static function cookie($name, $type) {
		if (isset($_COOKIE[$name]))
			return self::castType($_COOKIE[$name], $type);
		return NULL;
	}
	// php session var
	public static function session($name, $type) {
		if (isset($_SESSION[$name]))
			return self::castType($_SESSION[$name], $type);
		return NULL;
	}



	/**
	 * Parses REQUEST_URI from http request header and inserts into $_GET array.
	 * @example:
	 * URL: http://example.com/page/home/?action=display
	 * // After processing, $_GET contains:
	 * array(
	 *     'page' => 'home',
	 *     'action' => 'display'
	 * );
	 */
	public static function ParseModRewrite() {
		// parse mod_rewrite uri
		if (isset($_SERVER['REDIRECT_STATUS'])) {
			$data = $_SERVER['REQUEST_URI'];
			// parse ? query string
			if (\mb_strpos($data, '?') !== FALSE) {
				list($data, $query) = \explode('?', $data, 2);
				if (!empty($query)) {
					//$arr = explode('&', $query);
					//echo 'query: ?'.$query.'<br />';
				}
			}
			// parse url path
			$data = \array_values(\psm\utils::array_remove_empty(\explode('/', $data)));
			// needs to be even
			if ((\count($data) % 2) != 0)
				$data[] = '';
			// merge values into GET
			for ($i=0; $i<\count($data); $i++) {
				$_GET[$data[$i]] = $data[++$i];
			}
		}
	}



	##########
	## Time ##
	##########



	/**
	 * @return double - Returns current timestamp in seconds.
	 */
	public static function getTimestamp($places=3) {
		$time = \explode(' ', \microtime(), 2);
		if ($places <= 0) {
			return (int) $time[1];
		}
		$timestamp = ((double) $time[0]) + ((double) $time[1]);
		return Numbers::Round($timestamp, $places);
	}
	/**
	 * Sleep execution for x milliseconds.
	 * @param int $ms - Milliseconds to sleep.
	 */
	public static function Sleep($ms) {
		\usleep($ms * 1000.0);
	}



	##################
	## HTTP Headers ##
	##################



	/**
	 * Sends http headers to disable page caching.
	 * @return boolean - TRUE if successful; FALSE if headers already sent.
	 * @codeCoverageIgnore
	 */
	public static function NoPageCache() {
		if (System::isShell()) {
			return;
		}
		if (self::$INITED_NoPageCache)
			return TRUE;
		if (\headers_sent())
			return FALSE;
		@\header('Expires: Mon, 26 Jul 1990 05:00:00 GMT');
		@\header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		@\header('Cache-Control: no-store, no-cache, must-revalidate');
		@\header('Cache-Control: post-check=0, pre-check=0', FALSE);
		@\header('Pragma: no-cache');
		self::$INITED_NoPageCache = TRUE;
		return TRUE;
	}
	private static $INITED_NoPageCache = FALSE;



	/**
	 * Forward to provided url.
	 * @param string $url - The url/address in which to forward to.
	 * @param number $delay - Optional delay in seconds before forwarding.
	 * @codeCoverageIgnore
	 */
	public static function ForwardTo($url, $delay=0) {
		if (System::isShell()) {
			echo "--FORWARD: $url\n";
		} else {
			if (\headers_sent() || $delay > 0) {
				echo '<header><meta http-equiv="refresh" content="'.((int) $delay).';url='.$url.'"></header>';
				echo '<p><a href="'.$url.'"><font size="+1">Continue..</font></a></p>';
			} else {
				\header('HTTP/1.0 302 Found');
				\header('Location: '.$url);
			}
		}
		ExitNow(0);
		exit(0);
	}



	/**
	 * Scroll to the bottom of the page.
	 * @param string $id - Optional id of element in which to scroll.
	 * @codeCoverageIgnore
	 */
	public static function ScrollToBottom($id=NULL) {
		if (System::isShell()) {
			echo "--SCROLL--\n";
		} else {
			if (empty($id)) {
				$id = 'document';
			}
			echo Defines::EOL.'<!-- ScrollToBottom() -->'.Defines::EOL.
				'<script type="text/javascript"><!--//'.Defines::EOL.
				$id.'.scrollTop='.$id.'.scrollHeight; '.
				'window.scroll(0,document.body.offsetHeight); '.
				'//--></script>'.Defines::EOL.Defines::EOL;
		}
	}



	/**
	 * Checks for GD support.
	 * @return boolean - TRUE if GD functions are available.
	 */
	public static function GDSupported() {
		return \function_exists('gd_info');
	}



	/**
	 * Validates an object by class name.
	 * @param string $className - Name of class to look for.
	 * @param object $object - Object to validate.
	 * @return boolean - TRUE if object matches class name.
	 */
	public static function InstanceOfClass($className, $object) {
		if (empty($className)) return FALSE;
		if ($object == NULL)   return FALSE;
		//echo '<p>$className - '.$className.'</p>';
		//echo '<p>get_class($clss) - '.get_class($clss).'</p>';
		//echo '<p>get_parent_class($clss) - '.get_parent_class($clss).'</p>';
		return
			\get_class($object) == $className ||
//			get_parent_class($clss) == $className ||
			\is_subclass_of($object, $className);
	}
	/**
	 * Validates an object by class name, throwing an exception if invalid.
	 * @param string $className - Name of class to check for.
	 * @param object $object - Object to validate.
	 */
	public static function ValidateClass($className, $object) {
		if (empty($className))
			throw new \InvalidArgumentException('classname not defined');
		if ($object == NULL)
			throw new \InvalidArgumentException('object not defined');
		if (!self::InstanceOfClass($className, $object))
			throw new \InvalidArgumentException('Class object isn\'t of type '.$className);
	}



}
