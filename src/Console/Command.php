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

class Command extends \Symfony\Component\Console\Command\Command {



	public static function NewCommand($name, $callback=NULL) {
		if(empty($name)) throw new \Exception('Command name argument is required');
		// build the command
		$command = new static($name);
		if($callback != NULL)
			$command->setCode($callback);
		return $command;
	}
	public static function RegisterNew($name, $callback=NULL) {
		$command = self::NewCommand($name, $callback);
		// register the command
		$console = ConsoleFactory::get();
		$console->add($command);
		return $command;
	}



	public function setInfo($desc=NULL, $help=NULL) {
		if(!empty($desc))
			$this->setDescription($desc);
		if(!empty($help))
			$this->setHelp($help);
		//TODO:
		//->addUsage
	}



}
