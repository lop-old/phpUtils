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

use pxn\phpUtils\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class Router implements \pxn\phpUtils\Console\Router {

	protected static $instance = NULL;



	public static function get() {
		if(self::$instance == NULL)
			self::$instance = new static();
		return self::$instance;
	}
	protected function __construct() {
		// example commands
		Commands\Random::get();
		Commands\Sequential::get();
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
