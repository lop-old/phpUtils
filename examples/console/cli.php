<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\examples\console;

require_once(__DIR__.'/../../vendor/autoload.php');

use pxn\phpUtils\console\ConsoleFactory;


$console = ConsoleFactory::get();
$router  = Router::get();

$console->run();
