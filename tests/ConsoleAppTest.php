<?php
/*
 * PoiXson phpUtils - Utilities for PoiXson PHP projects
 *
 * @copyright 2004-2015
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\tests;

use pxn\phpUtils\Console\ConsoleFactory;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @coversDefaultClass \pxn\phpUtils\ConsoleApp
 */
class ConsoleAppTest extends \PHPUnit_Framework_TestCase {



	/**
	 * @covers \pxn\phpUtils\ConsoleAppFactory::get
	 * @covers ::__construct
	 */
	public function testInstances() {
		$a = ConsoleFactory::get();
		$b = ConsoleFactory::get();
		$this->assertTrue($a === $b);
	}



	/**
	 * @covers ::__construct
	 * @covers ::getDefaultCommands
	 * @covers ::newCommand
	 */
	public function testCommands() {
		$console = ConsoleFactory::get();
		$this->assertNotNull($console);
		// get commands
		$expected = [
				'help'
		];
		$commands = $console->all();
		$this->assertEquals(
				\print_r($expected, TRUE),
				\print_r(\array_keys($commands), TRUE)
		);
		// add a test command
		$command = $console->newCommand(
				'test-command',
				function(ArgvInput $input, ConsoleOutput $output) {
					echo 'This is a test command.';
				}
		);
		$command
			->setAliases    (['testcommand'])
			->setDescription('Description of a test command')
			->setHelp       ('This is help for a test command')
			->addUsage      ('Usage for a test command');
		// verify commands exist
		$expected = [
				'help',
				'test-command'
		];
		$commands = $console->all();
		$this->assertEquals(
				\print_r($expected, TRUE),
				\print_r(\array_keys($commands), TRUE)
		);
	}



}
