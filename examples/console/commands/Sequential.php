<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\examples\console\commands;

use pxn\phpUtils\console\commands\CommandFactory;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class Sequential extends \pxn\phpUtils\console\Command {

	private static $instance = NULL;



	public static function get() {
		if (self::$instance == NULL) {
			$command = self::RegisterNew('sequential');
			$command->setAliases(['seq']);
			$command->setInfo(
					'Example command displays ramdom numbers',
					'HELP!',
					'USAGE?'
			);
			self::$instance = $command;
		}
		return self::$instance;
	}



	protected function execute(InputInterface $input, OutputInterface $output) {
		echo "\n\n";
		echo "Running Command: SEQ\n";
		for ($i=1; $i<=5; $i++) {
			echo '  '.$i;
		}
		echo "\n\n";
	}



}
