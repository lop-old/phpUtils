<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\pxdb;

use pxn\phpUtils\San;
use pxn\phpUtils\Strings;
use pxn\phpUtils\System;

use pxn\phpPortal\Website;


final class dbTools {
	private function __construct() {}



	public static function UpdateTables($pool=NULL, $tables=NULL) {
		if ($pool == NULL) {
			fail('pool argument is required!');
			exit(1);
		}
		$isShell = System::isShell();
		// default to all tables in use
		if (empty($tables)) {
			$tables = $pool->getUsingTables();
		}
		if ($isShell) {
			echo "\n\n == Creating/Updating DB Tables..\n";
		}
		// multiple tables
		if (\is_array($tables)) {
			$countTables = 0;
			foreach ($tables as $tableName) {
				if (empty($tableName)) {
					continue;
				}
				if (Strings::StartsWith($tableName, '_')) {
					continue;
				}
				$hasCreated = self::doUpdateTable($pool, $tableName);
				if ($hasCreated) {
					$countTables++;
				}
			}
			if ($isShell && $countTables > 0) {
				echo "Created [ {$countTables} ] tables.\n";
			}
			return TRUE;
		}
		//single table
		return self::doUpdateTable($pool, (string)$tables);
	}
	protected static function doUpdateTable($pool, $tableName) {
		$tableName = San::AlphaNumUnderscore($tableName);
		if (empty($tableName)) {
			fail('Table argument is required!');
			exit(1);
		}
		if (Strings::StartsWith($tableName, '_')) {
			fail("Cannot use tables starting with an underscore: {$tableName}");
			exit(1);
		}
		// find table schema
		$schema = self::getTableSchema($tableName);
		$schemaFields = $schema->getFields();

		// create new table
		$createdTable = FALSE;
		if (!$pool->hasTable($tableName)) {
			// get first fields
			\reset($schemaFields);
			list($fieldName, $field) = \each($schemaFields);
			// ensure has name key
			if (!isset($field['name']) || empty($field['name'])) {
				$field = \array_merge(['name' => $fieldName], $field);
			}
			$pool->CreateTable(
				$tableName,
				$field
			);
			$createdTable = TRUE;
		}

		// check fields
		$tableFields = $pool->getTableFields($tableName);
		$countAdded   = 0;
		$countAltered = 0;
		foreach ($schemaFields as $fieldName => $schField) {
			self::fillFieldKeys($schField, $fieldName);
			// ensure field exists
			if (!$pool->hasTableField($tableName, $fieldName)) {
				// add field to table
				$result = $pool->addTableField($tableName, $schField);
				if ($result) {
					$countAdded++;
					continue;
				}
			}
			// ensure field properties are correct
			$tabField = $tableFields[$fieldName];
			self::fillFieldKeys($tabField, $fieldName);
			$result = self::fieldNeedsChanges($schField, $tabField);
			if ($result !== FALSE) {
				$schFieldType = $schField['type'];
				echo "\nTable/Field [ {$tableName}::{$fieldName}({$schFieldType}) ] needs changes:  {$result}\n";
				// alter table
				$pool->updateTableField($tableName, $schField);
				$countAltered++;
			}
		}

		// stats
		if (System::isShell() && ($countAdded > 0 || $countAltered > 0)) {
			if ($countAdded > 0) {
				echo "\nAdded [ {$countAdded} ] fields to table: {$tableName}\n";
			}
			if ($countAltered > 0) {
				echo "\nAltered [ {$countAltered} ] fields in table: {$tableName}\n";
			}
			echo "\n";
		}
		// done
		return $createdTable;
	}
	protected static function fieldNeedsChanges(array $schField, array $tabField) {
		if ($schField == NULL || \count($schField) == 0) {
			fail('schField argument is required!');
			exit(1);
		}
		if ($tabField == NULL || \count($tabField) == 0) {
			fail('tabField argument is required!');
			exit(1);
		}
		$fieldType = \mb_strtolower($schField['type']);
		$changes = [];

		// check field type
		if ($fieldType == 'increment') {
			if (\mb_strtolower($tabField['type']) !== 'int'
			&& \mb_strtolower($tabField['type']) !== 'increment') {
				$tabType = (string) $tabField['type'];
				$schType = (string) $schField['type'];
				return "type({$tabType}|{$schType})";
			}
		} else
		if (\mb_strtolower($tabField['type']) !== $fieldType) {
				$tabType = (string) $tabField['type'];
				$schType = (string) $schField['type'];
				return "type({$tabType}|{$schType})";
		}

		// check properties based on field type
		switch ($fieldType) {
		// auto-increment
		case 'increment':
			$tabSize = (string) $tabField['size'];
			if ($tabSize != '11' ) {
				$changes[] = "size({$tabSize}|11)";
			}
			if ($tabField['default'] != NULL) {
				$tabDefault = (string) $tabField['default'];
				$changes[] = "default({$tabDefault}|NULL)";
			}
			if ($tabField['increment'] !== TRUE) {
				$changes[] = 'auto-increment';
			}
			if ($tabField['primary'] !== TRUE) {
				$changes[] = 'primary-key';
			}
			if ($tabField['nullable'] === TRUE) {
				$changes[] = 'nullable(YES|NOT)';
			}
			break;
		// length size
		case 'int':       case 'tinyint': case 'smallint':
		case 'mediumint': case 'bigint':
		case 'decimal':   case 'double':  case 'float':
		case 'bit':       case 'char':
		case 'boolean':   case 'bool':
		case 'varchar':
		case 'enum': case 'set':
			$tabSize = (string) $tabField['size'];
			$schSize = (string) $schField['size'];
			if ($tabSize != $schSize) {
				$changes[] = "size({$tabSize}|{$schSize})";
			}
		// no size
		case 'text': case 'longtext': case 'blob':
		case 'date': case 'time':     case 'datetime':
			$tabDefault = (string) $tabField['default'];
			$schDefault = (string) $schField['default'];
			if ($tabDefault != $schDefault) {
				$changes[] = "default({$tabDefault}|{$schDefault})";
			}
			if ($tabField['nullable'] !== $schField['nullable']) {
				$n1 = ($tabField['nullable'] ? 'YES' : 'NOT');
				$n2 = ($schField['nullable'] ? 'YES' : 'NOT');
				$changes[] = "nullable({$n1}|{$n2})";
			}
			break;
		default:
			$fieldName = $schField['name'];
			fail("Unsupported field type: {$fieldType} - {$fieldName}");
			exit(1);
		}
		if (\count($changes) == 0) {
			return FALSE;
		}
		return \implode(', ', $changes);
	}



	public static function fillFieldKeys(array &$field, $fieldName=NULL) {
		if ($field == NULL || \count($field) == 0) {
			return NULL;
		}
		// field name
		if (!isset($field['name']) || empty($field['name'])) {
			if (empty($fieldName)) {
				fail('Unknown field name!');
				exit(1);
			}
			$field['name'] = $fieldName;
		}
		$fieldName = $field['name'];
		// field type
		if (!isset($field['type']) || empty($field['type'])) {
			fail("Unknown type for field: {$fieldName}");
			exit(1);
		}
		$fieldType = \mb_strtolower($field['type']);

		// size
		if (!isset($field['size']) || empty($field['size'])) {
			switch ($fieldType) {
			case 'int': case 'increment':
				$field['size'] = 11;
				break;
			case 'tinyint':
				$field['size'] = 4;
				break;
			case 'smallint':
				$field['size'] = 6;
				break;
			case 'mediumint':
				$field['size'] = 8;
				break;
			case 'bigint':
				$field['size'] = 20;
				break;
			case 'decimal': case 'double':
				$field['size'] = '16,4';
				break;
			case 'float':
				$field['size'] = '10,2';
				break;
			case 'bit':     case 'char':
			case 'boolean': case 'bool':
				$field['size'] = 1;
				break;
			case 'varchar':
				$field['size'] = 255;
			case 'enum': case 'set':
				$field['size'] = "''";
				break;
			case 'text': case 'longtext': case 'blob':
			case 'date': case 'time':     case 'datetime':
				break;
			default:
				fail("Unknown size for field: {$fieldType} - {$fieldName}");
				exit(1);
			}
		}

		// nullable
		if (isset($field['default']) && $field['default'] === NULL) {
			if (!isset($field['nullable'])) {
				$field['nullable'] = TRUE;
			}
		} else {
			switch ($fieldType) {
			case 'increment':
			case 'int':       case 'tinyint':  case 'smallint':
			case 'mediumint': case 'bigint':
			case 'decimal':   case 'float':    case 'double':
			case 'bit':       case 'boolean':  case 'bool':
			case 'text':      case 'longtext': case 'blob':
			case 'date':      case 'time':     case 'datetime':
				if (!isset($field['nullable'])) {
					$field['nullable'] = FALSE;
				}
				break;
			case 'varchar': case 'char':
			case 'enum':    case 'set':
				if (!isset($field['nullable'])) {
					$field['nullable'] = TRUE;
				}
				break;
			default:
				fail("Unsupported field type: {$fieldName}({$fieldType})");
				exit(1);
			}
		}

		// default value
		if ($field['nullable'] === TRUE) {
			if (!isset($field['default'])) {
				$field['default'] = NULL;
			}
		} else {
			switch ($fieldType) {
			case 'increment':
			case 'varchar': case 'char':
			case 'enum':    case 'set':
			case 'blob':
				if (!isset($field['default'])) {
					$field['default'] = NULL;
				}
				break;
			case 'int':       case 'tinyint': case 'smallint':
			case 'mediumint': case 'bigint':
			case 'bit':       case 'boolean': case 'bool':
				if (!isset($field['default'])) {
					$field['default'] = (
						$field['nullable']
						? NULL
						: 0
					);
				}
				break;
			case 'decimal': case 'float': case 'double':
				if (!isset($field['default'])) {
					$field['default'] = 0.0;
				}
				break;
			case 'text': case 'longtext':
				if (!isset($field['default'])) {
					$field['default'] = '';
				}
				break;
			case 'date':
				if (!isset($field['default'])) {
					$field['default'] = '0000-00-00';
				}
				break;
			case 'time':
				if (!isset($field['default'])) {
					$field['default'] = '00:00:00';
				}
				break;
			case 'datetime':
				if (!isset($field['default'])) {
					$field['default'] = '0000-00-00 00:00:00';
				}
				break;
			default:
				fail("Unsupported field type: {$fieldName}({$fieldType})");
				exit(1);
			}
		}
	}



	public static function getTableSchema($tableName) {
		$tableName = San::AlphaNumUnderscore($tableName);
		if (empty($tableName)) {
			fail('Table name argument is required!');
			exit(1);
		}
		$namespaces = [];
		// if website project (not shell)
		if (\class_exists('pxn\\phpPortal\\Website')) {
			$namespaces[] = Website::getSiteNamespace().'\\schemas';
			$namespaces[] = Website::getPortalNamespace().'\\schemas';
		}
		$namespaces[] = '\\pxn\\phpUtils\\schemas';
		// find table class
		$clss = NULL;
		foreach ($namespaces as $space) {
			$clss = "{$space}\\table_{$tableName}";
			if (\class_exists($clss)) {
				break;
			}
			$clss = '';
		}
		if (empty($clss)) {
			fail("Failed to find table schema class: $tableName");
			exit(1);
		}
		$schema = new $clss();
		return $schema;
	}



}
