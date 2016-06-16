<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\pxdb;

use pxn\phpUtils\Strings;
use pxn\phpUtils\San;
use pxn\phpUtils\System;


class dbPool {

	const dbNameDefault = 'main';
	const MaxConnections = 5;  // max connections per pool

	// pools[name]
	protected static $pools = [];

	protected $dbName = NULL;
	// conns[index]
	protected $conns   = [];

	protected $usingTables = [];
	protected $knownTables = NULL;
	protected $knownTableFields = [];



	public static function configure(
		$dbName,
		$driver,
		$host,
		$port,
		$u,
		$p,
		$database,
		$prefix
	) {
		$conn = dbConn::Factory(
			(string) $dbName,
			(string) $driver,
			(string) $host,
			(int)    $port,
			(string) $u,
			(string) $p,
			(string) $database,
			(string) $prefix
		);
		unset($u, $p);
		$db = new self(
			$dbName,
			$conn
		);
		self::$pools[$dbName] = $db;
	}
	public function __construct($dbName, $conn) {
		$this->dbName = $dbName;
		$this->conns[] = $conn;
	}



	public static function get($dbName=NULL, $errorMode=dbConn::ERROR_MODE_EXCEPTION) {
		$pool = self::getPool($dbName);
		if ($pool == NULL) {
			return NULL;
		}
		$db = $pool->getDB();
		$db->setErrorMode($errorMode);
		return $db;
	}
	public static function getPool($dbName=NULL) {
		// already pool instance
		if ($dbName != NULL && $dbName instanceof dbPool) {
			return $dbName;
		}
		// default db
		if (empty($dbName)) {
			$dbName = self::dbNameDefault;
		}
		$dbName = (string) $dbName;
		// db pool doesn't exist
		if (!self::dbExists($dbName)) {
			fail("Database isn't configured: $dbName");
			exit(1);
		}
		return self::$pools[$dbName];
	}
	public function getDB() {
		// get db connection
		$found = NULL;
		// find unused
		foreach ($this->conns as $conn) {
			// connection in use
			if ($conn->inUse())
				continue;
			// available connection
			$found = $conn;
			break;
		}
		// clone if in use
		if ($found === NULL) {
			if (\count($this->conns) >= self::MaxConnections) {
				fail("Max connections reached for database: {$dbName}");
				exit(1);
			}
			// get first connection
			$conn = \reset($this->conns);
			// clone the connection
			$found = $conn->cloneConn();
		}
		$found->lock();
		$found->clean();
		return $found;
	}



	public static function dbExists($dbName=NULL) {
		if (empty($dbName)) {
			$dbName = self::$dbNameDefault;
		}
		return isset(self::$pools[$dbName])
			&& self::$pools[$dbName] != NULL;
	}



	public static function getPoolName($pool=NULL) {
		$p = dbPool::getPool($pool);
		if ($p == NULL) {
			return NULL;
		}
		return $p->getName();
	}
	public function getName() {
		return $this->dbName;
	}



	public function getConnCount() {
		return \count($this->conns);
	}



	##########################
	## table / field exists ##
	##########################



	public function getKnownTables() {
		// cached table list
		if ($this->knownTables != NULL) {
			return $this->knownTables;
		}
		// get known tables
		$db = $this->getDB();
		if ($db == NULL) {
			fail('Failed to get db for list of tables!');
			exit(1);
		}
		$db->Execute("SHOW TABLES");
		$database = $db->getDatabaseName();
		while ($db->hasNext()) {
			$tableName = $db->getString("Tables_in_{$database}");
			if (Strings::StartsWith($tableName, '_'))
				continue;
			$this->knownTables[] = $tableName;
		}
		$db->release();
		return $this->knownTables;
	}
	public function hasTable($tableName) {
		$tableName = San::AlphaNumUnderscore($tableName);
		if (empty($tableName)) {
			return NULL;
		}
		return \in_array(
			$tableName,
			$this->getKnownTables()
		);
	}



	public function getTableFields($tableName) {
		$tableName = San::AlphaNumUnderscore($tableName);
		// cached fields list
		if (isset($this->knownTableFields[$tableName])) {
			return $this->knownTableFields[$tableName];
		}
		// get known fields
		$db = $this->getDB();
		$db->Execute("DESCRIBE `__TABLE__{$tableName}`;");
		$fields = [];
		while ($db->hasNext()) {
			// field name
			$name = $db->getString('Field');
			if (Strings::StartsWith($name, '_'))
				continue;
			$field = [];
			// field name
			$field['name'] = $name;
			// type
			$field['type'] = $db->getString('Type');
			// size
			$pos = \strpos($field['type'], '(');
			if ($pos !== FALSE) {
				$field['size'] = Strings::Trim(
					\substr($field['type'], $pos),
					'(', ')'
				);
				$field['type'] = \substr($field['type'], 0, $pos);
			}
			// null / not null
			$nullable = $db->getString('Null');
			$field['nullable'] = (\strtoupper($nullable) == 'YES');
			// default
			$default = $db->getString('Default');
			if ($default !== FALSE && \strtoupper($default) != 'NULL') {
				$field['default'] = $default;
			}
			// primary key
			$primary = $db->getString('Key');
			if (\strtoupper($primary) == 'PRI') {
				$field['primary'] = TRUE;
			}
			// auto increment
			$extra = $db->getString('Extra');
			if (\strpos(\strtolower($extra), 'auto_increment') !== FALSE) {
				$field['increment'] = TRUE;
			}
			$fields[$name] = $field;
		}
		$this->knownTableFields[$tableName] = $fields;
		$db->release();
		return $this->knownTableFields[$tableName];
	}
	public function hasTableField($tableName, $fieldName) {
		$tableName = San::AlphaNumUnderscore($tableName);
		$fieldName = San::AlphaNumUnderscore($fieldName);
		if (empty($tableName) || empty($fieldName)) {
			return NULL;
		}
		$tableFields = $this->getTableFields($tableName);
		if (!isset($tableFields[$fieldName])) {
			return FALSE;
		}
		return TRUE;
	}



	public function UsingTables(...$tables) {
		$this->usingTables = \array_merge($this->usingTables, $tables);
	}
	public function getUsedTables() {
		return $this->usingTables;
	}



	public function CreateTable($tableName, array $firstField) {
		$tableName = San::AlphaNumUnderscore($tableName);
		if (empty($tableName)) {
			fail('table name argument is required!');
			exit(1);
		}
		if (Strings::StartsWith($tableName, '_')) {
			fail("Cannot create tables starting with underscore: {$tableName}");
			exit(1);
		}
		if ($this->hasTable($tableName)) {
			fail("Cannot create table, already exists: {$tableName}");
			exit(1);
		}
		if (empty($firstField)) {
			fail('first field argument is required!');
			exit(1);
		}
		$db = $this->getDB();
		// create table sql
		$fieldSQL = self::getFieldSQL($firstField);
		$engine = 'InnoDB';
		$sql = "CREATE TABLE `__TABLE__{$tableName}` ( {$fieldSQL} ) ENGINE={$engine} DEFAULT CHARSET=latin1";
		if (System::isShell()) {
			echo "\nCreating table: {$tableName} ..\n";
		}
		$db->Execute($sql);
		if (\strtolower($firstField['type']) == 'increment') {
			$fieldName = $firstField['name'];
			if (!self::InitAutoIncrementField($db, $tableName, $fieldName)) {
				fail("Failed to finish creating auto increment field: {$fieldName}");
				exit(1);
			}
		}
		$this->knownTables[] = $tableName;
		$db->release();
	}



	public function addTableField($tableName, array $field) {
		$tableName = San::AlphaNumUnderscore($tableName);
		if ($this->hasTableField($tableName, $field['name'])) {
			return FALSE;
		}
		$db = $this->getDB();
		$sql = self::getFieldSQL($field);
		$sql = "ALTER TABLE `{$tableName}` ADD {$sql}";
		$db->Execute($sql);
		$db->release();
		return TRUE;
	}
	public function updateTableField($tableName, array $field) {
		$tableName = San::AlphaNumUnderscore($tableName);



	}



	protected static function getFieldSQL(array $field) {
		if (!isset($field['name']) || empty($field['name'])) {
			fail('Field name is required!');
			exit(1);
		}
		$name = San::AlphaNumUnderscore( $field['name'] );
		if (Strings::StartsWith($name, '_')) {
			fail("Field names cannot start with underscore: {$name}");
			exit(1);
		}
		if (!isset($field['type']) || empty($field['type'])) {
			fail('Field type is required!');
			exit(1);
		}
		$type = San::AlphaNumUnderscore( $field['type'] );
		$sql = [];
		// name
		$sql[] = "`{$name}`";
		// auto increment
		if (\strtolower($type) == 'increment') {
			$sql[] = 'int(11)';
			$field['nullable'] = FALSE;
		// type/size
		} else {
			$size = '';
			if (isset($field['size']) && !empty($field['size'])) {
				$size = San::AlphaNumSpaces($field['size']);
			}
			$type = \strtoupper($type);
			if (empty($size)) {
				$sql[] = $type;
			} else {
				$sql[] = "{$type}({$size})";
			}
		}
		// null / not null
		if (!isset($field['nullable'])) {
			switch (\strtolower($type)) {
			case 'int':
			case 'tinyint':
			case 'smallint':
			case 'mediumint':
			case 'bigint':
			case 'decimal':
			case 'float':
			case 'double':
			case 'bit':
			case 'boolean':
				$field['nullable'] = FALSE;
				break;
			case 'varchar':
			case 'char':
			case 'text':
			case 'longtext':
			case 'blob':
				$field['nullable'] = TRUE;
				break;
			case 'enum':
			case 'set':
				$field['nullable'] = TRUE;
				break;
			case 'date':
				$field['nullable'] = FALSE;
				if (!isset($field['default'])) {
					$field['default'] = '0000-00-00';
				}
				break;
			case 'time':
				$field['nullable'] = FALSE;
				if (!isset($field['default'])) {
					$field['default'] = '00:00:00';
				}
				break;
			case 'datetime':
				$field['nullable'] = FALSE;
				if (!isset($field['default'])) {
					$field['default'] = '0000-00-00 00:00:00';
				}
				break;
			default:
				$field['nullable'] = TRUE;
				break;
			}
		}
		$sql[] = ($field['nullable'] == FALSE ? 'NOT ' : '').'NULL';
		// default
		if (!isset($field['default']) && $field['nullable'] == TRUE) {
			$field['default'] = NULL;
		}
		if (isset($field['default'])) {
			if ($field['default'] === NULL) {
				$sql[] = 'DEFAULT NULL';
			} else {
				$default = San::AlphaNumSafeMore($field['default']);
				switch (\strtolower($type)) {
				case 'int':
				case 'tinyint':
				case 'smallint':
				case 'mediumint':
				case 'bigint':
					$default = (int) $default;
					$sql[] = "DEFAULT {$default}";
					break;
				case 'decimal':
				case 'double':
					$default = (double) $default;
					$sql[] = "DEFAULT {$default}";
					break;
				case 'float':
					$default = (float) $default;
					$sql[] = "DEFAULT {$default}";
					break;
				case 'bit':
					$default = ($default == 0 ? 0 : 1);
					$sql[] = "DEFAULT {$default}";
					break;
				case 'boolean':
					$default = ($default == 0 ? 0 : 1);
					$sql[] = "DEFAULT {$default}";
					break;
				default:
					$sql[] = "DEFAULT '{$default}'";
					break;
				}
			}
		}
		// done
		return \implode(' ', $sql);
	}
	protected static function InitAutoIncrementField($db, $tableName, $fieldName) {
		$tableName = San::AlphaNumUnderscore($tableName);
		$fieldName = San::AlphaNumUnderscore($fieldName);
		$sql = "ALTER TABLE `__TABLE__{$tableName}` ADD PRIMARY KEY ( `{$fieldName}` )";
		if (!$db->Execute($sql)) {
			return FALSE;
		}
		$sql = "ALTER TABLE `__TABLE__{$tableName}` MODIFY `{$fieldName}` int(11) NOT NULL AUTO_INCREMENT";
		if (!$db->Execute($sql)) {
			return FALSE;
		}
		return TRUE;
	}



}
