<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
# init 1 - startup
# init 2 - defines
# init 3 - functions
# init 4 - debug
# init 5 - configs
# init 6 - globals
namespace pxn\phpUtils;

use pxn\phpUtils\Defines;
use pxn\phpUtils\General;



########################
##                    ##
##  init 1 - startup  ##
##                    ##
########################



// default error reporting
{
	$isShell = ( isset($_SERVER['SHELL']) && ! empty($_SERVER['SHELL']) );
	\error_reporting(\E_ALL);
	\ini_set('display_errors', 'On');
	\ini_set('html_errors',    $isShell ? 'Off' : 'On');
	\ini_set('log_errors',     'On');
	if ( ! $isShell ) {
		\ini_set('error_log',      'error_log');
	}
}

// php version 5.6 required
if (\PHP_VERSION_ID < 50600) {
	echo '<p>PHP 5.6 or newer is required!</p>'; exit(1);
}

// atomic defines
if (\defined('pxn\\phpUtils\\INDEX_DEFINE')) {
	echo '<h2>Unknown state! init.php already loaded?</h2>';
	exit(1);
}
if (\defined('pxn\\phpUtils\\PORTAL_LOADED')) {
	echo '<h2>Unknown state! Portal already loaded?</h2>';
	exit(1);
}
\define('pxn\\phpUtils\\INDEX_DEFINE', TRUE);

// timezone
//TODO: will make a config entry for this
try {
	$zone = @date_default_timezone_get();
	if ($zone == 'UTC') {
		@date_default_timezone_set(
			'America/New_York'
		);
	} else {
		@date_default_timezone_set(
			@date_default_timezone_get()
		);
	}
	unset($zone);
} catch (\Exception $ignore) {}



########################
##                    ##
##  init 2 - defines  ##
##                    ##
########################



// defines
\pxn\phpUtils\Defines::init();

// paths
\pxn\phpUtils\Paths::init();



##########################
##                      ##
##  init 3 - functions  ##
##                      ##
##########################



//$phpUtils_logger = NULL;
//if (!\function_exists('log')) {
//	function log() {
//		global $phpUtils_logger;
//		if ($phpUtils_logger == NULL)
//TODO:
//			$phpUtils_logger = new logger();
//		return $phpUtils_logger;
//	}
//}
//class logger {
//TODO:
//}



//// php session
//if (function_exists('session_status'))
//	if (session_status() == PHP_SESSION_DISABLED){
//	echo '<p>PHP Sessions are disabled. This is a requirement, please enable this.</p>';
//	exit;
//}
//session_init();



//// init php sessions
//private static $session_init_had_run = FALSE;
//public static function session_init() {
//	if (self::$session_init_had_run) return;
//	\session_start();
//	self::$session_init_had_run = TRUE;
//}



//function addlog($text){global $config,$pathroot;
//if (substr($config['log file'],-4)!='.txt'){die('error in log file var');}
//$fp=@fopen($pathroot.$config['log file'],'a') or die('failed to write log');
//fwrite($fp,date('Y-m-d H:i:s').' - '.trim($text)."\r\n");
//fclose($fp);
//}



// dump()
function dump($var) {
	d($var);
}
// d()
function d($var) {
	$CRLF = "\n";
	echo '<pre style="color: black; background-color: #dfc0c0; padding: 10px;">';
	\var_dump($var);
	echo '</pre>'.$CRLF;
}
// dd()
function dd($var) {
	d($var);
	exit(1);
}



// exit functions
function ExitNow($code=1) {
	$website = \pxn\phpUtils\portal\Website::peak();
	// set rendered
	if ($website !== NULL) {
		$website->hasRendered(TRUE);
	}
	// exit code
	if ($code !== NULL) {
		exit( ((int) $code) );
	}
	exit(0);
}
function fail($msg, $code=1, \Exception $e=NULL) {
	if (!\is_string($msg)) {
		$msg = \print_r($msg, TRUE);
	}
	if (System::isShell()) {
		echo " *** {$msg} *** \n";
	} else {
		echo '<pre style="color: black; background-color: #ffaaaa; '.
				'padding: 10px;"><font size="+2">FATAL: '.$msg."</font></pre>\n";
	}
	if (\pxn\phpUtils\debug()) {
		if ($e == NULL) {
			\backtrace();
		} else {
			if (System::isShell()) {
				echo $e->getTraceAsString();
			} else {
				echo $e->getTrace();
			}
		}
		echo "\n";
	}
	if ($code !== NULL) {
		ExitNow($code);
	}
}
function backtrace() {
	$CRLF = "\n";
	//TODO: is this right?
	$trace = \debug_backtrace();
	$ignore = [
		'inc.php' => [
			'fail',
			'backtrace',
			'autoload',
			'__autoload',
		],
	];
//	$ignore = array();
	foreach ($trace as $index => $tr) {
		if (!isset($tr['file'])) continue;
		$file = \basename($tr['file']);
		if (isset($ignore[$file])) {
			$func = $tr['function'];
			if (\in_array($func, $ignore[$file]))
				unset($trace[$index]);
		}
	}
	echo '<table style="background-color: #ffeedd; padding: 10px; '.
		'border-width: 1px; border-style: solid; border-color: #aaaaaa;">'.$CRLF;
	$first = TRUE;
	$evenodd = FALSE;
	foreach ($trace as $num => $tr) {
		if (!$first) {
			echo '<tr><td height="20">&nbsp;</td></tr>';
		}
		$evenodd = ! $evenodd;
		$bgcolor = ($evenodd ? '#ffe0d0' : '#fff8e8');
		$first = FALSE;
		echo '<tr style="background-color: '.$bgcolor.';">'.$CRLF;
		echo \TAB.'<td><font size="-2">#'.((int) $num).'</font></td>'.$CRLF;
		echo \TAB.'<td>'.@$tr['file'].'</td>'.$CRLF;
		echo '</tr>'.$CRLF;
		echo '<tr style="background-color: '.$bgcolor.';">'.$CRLF;
		echo \TAB.'<td></td>'.$CRLF;
		$args = '';
		foreach ($tr['args'] as $arg) {
			if (!empty($args))
				$args .= ', ';
			if (\is_string($arg)) {
				if (\strpos($arg, $CRLF)) {
					$args .= '<pre>'.$arg.'</pre>';
				} else {
					$args .= $arg;
				}
			} else {
				$args .= \print_r($arg, TRUE);
			}
		}
		echo TAB.'<td>'.
				(isset($tr['file']) ? '<i>'.\basename($tr['file']).'</i> ' : '' ).
				'<font size="-1">--&gt;</font> '.
				'<b>'.$tr['function'].'</b> '.
				'( '.$args.' ) '.
				(isset($tr['line']) ? '<font size="-1">line: '.$tr['line'].'</font>' : '' ).
				'</td>'.$CRLF;
		echo '</tr>'.$CRLF;
	}
	echo '</table>'.$CRLF;
	//dump($trace);
}



######################
##                  ##
##  init 4 - debug  ##
##                  ##
######################



// debug mode
global $pxnUtils_DEBUG;
$pxnUtils_DEBUG = NULL;
function debug($debug=NULL) {
	global $pxnUtils_DEBUG;
	if ($debug !== NULL) {
		$last = $pxnUtils_DEBUG;
		$pxnUtils_DEBUG = General::toBoolean($debug);
		// update debug mode
		if ($pxnUtils_DEBUG != $last) {
			// enabled
			if ($pxnUtils_DEBUG) {
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
	if ($pxnUtils_DEBUG === NULL)
		debug(FALSE);
	return $pxnUtils_DEBUG;
}
// by define
if (\defined('\DEBUG'))
	debug(\DEBUG);
if (\defined('pxn\\phpUtils\\DEBUG'))
	debug(\pxn\phpUtils\DEBUG);
// by url
$val = General::getVar('debug', 'bool');
if ($val !== NULL) {
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
	if ($val === TRUE)
		debug($val);
}
unset($val);
// ensure inited
debug();

/*
// Kint backtracer
if (file_exists(paths::getLocal('portal').DIR_SEP.'kint.php')) {
	include(paths::getLocal('portal').DIR_SEP.'kint.php');
}
// php_error
if (file_exists(paths::getLocal('portal').DIR_SEP.'php_error.php')) {
	include(paths::getLocal('portal').DIR_SEP.'php_error.php');
}
// Kint backtracer
$kintPath = paths::getLocal('portal').DIR_SEP.'debug'.DIR_SEP.'kint'.DIR_SEP.'Kint.class.php';
if (file_exists($kintPath)) {
	//global $GLOBALS;
	//if (!@is_array(@$GLOBALS)) $GLOBALS = array();
	//$_kintSettings = &$GLOBALS['_kint_settings'];
	//$_kintSettings['traceCleanupCallback'] = function($traceStep) {
	//echo '<pre>';print_r($traceStep);exit();
	//	if (isset($traceStep['class']) && $traceStep['class'] === 'Kint')
	//		return NULL;
	//	if (isset($traceStep['function']) && \strtolower($traceStep['function']) === '__tostring')
	//		$traceStep['function'] = '[object converted to string]';
	//	return $traceStep;
	//};
	//echo '<pre>';print_r($_kintSettings);exit();
	include($kintPath);
	}
	// php_error
	$phpErrorPath = paths::getLocal('portal').DIR_SEP.'debug'.DIR_SEP.'php_error.php';
	if (file_exists($phpErrorPath))
		include($phpErrorPath);
		if (function_exists('php_error\\reportErrors')) {
			$reportErrors = '\\php_error\\reportErrors';
			$reportErrors([
					'catch_ajax_errors'      => TRUE,
					'catch_supressed_errors' => FALSE,
					'catch_class_not_found'  => FALSE,
					'snippet_num_lines'      => 11,
					'application_root'       => __DIR__,
					'background_text'        => 'PSM',
			]);
		}
	}
}
// error page
public static function Error($msg) {
	echo '<div style="background-color: #ffbbbb;">'.CRLF;
	if (!empty($msg))
		echo '<h4>Error: '.$msg.'</h4>'.CRLF;
	echo '<h3>Backtrace:</h3>'.CRLF;
//	if (\method_exists('Kint', 'trace'))
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
	if (0 === error_reporting())
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
	foreach ($e->getTrace() as $t)
		\var_dump($t);
	exit(1);
});
*/



########################
##                    ##
##  init 5 - configs  ##
##                    ##
########################



\pxn\phpUtils\Config::init();



########################
##                    ##
##  init 6 - globals  ##
##                    ##
########################



require ('Globals.php');
