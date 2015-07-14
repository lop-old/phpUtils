<?php
/**
 * PoiXson phpUtils
 *
 * @copyright 2004-2015
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\tests;

use pxn\phpUtils\ConsoleApp;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @coversDefaultClass \pxn\phpUtils\ConsoleApp
 */
class ConsoleAppTest extends \PHPUnit_Framework_TestCase {



	/**
	 * @covers ::__construct
	 * @covers ::getDefaultCommands
	 */
	public function testDefaultCommands() {
		$console = new ConsoleApp();
		$console->add(
				$this->getTestCommand()
		);
		$commands = $console->all();
		$expected = [
				'help',
				'test-command',
				'testcommand'
		];
		$this->assertEquals(
				\print_r($expected, TRUE),
				\print_r(\array_keys($commands), TRUE)
		);
	}



	protected function getTestCommand() {
		return (new Command('test-command'))
				->setAliases(['testcommand'])
				->setDescription('This command is for testing only')
				->setHelp('This is help for a test command')
				->addUsage('Usage for a test command')
				->setDefinition([])
				->setCode(function(ArgvInput $input, ConsoleOutput $output) {
					echo 'This is a test command.';
				}
		);
	}



}
