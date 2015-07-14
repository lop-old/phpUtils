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

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\HelpCommand;

class ConsoleApp extends Application {



	public function __construct($name='UNKNOWN', $version='UNKNOWN') {
		parent::__construct($name, $version);
		$this->setDefaultCommand('help');
	}



	protected function getDefaultCommands() {
		return [
				new HelpCommand()
		];
	}



}
