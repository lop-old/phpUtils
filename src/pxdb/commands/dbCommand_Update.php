<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2017
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\pxdb\commands;

use pxn\phpUtils\Defines;
use pxn\phpUtils\San;

use pxn\phpUtils\pxdb\dbPool;
use pxn\phpUtils\pxdb\dbTools;


class dbCommand_Update extends \pxn\phpUtils\pxdb\dbCommands {



	/**
	 * return: true if changes made (or need to be made, in dry mode)
	 *         false if no changes need to be made
	 *         null if failure
	 */
	public function execute($pool, $table) {
		$poolName = dbPool::castPoolName($pool);
		$poolName = San::AlphaNumUnderscore($poolName);
		$table    = San::AlphaNumUnderscore($table);
		// get table schema
		$tables = $pool->getUsingTables();
		if (!isset($tables[$table])) {
			fail("Unknown table: {$poolName}:{$table}",
				Defines::EXIT_CODE_INVALID_ARGUMENT);
		}
		$schema = $tables[$table];
		$schemaFields = $schema->getFields();
		$tableExists = $pool->hasTable($table);

		// create new table
		if (!$tableExists) {
			// get first field
			\reset($schemaFields);
			list($fieldName, $field) = \each($schemaFields);
			$field = dbTools::FillFieldKeys_Full($fieldName, $field);
			$dryStr = ($this->dry ? '[DRY] ' : '[ACTION] ');
			$fieldType = $field['type'];
			echo "($dryStr}CREATE TABLE: $table [ $fieldName | $fieldType ]\n";
//TODO: logging
			if ($this->dry) {
				return TRUE;
			} else {
				$pool->CreateTable(
					$table,
					$field
				);
			}
		}

		// check fields
		$existingFields = $pool->getTableFields($table);
		if ($existingFields == NULL) {
			fail("Failed to find table fields! {$poolName}:{$table}",
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		$countAdded = 0;
		$countAlter = 0;
		foreach ($schemaFields as $fieldName => $field) {
			// normalize records
			$existField = NULL;
			if (isset($existingFields[$fieldName])) {
				$existField = dbTools::FillFieldKeys_Simple($fieldName, $existingFields[$fieldName]);
			}
			$schemField = dbTools::FillFieldKeys_Full($fieldName, $field);

			// add field to table
			if (!$pool->hasTableField($table, $fieldName)) {
				$countAdded++;
				$dryStr = ($this->dry ? '[DRY] ' : '[ACTION] ');
				$fieldType = $schemField['type'];
				$fieldSize = $schemField['size'];
				echo "{$dryStr}ADD FIELD: $table [ $fieldName | $fieldType ($fieldSize) ]\n";
//TODO: logging
				if (!$this->dry) {
					$result = $pool->addTableField($table, $schemField);
					if (!$result) {
						fail("Failed to add field to table! $table [ $fieldName | $fieldType ($fieldSize) ]",
							Defines::EXIT_CODE_INTERNAL_ERROR);
					}
				}
				continue;
			}

			// ensure field properties are correct
			$result = dbTools::CheckFieldNeedsChanges($schemField, $existField);
			if ($result !== FALSE) {
				$countAlter++;
				$dryStr = ($this->dry ? '[DRY] ' : '[ACTION] ');
				$existFieldType  = $existField['type'];
				$schemaFieldType = $schemField['type'];
				// alter table
				echo "{$dryStr}ALTER FIELD: $table [ $fieldName | $existFieldType > $schemaFieldType ] -> {$result}\n";
//TODO: logging
				if (!$this->dry) {
					$pool->updateTableField($table, $schemField);
				}
				continue;
			}
		}

		// stats
		if ($countAdded > 0 || $countAlter > 0) {
			echo "\n";
			if ($countAdded > 0) {
				$fieldMultiple = ($countAdded > 1 ? 's' : ' ');
				echo "Added   [ $countAdded ] field{$fieldMultiple} to table: {$table}\n";
			}
			if ($countAlter > 0) {
				$fieldMultiple = ($countAlter > 1 ? 's' : ' ');
				echo "Altered [ $countAlter ] field{$fieldMultiple} in table: {$table}\n";
			}
			echo "\n";
			return TRUE;
		}
		return FALSE;
	}



}
