<?php
/*
 * PoiXson phpUtils - Utilities for PoiXson PHP projects
 *
 * @copyright 2004-2015
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\Console;

use pxn\phpUtils\Console\Commands\Help as HelpCommand;
use pxn\phpUtils\ComposerTools;

class ConsoleApp extends \Symfony\Component\Console\Application {

	protected $composer = NULL;



	public function __construct($name=NULL, $version=NULL) {
		if(empty($name)) {
			$name = $this->getComposer()
					->getName();
		}
		if(empty($version)) {
			$version = $this->getComposer()
					->getVersion();
		}
		parent::__construct($name, $version);
		$this->setDefaultCommand('help');
	}



	public function getComposer() {
		if($this->composer == NULL)
			$this->composer = ComposerTools::find(2);
		if($this->composer == NULL)
			throw new FileNotFoundException('composer.json file not found');
		return $this->composer;
	}




	/**
	 * @codeCoverageIgnoreStart
	 */
	protected function getDefaultCommands() {
		return [
				new HelpCommand()
		];
	}
	/**
	 * @codeCoverageIgnoreEnd
	 */



	public function newCommand($name, Callable $callback) {
		if(empty($name))      throw new \Exception('Command name argument is required');
		if($callback == NULL) throw new \Exception('Callback argument is required');
		$command = new Command($name);
		$command->setCode($callback);
		$this->add($command);
		return $command;
	}



}
