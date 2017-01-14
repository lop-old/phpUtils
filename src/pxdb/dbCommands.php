<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2017
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\pxdb;

use pxn\phpUtils\San;
use pxn\phpUtils\ShellTools;
use pxn\phpUtils\System;
use pxn\phpUtils\Defines;


abstract class dbCommands {

	protected $dry = NULL;



	public static function run() {
		System::RequireShell();

		// empty command argument
		$cmd = ShellTools::getArg(1);
		$cmd = \mb_strtolower($cmd);
		if (empty($cmd)) {
			self::DisplayHelp();
			ExitNow(Defines::EXIT_CODE_GENERAL);
		}
		// -h or --help
		if (ShellTools::isHelp()) {
			self::DisplayHelp($cmd);
			ExitNow(Defines::EXIT_CODE_GENERAL);
		}

		// is dry run?
		{
			$dry = ShellTools::getFlagBool('-D', '--dry');
			// default dry run
			if ($dry === NULL) {
				// commands which modify
				if ($cmd == 'update' || $cmd == 'import') {
					$dry = TRUE;
				} else {
					$dry = FALSE;
				}
			}
			// confirmed, not dry run
			$confirm = ShellTools::getFlagBool('--confirm');
			if ($confirm != FALSE) {
				$dry = FALSE;
			}
		}

		// list / update / import / export

		// --pool/--table flags
		{
			$poolFlag  = ShellTools::getFlag('-p', '--pool');
			$tableFlag = ShellTools::getFlag('-t', '--table');
			if (!empty($poolFlag) || !empty($tableFlag)) {
				if (empty($poolFlag) || $poolFlag == '*' || \mb_strtolower($poolFlag) == 'all') {
					$poolFlag = '*';
				} else {
					$poolFlag = San::AlphaNumUnderscore($poolFlag);
					if (empty($poolFlag)) {
						fail('Invalid pool name provided!');
						ExitNow(Defines::EXIT_CODE_INVALID_ARGUMENT);
					}
				}
				if (empty($tableFlag) || $tableFlag == '*' || \mb_strtolower($tableFlag) == 'all') {
					$tableFlag = '*';
				} else {
					$tableFlag = San::AlphaNumUnderscore($tableFlag);
					if (empty($tableFlag)) {
						fail('Invalid table name provided!');
						ExitNow(Defines::EXIT_CODE_INVALID_ARGUMENT);
					}
				}
				// perform the command
				$result = self::runCommand(
					$cmd,
					$poolFlag,
					$tableFlag,
					$dry
				);
				return $result;
			}
		}

		// pool:table arguments
		{
			$args = ShellTools::getArgs();
			\array_shift($args);
			\array_shift($args);
			if (\count($args) > 0) {
				// split pool:table arguments
				$entries = self::splitPoolTable($args);
				// perform the command
				$count = 0;
				foreach ($entries as $entry) {
					$result = self::runCommand(
						$cmd,
						$entry['pool'],
						$entry['table'],
						$dry
					);
					if ($result != TRUE) {
						return FALSE;
					}
					$count++;
				}
				if ($count > 0) {
					return TRUE;
				}
			}
		}

		// list all
		if ($cmd == 'list') {
			$result = self::runCommand(
				$cmd,
				'*',
				'*',
				$dry
			);
			return $result;
		}

		// command not handled
		self::DisplayHelp($cmd);
		ExitNow(Defines::EXIT_CODE_INVALID_COMMAND);
	}
	public static function runCommand($cmd, $pool, $table, $dry) {
		echo "\n";
		// all pools and tables
		if ($pool == '*' && $table == '*') {
			echo " Cmd: $cmd  Pool: -all-  Table: -all-\n\n";
			$pools = dbPool::getPools();
			$count = 0;
			foreach ($pools as $poolEntryName => $poolEntry) {
				$tables = $poolEntry->getUsingTables();
				foreach ($tables as $tableEntryName => $tableEntry) {
					$result = self::runCommandOnce(
						$cmd,
						$poolEntry,
						$tableEntryName,
						$dry
					);
					if ($result != TRUE) {
						echo "\n Ran $cmd on $count tables, then failed!";
						return FALSE;
					}
					$count++;
				}
			}
			if ($count > 0) {
				echo "\n Ran $cmd on $count tables";
				return TRUE;
			}
		} else
		// all pools
		if ($pool == '*') {
			echo " Cmd: $cmd  Pool: -all-  Table: $table\n\n";
			$pools = dbPool::getPools();
			$count = 0;
			foreach ($pools as $poolEntryName => $poolEntry) {
				if ($poolEntry->hasTable($table)) {
					$result = self::runCommandOnce(
						$cmd,
						$poolEntry,
						$table,
						$dry
					);
					if ($result != TRUE) {
						return FALSE;
					}
					$count++;
					continue;
				}
			}
			if ($count > 0) {
				echo "\n Ran $cmd on $count tables";
				return TRUE;
			}
		} else
		// all tables
		if ($table == '*') {
			$poolName = dbPool::castPoolName($pool);
			echo " Cmd: $cmd  Pool: $poolName  Table: -all-\n\n";
			$poolEntry = dbPool::getPool($pool);
			if ($poolEntry == NULL) {
				fail('Invalid pool!');
				ExitNow(Defines::EXIT_CODE_INVALID_ARGUMENT);
			}
			$tables = $poolEntry->getUsingTables();
			$count = 0;
			foreach ($tables as $tableEntryName => $tableEntry) {
				$result = self::runCommandOnce(
					$cmd,
					$poolEntry,
					$tableEntryName,
					$dry
				);
				if ($result != TRUE) {
					return FALSE;
				}
				$count++;
			}
			if ($count > 0) {
				echo "\n Ran $cmd on $count tables";
				return TRUE;
			}
		// one pool/table
		} else {
			$poolName = dbPool::castPoolName($pool);
			echo " Cmd: $cmd  Pool: $poolName  Table: $table\n\n";
			$result = self::runCommandOnce(
				$cmd,
				$pool,
				$table,
				$dry
			);
			return $result;
		}
		return FALSE;
	}
	public static function runCommandOnce($cmd, $pool, $table, $dry) {
		$poolName = dbPool::castPoolName($pool);
		$pool = dbPool::getPool($pool);
		if ($pool == NULL) {
			fail("Failed to find db pool: $poolName"); ExitNow(Defines::EXIT_CODE_INVALID_ARGUMENT);
		}
		$cmdObj = NULL;
		switch ($cmd) {
		// list pools/tables
		case 'list':
			$cmdObj = new dbCommand_List($dry);
			break;
		// update db schema
		case 'update':
			$cmdObj = new dbCommand_Update($dry);
			break;
		// import db tables
		case 'import':
			$cmdObj = new dbCommand_Import($dry);
			break;
		// export db tables
		case 'export':
			$cmdObj = new dbCommand_Export($dry);
			break;
		default:
			self::DisplayHelp();
			ExitNow(Defines::EXIT_CODE_INVALID_COMMAND);
		}
		$result = $cmdObj->execute(
			$pool,
			$table
		);
		return $result;
	}
	public abstract function execute($pool, $table);



	public function __construct($dry) {
		System::RequireShell();
		$this->dry = $dry;
	}



	private static function splitPoolTable($args) {
		$entries = [];
		foreach ($args as $arg) {
			$poolName  = NULL;
			$tableName = NULL;
			// split pool:table
			if (\strpos($arg, ':') === FALSE) {
				$tableName = $arg;
			} else {
				$array = \explode(':', $arg, 2);
				$poolName  = $array[0];
				$tableName = $array[1];
			}
			// parse table name
			if (empty($tableName) || $tableName == '*' || \mb_strtolower($tableName) == 'all') {
				$tableName = '*';
			} else {
				$tableName = San::AlphaNumUnderscore($tableName);
				if (empty($tableName)) {
					fail('Invalid table name provided!');
					ExitNow(Defines::EXIT_CODE_INVALID_ARGUMENT);
				}
			}
			// default pool name
			if (empty($poolName)) {
				if ($tableName == '*') {
					$poolName = '*';
				} else {
					$poolName = dbPool::dbNameDefault;
				}
			}
			// parse pool name
			if ($poolName == '*' || \mb_strtolower($poolName) == 'all') {
				$poolName = '*';
			} else {
				$poolName = San::AlphaNumUnderscore($poolName);
				if (empty($poolName)) {
					fail('Invalid pool name provided!');
					ExitNow(Defines::EXIT_CODE_INVALID_ARGUMENT);
				}
			}
			// build entry
			$entries["$poolName:$tableName"] = [
				'pool'  => $poolName,
				'table' => $tableName
			];
		}
		return $entries;
	}



	public static function DisplayHelp($cmd=NULL) {
		echo "\n";
		echo "Usage:\n";
		switch ($cmd) {
		case 'update':
			echo "  db update [options] [[pool:]table] ..\n";
			break;
		case 'import':
			echo "  db import [options] <filename>\n";
			break;
		case 'export':
			echo "  db export [options] <filename> [[pool:]table] ..\n";
			break;
		default:
			echo "  db <command> [options]\n";
			break;
		}
		echo "\n";
		echo "Commands:\n";
		echo "  list    List the existing database pools/tables\n";
		echo "  update  Update the database tables to the current schema, and create tables as needed.\n";
		echo "  import  Import data from a stored backup.\n";
		echo "  export  Export data to a backup stored in the filesystem.\n";
		echo "\n";
		echo "Options:\n";
		echo "  -D, --dry    Run the operation without making changes. (default for some operations)\n";
		if ($cmd == 'update' || $cmd == 'import') {
			echo "  --confirm    Confirm the changes to be made (overrides the --dry flag)\n";
		}
		echo "  -p, --pool   Database pool name to use for the operation.\n";
		echo "  -t, --table  Name of the table to use for the operation.\n";
		if ($cmd == 'import') {
			echo "\n";
			echo "  -f, --file   The filename or path to restore data from.\n";
		}
		if ($cmd == 'export') {
			echo "  -f, --file   The filename or path to write the exported data.\n";
		}
		echo "\n";
	}



}
