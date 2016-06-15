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


final class dbUtils {
	private function __construct() {}



	public static function UpdateTables($pool=NULL, $tables=NULL) {
		if ($pool == NULL) {
			fail('pool argument is required!');
			exit(1);
		}
		$isShell = System::isShell();
		// default to all tables in use
		if (empty($tables)) {
			$tables = $pool->getUsedTables();
		}
				if ($isShell) {
			echo "\n == Creating/Updating DB Tables..\n";
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
			if ($isShell) {
				echo "\nCreated [ {$countTables} ] tables.\n";
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
		$fields = $schema->getFields();

		// create new table
		$createdTable = FALSE;
		if (!$pool->hasTable($tableName)) {
			// get first fields
			\reset($fields);
			list($fieldName, $field) = \each($fields);
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
		$db = NULL;
		$countFields = 0;
		foreach ($fields as $fieldName => $field) {
			// ensure has name key
			if (!isset($field['name']) || empty($field['name'])) {
				$field = \array_merge(['name' => $fieldName], $field);
			}
			// add missing field
			if (!$pool->hasTableField($tableName, $fieldName)) {
				if ($pool->addTableField($tableName, $field)) {
					$countFields++;
				}
			}
		}
		if (System::isShell() && $countFields > 0) {
			echo "\nAdded [ {$countFields} ] fields to table: {$tableName}\n";
		}

		// done
		if ($db != NULL) {
			$db->release();
		}
		return $createdTable;
	}




	public static function getTableSchema($tableName) {
		$tableName = San::AlphaNumUnderscore($tableName);
		if (empty($tableName)) {
			fail('Table name argument is required!');
			exit(1);
		}
		$namespaces = [];
		// if website project, not shell
		if (\class_exists('pxn\\phpPortal\\Website')) {
			$namespaces[] = Website::getSiteNamespace().'\\schemas';
		}
		$namespaces[] = '\\pxn\\phpPortal\\schemas';
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
