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
		$dryStr = ($this->dry ? '[DRY]' : '[ACTION]');

		// create new table
		if (!$tableExists) {
			// get first field
			\reset($schemaFields);
			list($fieldName, $field) = \each($schemaFields);
			$field = dbTools::FillFieldKeys_Full($fieldName, $field);
			$fieldType = $field['type'];
			$fieldTypeStr = (
				isset($field['size']) && !empty($field['size'])
				? $fieldType.'|'.$field['size']
				: $fieldType
			);
			echo " {$dryStr} CREATE TABLE: $table [{$fieldTypeStr}]{$fieldName}\n";
//TODO: logging
			$pool->CreateTable(
				$table,
				$field,
				$this->dry
			);
			if ($this->dry) {
				return TRUE;
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
				$schemaFieldType = $schemField['type'];
				$schemaFieldTypeStr = (
					isset($schemField['size']) && !empty($schemField['size'])
					? $schemaFieldType.'|'.$schemField['size']
					: $schemaFieldType
				);
				echo " {$dryStr} ADD FIELD: $table [{$schemaFieldTypeStr}]{$fieldName}\n";
//TODO: logging
				if (!$this->dry) {
					$result = $pool->addTableField($table, $schemField);
					if (!$result) {
						fail("Failed to add field to table! $table [{$schemaFieldTypeStr}]{$fieldName}",
							Defines::EXIT_CODE_INTERNAL_ERROR);
					}
				}
				continue;
			}

			// ensure field properties are correct
			$result = dbTools::CheckFieldNeedsChanges($schemField, $existField);
			if ($result !== FALSE) {
				$countAlter++;
				$existFieldType  = $existField['type'];
				$schemaFieldType = $schemField['type'];
				$existFieldTypeStr = (
					isset($existField['size']) && !empty($existField['size'])
					? $existFieldType.'|'.$existField['size']
					: $existFieldType
				);
				$schemaFieldTypeStr = (
					isset($schemField['size']) && !empty($schemField['size'])
					? $schemaFieldType.'|'.$schemField['size']
					: $schemaFieldType
				);
				// alter table
				echo " {$dryStr} ALTER FIELD: $table [{$existFieldTypeStr}] -> [{$schemaFieldTypeStr}] $fieldName\n";
//TODO: logging
				$pool->updateTableField($table, $schemField, $this->dry);
				continue;
			}
		}

		// stats
		if ($countAdded == 0 && $countAlter == 0) {
			echo "No changes needed for table: $table\n";
			echo "\n";
		} else
		if ($countAdded > 0 || $countAlter > 0) {
			echo "\n";
			if ($countAdded > 0) {
				$fieldMultiple = ($countAdded > 1 ? 's' : ' ');
				echo " {$dryStr} Added   [ $countAdded ] field{$fieldMultiple} to table: {$table}\n";
			}
			if ($countAlter > 0) {
				$fieldMultiple = ($countAlter > 1 ? 's' : ' ');
				echo " {$dryStr} Altered [ $countAlter ] field{$fieldMultiple} in table: {$table}\n";
			}
			echo "\n";
			return TRUE;
		}
		return FALSE;
	}



}
