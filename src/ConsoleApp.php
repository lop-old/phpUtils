<?php
/**
 * PoiXson phpUtils
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



	protected function getDefaultCommands() {
		return [
				new HelpCommand()
		];
	}



}
