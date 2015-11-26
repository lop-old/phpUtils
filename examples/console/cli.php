<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 *
 * @copyright 2004-2015
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\examples\Console;

require_once(__DIR__.'/../../vendor/autoload.php');

use pxn\phpUtils\Console\ConsoleFactory;


$console = ConsoleFactory::get();
$router  = Router::get();

$console->run();
