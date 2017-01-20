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



	public static function CheckFieldNeedsChanges(array $schemField, array $existField) {
		$fieldName = $schemField['name'];
		$schemField = self::FillFieldKeys_Full  ($fieldName, $schemField);
		$existField = self::FillFieldKeys_Simple($fieldName, $existField);
		if ($schemField == NULL || !\is_array($schemField) || \count($schemField) == 0) {
			fail('Missing schema field array!',
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		if ($existField == NULL || !\is_array($existField) || \count($existField) == 0) {
			fail('Missing existing field array!',
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		$changes = [];

		// check properties based on field type
		switch ($schemField['type']) {
		// length size
		case 'int':       case 'tinyint': case 'smallint':
		case 'mediumint': case 'bigint':
		case 'decimal':   case 'double':  case 'float':
		case 'bit':       case 'char':
		case 'boolean':   case 'bool':
		case 'varchar':
		case 'enum': case 'set':
			$existSize = ($existField['size'] === NULL ? 'null' : (string) $existField['size']);
			$schemSize = ($schemField['size'] === NULL ? 'null' : (string) $schemField['size']);
			if ($existSize != $schemSize) {
				$changes[] = "size({$existSize}>{$schemSize})";
			}
		// no size
		case 'text': case 'longtext': case 'blob':
		case 'date': case 'time':     case 'datetime':
			$existDefault = $existField['default'];
			$schemDefault = $schemField['default'];
			if ($existDefault != $schemDefault) {
				$changes[] = "default({$existDefault}>{$schemDefault})";
			}
			if ($existField['nullable'] !== $schemField['nullable']) {
				$n1 = ($existField['nullable'] ? 'YES' : 'NOT');
				$n2 = ($schemField['nullable'] ? 'YES' : 'NOT');
				$changes[] = "nullable({$n1}>{$n2})";
			}
			break;
		default:
			$fieldName = $schemField['name'];
			$fieldType = $schemField['type'];
			fail("Unsupported field type: $fieldType - $fieldName",
				Defines::EXIT_CODE_USAGE_ERROR);
		}

//		// nullable
//		{
//			$schemFieldNullable = (isset($schemField['nullable']) ? $schemField['nullable'] : NULL);
//			$schemNullStr = NULL;
//			if ($schemFieldNullable === TRUE) {
//				$schemNullStr = 'NULL';
//			} else
//			if ($schemFieldNullable === FALSE) {
//				$schemNullStr = 'NOT_NULL';
//			} else {
//				$schemNullStr = '?NULL?';
//			}
//			$existFieldNullable = (isset($existField['nullable']) ? $existField['nullable'] : NULL);
//			$existNullStr = NULL;
//			if ($existFieldNullable === TRUE) {
//				$existNullStr = 'NULL';
//			} else
//			if ($existFieldNullable === FALSE) {
//				$existNullStr = 'NOT_NULL';
//			} else {
//				$existNullStr = '?NULL?';
//			}
//			$changes[] = "nullable($existNullStr>$schemNullStr)";
//		}

		// auto-increment
		if (isset($schemField['increment']) && $schemField['increment'] == TRUE) {
			if (!isset($existField['increment']) || $existField['increment'] != TRUE) {
				$changes[] = "increment";
			}
		}
		// primary key
		if (isset($schemField['primary']) && $schemField['primary'] == TRUE) {
			if (!isset($existField['primary']) || $existField['primary'] != TRUE) {
				$changes[] = 'primary';
			}
		}
		// done
		if (\count($changes) == 0) {
			return FALSE;
		}
		return \implode(', ', $changes);
	}



	private static function FillFieldKeys_Common(&$fieldName, array &$field) {
		// field name
		$fieldName = \mb_strtolower((string) $fieldName);
		$fieldName = San::AlphaNumUnderscore( $fieldName );
		if (empty($fieldName)) {
			fail('Invalid or missing field name!',
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		if (!isset($field['name'])) {
			// prepend name key
			$field = \array_merge(
				['name' => $fieldName],
				$field
			);
		}
		$field['name'] = $fieldName;
		// field type
		if (!isset($field['type']) || empty($field['type'])) {
			fail("Missing field type for field: $fieldName",
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		$field['type'] = \mb_strtolower(
			San::AlphaNumUnderscore(
				(string) $field['type']
			)
		);
		if (empty($field['type'])) {
			fail("Invalid field type for field: $fieldName",
				Defines::EXIT_CODE_INTERNAL_ERROR);
		}
		// size
		if (!isset($field['size']) || $field['size'] === NULL) {
			$field['size'] = NULL;
		} else {
			switch ($field['type']) {
			case 'int':       case 'tinyint': case 'smallint':
			case 'mediumint': case 'bigint':
			case 'bit':       case 'char':
			case 'boolean':   case 'bool':
			case 'varchar':
				$field['size'] = (int) $field['size'];
			case 'decimal': case 'double':   case 'float':
			case 'enum':    case 'set':
			case 'text':    case 'longtext': case 'blob':
			case 'date':    case 'time':     case 'datetime':
				$field['size'] = (string) $field['size'];
				break;
			default:
				$fieldType = $field['type'];
				fail("Unable to guess size for field: [$fieldType] $fieldName",
					Defines::EXIT_CODE_INTERNAL_ERROR);
			}
		}
	}
	public static function FillFieldKeys_Simple($fieldName, array $field) {
		if ($field == NULL || ! \is_array($field) || \count($field) == 0) {
			return NULL;
		}
		self::FillFieldKeys_Common($fieldName, $field);
//TODO: may need to add more here
		if (!isset($field['default'])) {
			$field['default'] = NULL;
		}
		// done
		return $field;
	}
	public static function FillFieldKeys_Full($fieldName, array $field) {
		if ($field == NULL || ! \is_array($field) || \count($field) == 0) {
			return NULL;
		}
		self::FillFieldKeys_Common($fieldName, $field);
		$fieldName = $field['name'];
		$fieldType = $field['type'];
		// auto-increment
		if ($field['type'] == 'increment') {
			$field['increment'] = TRUE;
		}
		if (isset($field['increment'])) {
			if ($field['increment'] == TRUE) {
				$field['primary']  = TRUE;
				$field['type']     = 'int';
				$field['size']     = 11;
				$field['nullable'] = FALSE;
			} else {
				unset($field['increment']);
			}
		}
		// size
		if (!isset($field['size']) || empty($field['size'])) {
			// guess default size
			switch ($field['type']) {
			case 'int':
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
				fail("Unable to guess size for field: [$fieldType] $fieldName",
					Defines::EXIT_CODE_INTERNAL_ERROR);
			}
		}
		$field['size'] = (int) $field['size'];
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
				fail("Unsupported field type: [$fieldType] $fieldName",
					Defines::EXIT_CODE_INTERNAL_ERROR);
			}
		}
		// default value
		if ($field['nullable'] === TRUE) {
			if (!isset($field['default'])) {
				$field['default'] = NULL;
			}
		} else {
			switch ($field['type']) {
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
				fail("Unsupported field type: [$fieldType] $fieldName",
					Defines::EXIT_CODE_USAGE_ERROR);
			}
		}
		return $field;
	}



}
