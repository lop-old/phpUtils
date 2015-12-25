<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;

use \pxn\phpUtils\Defines;
use \pxn\phpUtils\General;


// debug mode
global $pxnUtils_DEBUG;
$pxnUtils_DEBUG = NULL;
function debug($debug=NULL) {
	global $pxnUtils_DEBUG;
	if($debug !== NULL) {
		$last = $pxnUtils_DEBUG;
		$pxnUtils_DEBUG = General::toBoolean($debug);
		// update debug mode
		if($pxnUtils_DEBUG != $last) {
			// enabled
			if($pxnUtils_DEBUG) {
				\error_reporting(\E_ALL | \E_STRICT);
				\ini_set('display_errors', 'On');
				\ini_set('html_errors',    'On');
			// disabled
			} else {
				\error_reporting(\E_ERROR | \E_WARNING | \E_PARSE | \E_NOTICE);
				\ini_set('display_errors', 'Off');
			}
		}
	}
	// default to false
	if($pxnUtils_DEBUG === NULL)
		debug(FALSE);
	return $pxnUtils_DEBUG;
}
// by define
if(\defined('\DEBUG'))
	debug(\DEBUG);
if(\defined('pxn\\phpUtils\\DEBUG'))
	debug(\pxn\phpUtils\DEBUG);
// by url
$val = General::getVar('debug', 'bool');
if($val !== NULL) {
	// set cookie
	\setcookie(
		Defines::getDebugCookieName(),
		($val === TRUE ? '1' : '0'),
		0
	);
	debug($val);
} else {
	// from cookie
	$val = General::getVar(
		Defines::getDebugCookieName(),
		'bool',
		'cookie'
	);
	if($val === TRUE)
		debug($val);
}
unset($val);
// ensure inited
debug();


/*
// Kint backtracer
if(file_exists(paths::getLocal('portal').DIR_SEP.'kint.php')) {
	include(paths::getLocal('portal').DIR_SEP.'kint.php');
}
// php_error
if(file_exists(paths::getLocal('portal').DIR_SEP.'php_error.php')) {
	include(paths::getLocal('portal').DIR_SEP.'php_error.php');
}
// Kint backtracer
$kintPath = paths::getLocal('portal').DIR_SEP.'debug'.DIR_SEP.'kint'.DIR_SEP.'Kint.class.php';
if(file_exists($kintPath)) {
	//global $GLOBALS;
	//if(!@is_array(@$GLOBALS)) $GLOBALS = array();
	//$_kintSettings = &$GLOBALS['_kint_settings'];
	//$_kintSettings['traceCleanupCallback'] = function($traceStep) {
	//echo '<pre>';print_r($traceStep);exit();
	//	if(isset($traceStep['class']) && $traceStep['class'] === 'Kint')
	//		return NULL;
	//	if(isset($traceStep['function']) && \strtolower($traceStep['function']) === '__tostring')
	//		$traceStep['function'] = '[object converted to string]';
	//	return $traceStep;
	//};
	//echo '<pre>';print_r($_kintSettings);exit();
	include($kintPath);
	}
	// php_error
	$phpErrorPath = paths::getLocal('portal').DIR_SEP.'debug'.DIR_SEP.'php_error.php';
	if(file_exists($phpErrorPath))
		include($phpErrorPath);
		if(function_exists('php_error\\reportErrors')) {
			$reportErrors = '\\php_error\\reportErrors';
			$reportErrors(array(
					'catch_ajax_errors'      => TRUE,
					'catch_supressed_errors' => FALSE,
					'catch_class_not_found'  => FALSE,
					'snippet_num_lines'      => 11,
					'application_root'       => __DIR__,
					'background_text'        => 'PSM',
			));
		}
	}
}
// error page
public static function Error($msg) {
	echo '<div style="background-color: #ffbbbb;">'.CRLF;
	if(!empty($msg))
		echo '<h4>Error: '.$msg.'</h4>'.CRLF;
	echo '<h3>Backtrace:</h3>'.CRLF;
//	if(\method_exists('Kint', 'trace'))
//		\Kint::trace();
//	else
		echo '<pre>'.print_r(\debug_backtrace(), TRUE).'</pre>';
	echo '</div>'.CRLF;
//	\psm\Portal::Unload();
	exit(1);
}
*/


/*
\set_error_handler(
function ($severity, $msg, $filename, $line, array $err_context) {
	if(0 === error_reporting())
		return FALSE;
	switch($severity) {
	case E_ERROR:             throw new ErrorException            ($msg, 0, $severity, $filename, $line);
	case E_WARNING:           throw new WarningException          ($msg, 0, $severity, $filename, $line);
	case E_PARSE:             throw new ParseException            ($msg, 0, $severity, $filename, $line);
	case E_NOTICE:            throw new NoticeException           ($msg, 0, $severity, $filename, $line);
	case E_CORE_ERROR:        throw new CoreErrorException        ($msg, 0, $severity, $filename, $line);
	case E_CORE_WARNING:      throw new CoreWarningException      ($msg, 0, $severity, $filename, $line);
	case E_COMPILE_ERROR:     throw new CompileErrorException     ($msg, 0, $severity, $filename, $line);
	case E_COMPILE_WARNING:   throw new CoreWarningException      ($msg, 0, $severity, $filename, $line);
	case E_USER_ERROR:        throw new UserErrorException        ($msg, 0, $severity, $filename, $line);
	case E_USER_WARNING:      throw new UserWarningException      ($msg, 0, $severity, $filename, $line);
	case E_USER_NOTICE:       throw new UserNoticeException       ($msg, 0, $severity, $filename, $line);
	case E_STRICT:            throw new StrictException           ($msg, 0, $severity, $filename, $line);
	case E_RECOVERABLE_ERROR: throw new RecoverableErrorException ($msg, 0, $severity, $filename, $line);
	case E_DEPRECATED:        throw new DeprecatedException       ($msg, 0, $severity, $filename, $line);
	case E_USER_DEPRECATED:   throw new UserDeprecatedException   ($msg, 0, $severity, $filename, $line);
	}
});
class WarningException          extends \ErrorException {}
class ParseException            extends \ErrorException {}
class NoticeException           extends \ErrorException {}
class CoreErrorException        extends \ErrorException {}
class CoreWarningException      extends \ErrorException {}
class CompileErrorException     extends \ErrorException {}
class CompileWarningException   extends \ErrorException {}
class UserErrorException        extends \ErrorException {}
class UserWarningException      extends \ErrorException {}
class UserNoticeException       extends \ErrorException {}
class StrictException           extends \ErrorException {}
class RecoverableErrorException extends \ErrorException {}
class DeprecatedException       extends \ErrorException {}
class UserDeprecatedException   extends \ErrorException {}
*/


/*
\set_exception_handler(
function (\Exception $e) {
	echo '<h1>Uncaught Exception</h1>'.CRLF;
	echo '<h2>'.$e->getMessage().'</h2>'.CRLF;
	echo '<h3>Line '.$e->getLine().' of '.$e->getFile().'</h3>'.CRLF;
	foreach($e->getTrace() as $t)
		\var_dump($t);
	exit(1);
});
*/
