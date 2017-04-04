<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\tests\console;

use pxn\phpUtils\console\ConsoleFactory;
use pxn\phpUtils\console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArgvInput;


/**
 * @coversDefaultClass \pxn\phpUtils\console\ConsoleApp
 */
class ConsoleAppTest extends \PHPUnit\Framework\TestCase {

	private $ranCommandB = FALSE;
	private $ranCommandC = FALSE;



	/**
	 * @covers \pxn\phpUtils\console\ConsoleAppFactory::get
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
		$console->setAutoExit(FALSE);
		$this->assertNotNull($console);
		// default commands
		$commands = $console->all();
		$this->assertEquals(
			\print_r([ 'help', 'list' ], TRUE),
			\print_r( \array_keys($commands), TRUE)
		);
		// class command
		$commandA = CommandTest::get();
		// inline command
		$commandB = Command::RegisterNew(
			'test-command-inline',
			function(InputInterface $input, OutputInterface $output) {
				$this->ranCommandB = TRUE;
			}
		);
		$commandB->setAliases(['test-b']);
		$commandB->setInfo(
				'Example command B',
				'HELP!'
		);
		// method command
		$commandC = Command::RegisterNew(
				'test-command-method',
				[ $this, 'runCommand' ]
		);
		$commandC->setAliases(['test-c']);
		$commandC->setInfo(
				'Example command C',
				'HELP!'
		);
		// verify commands exist
		$commands = $console->all();
		$this->assertEquals(
			\print_r([
				'help',
				'list',
				'test-command-class',
				'test-command-inline',
				'test-command-method'
			], TRUE),
			\print_r(\array_keys($commands), TRUE)
		);
		// run test commands
		$this->assertFalse($commandA->hasRun,  'Invalid state for command A');
		$this->assertFalse($this->ranCommandB, 'Invalid state for command B');
		$this->assertFalse($this->ranCommandC, 'Invalid state for command C');
		// run command A
		$console->run(new ArgvInput(['', 'test-command-class']));
		$this->assertTrue ($commandA->hasRun,  'Failed to run command A');
		$this->assertFalse($this->ranCommandB, 'Invalid state for command B');
		$this->assertFalse($this->ranCommandC, 'Invalid state for command C');

		// run command B
		$console->run(new ArgvInput(['', 'test-command-inline']));
		$this->assertTrue ($commandA->hasRun,  'Failed to run command A');
		$this->assertTrue ($this->ranCommandB, 'Failed to run command B');
		$this->assertFALSE($this->ranCommandC, 'Invalid state for command C');
		// run command C
		$console->run(new ArgvInput(['', 'test-command-method']));
		$this->assertTrue($commandA->hasRun,  'Failed to run command A');
		$this->assertTrue($this->ranCommandB, 'Failed to run command B');
		$this->assertTrue($this->ranCommandC, 'Failed to run command C');
	}



	public function runCommand() {
		$this->ranCommandC = TRUE;
	}



}
