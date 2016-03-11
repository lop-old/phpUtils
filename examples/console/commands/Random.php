<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\examples\console\commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class Random extends \pxn\phpUtils\console\Command {

	private static $instance = NULL;



	public static function get() {
		if(self::$instance == NULL) {
			$command = self::RegisterNew('random');
			$command->setAliases(['rand']);
			$command->setInfo(
				'Example command displays ramdom numbers',
				'HELP!',
				'USAGE?'
			);
			self::$instance = $command;
		}
		return self::$instance;
	}

//		$this
//			->setDefinition([
//				new InputArgument(
//						'command_name',
//						InputArgument::OPTIONAL,
//						'The command name',
//						'help'
//				),
//				new InputOption(
//						'format',
//						NULL,
//						InputOption::VALUE_REQUIRED,
//						'The output format (txt, xml, json, or md)',
//						'txt'
//				),
//			]
//		)
//		->setHelp(<<<EOF
//The <info>%command.name%</info> example command displays ramdom numbers:
//
//  <info>php %command.full_name%</info>
//EOF
//		);
//	}



	protected function execute(InputInterface $input, OutputInterface $output) {
		echo "\n\n";
		echo "Running Command: RANDOM\n";
		for ($i=1; $i<=5; $i++) {
			echo '  '.\mt_rand(0, 9);
		}
		echo "\n\n";

//		if($this->command == NULL) {
//			$this->command = $this->getApplication()
//			->find($input->getArgument('command_name'));
//		}
//		$helper = new DescriptorHelper();
//		$helper->describe(
//			$output,
//			$this->command,
//			[
//				'format'   => $input->getOption('format'),
//			]
//		);
//		$this->command = NULL;
	}



}
