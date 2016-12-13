<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\pxdb;


final class dbCommands {



	public static function run() {

		// export db
		if (ShellTools::getFlagBool('-e', '--export')) {
			if (ShellTools::isHelp()) {
				self::DisplayHelp_export();
				return NULL;
			}
//TODO:
			dbUtils::doExport();
			return TRUE;
		}

		// import db
		if (ShellTools::getFlagBool('-i', '--import') {
			if (ShellTools::isHelp()) {
				self::DisplayHelp_import();
				return NULL;
			}
//TODO:
			dbUtils::doImport();
			return TRUE;
		}

		if (ShellTools::isHelp()) {
			self::DisplayHelp();
			return NULL;
		}
		return FALSE;
	}



	public static function DisplayHelp() {
//TODO:
//		$help = new ShellHelp('', 'b', 'c');
//		$help->addFlag('import-db', 'Database tools to import MySQL tables.');
//		$help->addFlag('export-db', 'Database tools to export MySQL tables.');
//		$help->Display();
		ExitNow(1);
	}
	public static function DisplayHelp_export() {
//TODO:
		ExitNow(1);
	}
	public static function DisplayHelp_import() {
//TODO:
		ExitNow(1);
	}



}
