<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\examples\console;

use pxn\phpUtils\console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class Router implements \pxn\phpUtils\console\Router {

	protected static $instance = NULL;



	public static function get() {
		if (self::$instance == NULL) {
			self::$instance = new static();
		}
		return self::$instance;
	}
	protected function __construct() {
		// example commands
		commands\Random::get();
		commands\Sequential::get();
		// inline example command
		$command = Command::RegisterNew(
			'inline',
			function(InputInterface $input, OutputInterface $output) {
				echo "\n\n";
				echo 'Running Command: INLINE'."\n";
				echo "\n\n";
			}
		);
		$command->setInfo(
			'Example command calls inline callable',
			'HELP!',
			'USAGE?'
		);
		// method command
		$command = Command::RegisterNew(
			'method',
			[ $this, 'runCommand' ]
		);
		$command->setInfo(
			'Example command runs a method',
			'HELP!',
			'USAGE?'
		);
	}



	public function runCommand(InputInterface $input, OutputInterface $output) {
		echo "\n\n";
		echo 'Running Command: METHOD'."\n";
		echo "\n\n";
	}



}
