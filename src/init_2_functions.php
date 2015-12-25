<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace {

use \pxn\phpUtils\Numbers;


$phpUtils_logger = NULL;
if(!\function_exists('log')) {
	function log() {
		global $phpUtils_logger;
		if($phpUtils_logger == NULL)
//TODO:
			$phpUtils_logger = new logger();
		return $phpUtils_logger;
	}
}
class logger {
//TODO:
}


//// php session
//if(function_exists('session_status'))
//	if(session_status() == PHP_SESSION_DISABLED){
//	echo '<p>PHP Sessions are disabled. This is a requirement, please enable this.</p>';
//	exit;
//}
//session_init();


//// init php sessions
//private static $session_init_had_run = FALSE;
//public static function session_init() {
//	if(self::$session_init_had_run) return;
//	\session_start();
//	self::$session_init_had_run = TRUE;
//}


//function addlog($text){global $config,$pathroot;
//if(substr($config['log file'],-4)!='.txt'){die('error in log file var');}
//$fp=@fopen($pathroot.$config['log file'],'a') or die('failed to write log');
//fwrite($fp,date('Y-m-d H:i:s').' - '.trim($text)."\r\n");
//fclose($fp);
//}


// dump()
if(!\function_exists('dump')) {
	function dump($var) {
		\d($var);
	}
}
// d()
if(!\function_exists('d')) {
	function d($var) {
		echo '<pre style="color: black; background-color: #dfc0c0; padding: 10px;">';
		\var_dump($var);
		echo '</pre>'.\CRLF;
	}
}
// dd()
if(!\function_exists('dd')) {
	function dd($var) {
		\d($var);
		die();
	}
}


// exit functions
function ExitNow($code=NULL) {
	$website = \pxn\phpUtils\portal\Website::peak();
	// set rendered
	if($website !== NULL)
		$website->hasRendered(TRUE);
	// exit code
	if($code !== NULL && Numbers::isNumeric($code))
		exit( ((int)$code) );
	exit(1);
}
function fail($msg) {
	if(!\is_string($msg))
		$msg = \print_r($msg, TRUE);
	echo '<pre style="color: black; background-color: #ffaaaa; '.
		'padding: 10px;"><font size="+2">FATAL: '.$msg.'</font></pre>'.\CRLF;
	if(\psm\debug())
		\backtrace();
	\ExitNow(1);
}
function backtrace() {
	$trace = \debug_backtrace();
	$ignore = array(
		'inc.php' => array(
			'fail',
			'backtrace',
			'autoload',
			'__autoload',
		),
	);
//	$ignore = array();
	foreach($trace as $index => $tr) {
		if(!isset($tr['file'])) continue;
		$file = \basename($tr['file']);
		if(isset($ignore[$file])) {
			$func = $tr['function'];
			if(\in_array($func, $ignore[$file]))
				unset($trace[$index]);
		}
	}
	echo '<table style="background-color: #ffeedd; padding: 10px; '.
		'border-width: 1px; border-style: solid; border-color: #aaaaaa;">'.\CRLF;
	$first = TRUE;
	$evenodd = FALSE;
	foreach($trace as $num => $tr) {
		if(!$first)
			echo '<tr><td height="20">&nbsp;</td></tr>';
		$evenodd = ! $evenodd;
		$bgcolor = ($evenodd ? '#ffe0d0' : '#fff8e8');
		$first = FALSE;
		echo '<tr style="background-color: '.$bgcolor.';">'.\CRLF;
		echo \TAB.'<td><font size="-2">#'.((int) $num).'</font></td>'.\CRLF;
		echo \TAB.'<td>'.@$tr['file'].'</td>'.\CRLF;
		echo '</tr>'.\CRLF;
		echo '<tr style="background-color: '.$bgcolor.';">'.\CRLF;
		echo \TAB.'<td></td>'.\CRLF;
		$args = '';
		foreach($tr['args'] as $arg) {
			if(!empty($args))
				$args .= ', ';
			if(\is_string($arg)) {
				if(\strpos($arg, \CRLF))
					$args .= '<pre>'.$arg.'</pre>';
				else
					$args .= $arg;
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
				'</td>'.\CRLF;
		echo '</tr>'.\CRLF;
	}
	echo '</table>'.\CRLF;
	//dump($trace);
}


}
