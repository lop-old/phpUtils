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

if(!\ini_get('date.timezone')) {      // @codeCoverageIgnore
	\ini_set('date.timezone', 'UTC'); // @codeCoverageIgnore
}                                     // @codeCoverageIgnore

final class General {
	private final function __construct() {}



	##########
	## Time ##
	##########



	/**
	 * @return double - Returns current timestamp in seconds.
	 */
	public static function getTimestamp() {
		$time = \explode(' ', \microtime(), 2);
		$timestamp = ((double) $time[0]) + ((double) $time[1]);
		return Numbers::Round($timestamp, 3);
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
		if(self::$INITED_NoPageCache)
			return TRUE;
		if(\headers_sent())
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
		if(\headers_sent() || $delay > 0) {
			echo '<header><meta http-equiv="refresh" content="'.((int) $delay).';url='.$url.'"></header>';
			echo '<p><a href="'.$url.'"><font size="+1">Continue..</font></a></p>';
		} else {
			\header('HTTP/1.0 302 Found');
			\header('Location: '.$url);
		}
		exit();
	}



	/**
	 * Scroll to the bottom of the page.
	 * @param string $id - Optional id of element in which to scroll.
	 * @codeCoverageIgnore
	 */
	public static function ScrollToBottom($id='') {
		if(empty($id)) $id = 'document';
		echo Defines::EOL.'<!-- ScrollToBottom() -->'.Defines::EOL.
				'<script type="text/javascript"><!--//'.Defines::EOL.
				$id.'.scrollTop='.$id.'.scrollHeight; '.
				'window.scroll(0,document.body.offsetHeight); '.
				'//--></script>'.Defines::EOL.Defines::EOL;
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
		if(empty($className)) return FALSE;
		if($object == NULL)   return FALSE;
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
		if(empty($className))
			throw new \InvalidArgumentException('classname not defined');
		if($object == NULL)
			throw new \InvalidArgumentException('object not defined');
		if(!self::InstanceOfClass($className, $object))
			throw new \InvalidArgumentException('Class object isn\'t of type '.$className);
	}



}
