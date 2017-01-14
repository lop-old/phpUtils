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
use pxn\phpUtils\Defines;


final class dbTools {
	private function __construct() {}



	public static function CheckFieldNeedsChanges(array $schemaField, array $existingField) {
		if ($schemaField == NULL || \count($schemaField) == 0) {
			fail('Missing schema field array!'); ExitNow(Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		if ($existingField == NULL || \count($existingField) == 0) {
			fail('Missing -existing- field array!'); ExitNow(Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		$changes = [];
//TODO: unfinished
fail();



/*
		// check field type
		if ($fieldType == 'increment') {
			if (\mb_strtolower($tabField['type']) !== 'int'
			&&  \mb_strtolower($tabField['type']) !== 'increment') {
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
*/
	}



	public static function FillFieldKeys(&$fieldName, array &$field) {
		if ($field == NULL || ! \is_array($field) || \count($field) == 0) {
			return NULL;
		}
		// field name
		$fieldName = San::AlphaNumUnderscore($fieldName);
		if (empty($fieldName)) {
			fail('Invalid or missing field name!'); ExitNow(Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		if (!isset($field['name']) || empty($field['name'])) {
			// prepend name key
			$field = \array_merge(
				['name' => $fieldName],
				$field
			);
		}
		$fieldName = $field['name'];
		// field type
		if (!isset($field['type']) || empty($field['type'])) {
			fail("Missing field type for field: $fieldName"); ExitNow(Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		$field['type'] = \mb_strtolower(
			San::AlphaNumUnderscore(
				$field['type']
			)
		);
		if (empty($field['type'])) {
			fail("Invalid field type for field: $fieldName"); ExitNow(Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		// size
		if (!isset($field['size']) || empty($field['size'])) {
			// guess default
			switch ($field['type']) {
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
				$fieldType = $field['type'];
				fail("Unable to guess size for field: [$fieldType] $fieldName"); ExitNow(Defines::EXIT_CODE_INTERNAL_ERROR);
			}
		}
		// nullable
//TODO:
		if (isset($field['default']) && $field['default'] === NULL) {
			if (!isset($field['nullable'])) {
				$field['nullable'] = TRUE;
			}
		} else {
			// guess default
			switch ($field['type']) {
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
				fail(); ExitNow(Defines::EXIT_CODE_INTERNAL_ERROR);
			}
		}
//TODO: unfinished
fail();



/*
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
*/
	}



}
