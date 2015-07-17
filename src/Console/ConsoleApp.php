<?php
/*
 * PoiXson phpUtils - Utilities for PoiXson PHP projects
 *
 * @copyright 2004-2015
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;

class ConsoleApp extends \Symfony\Component\Console\Application {



	public function __construct($name=NULL, $version=NULL) {
		if(empty($name)) {
			$name = ComposerTools::get()
					->getName();
		}
		if(empty($version)) {
			$version = ComposerTools::get()
					->getVersion();
		}
		parent::__construct($name, $version);
		$this->setDefaultCommand('help');
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
