<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\tests\console;

use pxn\phpUtils\console\commands\CommandFactory;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CommandTest extends \pxn\phpUtils\console\Command {

	private static $instance = NULL;

	public $hasRun = FALSE;



	public static function get() {
		if(self::$instance == NULL) {
			self::$instance = self::RegisterNew('test-command-class');
			self::$instance->setAliases(['test-a']);
			self::$instance->setInfo(
					'Example command A',
					'HELP!'
			);
		}
		return self::$instance;
	}



	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->hasRun = TRUE;
	}



}
