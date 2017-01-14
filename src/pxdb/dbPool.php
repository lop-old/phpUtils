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

	// cache existing db schema
	protected $existingTables = NULL;
	protected $existingTableFields = [];
	protected $usingTables = [];



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
		$pool = new self(
			$dbName,
			$conn
		);
		self::$pools[$dbName] = $pool;
		return $pool;
	}
	public function __construct($dbName, $conn) {
		$this->dbName = $dbName;
		$this->conns[] = $conn;
	}



	public static function get($dbName=NULL, $errorMode=NULL) {
		$pool = self::getPool($dbName);
		if ($pool == NULL) {
			return NULL;
		}
		$db = $pool->getDB($errorMode);
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
	public function getDB($errorMode=NULL) {
		if ($errorMode === NULL) {
			$errorMode = dbConn::ERROR_MODE_EXCEPTION;
		}
		// get db connection
		$found = NULL;
		// find unused
		foreach ($this->conns as $conn) {
			// connection in use
			if ($conn->inUse())
				continue;
			// available connection
			$found = $conn;
			$found->setErrorMode($errorMode);
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
		$found->setErrorMode($errorMode);
		return $found;
	}



	public static function dbExists($dbName=NULL) {
		if (empty($dbName)) {
			$dbName = self::$dbNameDefault;
		}
		return isset(self::$pools[$dbName])
			&& self::$pools[$dbName] != NULL;
	}
	public static function getPools() {
		return self::$pools;
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
	public static function castPoolName($pool) {
		if (\is_string($pool)) {
			return (string) $pool;
		}
		if ($pool instanceof \pxn\phpUtils\pxdb\dbPool) {
			return $pool->getName();
		}
		return NULL;
	}



	public function getConnCount() {
		return \count($this->conns);
	}



	#########################
	## get tables / fields ##
	#########################



	public function getExistingTables() {
		// cached table list
		if ($this->existingTables != NULL) {
			return $this->existingTables;
		}
		// get existing tables
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
			$this->existingTables[] = $tableName;
		}
		$db->release();
		return $this->existingTables;
	}
	public function hasTable($tableName) {
		$tableName = San::AlphaNumUnderscore($tableName);
		if (empty($tableName)) {
			return NULL;
		}
		return \in_array(
			$tableName,
			$this->getExistingTables()
		);
	}



	public function getTableFields($tableName) {
		$tableName = San::AlphaNumUnderscore($tableName);
		// cached fields list
		if (isset($this->existingTableFields[$tableName])) {
			return $this->existingTableFields[$tableName];
		}
		// get fields
		$db = $this->getDB();
		$db->Execute("DESCRIBE `__TABLE__{$tableName}`;");
		$fields = [];
		while ($db->hasNext()) {
			$row = $db->getRow();
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
			$pos = \mb_strpos($field['type'], '(');
			if ($pos !== FALSE) {
				$field['size'] = Strings::Trim(
					\mb_substr($field['type'], $pos),
					'(', ')'
				);
				$field['type'] = \mb_substr($field['type'], 0, $pos);
			}
			// null / not null
			$nullable = $db->getString('Null');
			$field['nullable'] = (\mb_strtoupper($nullable) == 'YES');
			// default value
			if (isset($row['default'])) {
				$default = (
					$row['default'] === NULL
					? NULL
					: $db->getString('Default')
				);
			}
			// primary key
			$primary = $db->getString('Key');
			if (\mb_strtoupper($primary) == 'PRI') {
				$field['primary'] = TRUE;
			}
			// auto increment
			$extra = $db->getString('Extra');
			if (\mb_strpos(\mb_strtolower($extra), 'auto_increment') !== FALSE) {
				$field['increment'] = TRUE;
			}
			$fields[$name] = $field;
		}
		$db->release();
		$this->existingTableFields[$tableName] = $fields;
		return $this->existingTableFields[$tableName];
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
	public function getUsingTables() {
		return $this->usingTables;
	}



	public function CreateTable($tableName, array $firstField) {
		if (empty($tableName)) {
			fail('tableName argument is required!');
			exit(1);
		}
		if ($firstField == NULL || \count($firstField) == 0) {
			fail('firstField argument is required!');
			exit(1);
		}
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
		if (\mb_strtolower($firstField['type']) == 'increment') {
			$fieldName = $firstField['name'];
			if (!self::InitAutoIncrementField($db, $tableName, $fieldName)) {
				fail("Failed to finish creating auto increment field: {$fieldName}");
				exit(1);
			}
		}
		$this->existingTables[] = $tableName;
		$db->release();
	}



	public function addTableField($tableName, array $field) {
		if (empty($tableName)) {
			fail('tableName argument is required!');
			exit(1);
		}
		if ($field == NULL || \count($field) == 0) {
			fail('field argument is required!');
			exit(1);
		}
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
		if (empty($tableName)) {
			fail('tableName argument is required!');
			exit(1);
		}
		if ($field == NULL || \count($field) == 0) {
			fail('field argument is required!');
			exit(1);
		}
		$tableName = San::AlphaNumUnderscore($tableName);
		$fieldName = $field['name'];
		$db = $this->getDB();
		$sql = self::getFieldSQL($field);
		$sql = "ALTER TABLE `__TABLE__{$tableName}` CHANGE `{$fieldName}` {$sql}";
		echo \str_replace('__TABLE__', $db->getTablePrefix(), $sql)."\n";
		$result = $db->Execute($sql);
		if ($result == FALSE) {
			fail("Failed to update table field: {$tableName}::{$fieldName}");
			exit(1);
		}
		echo "\n";
		$db->release();
		return TRUE;
	}



	protected static function getFieldSQL(array $field) {
		if ($field == NULL || \count($field) == 0) {
			fail('field argument is required!');
			exit(1);
		}
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
		dbUtils::fillFieldKeys($field);
		$type = San::AlphaNumUnderscore( $field['type'] );
		$fieldType = \mb_strtolower($type);
		$type      = \mb_strtoupper($type);
		$sql = [];
		// name
		$sql[] = "`{$name}`";
		// auto increment
		if ($fieldType == 'increment') {
			$sql[] = 'INT(11)';
		// type/size
		} else {
			$size = '';
			if (!isset($field['size']) || empty($field['size'])) {
				$sql[] = $type;
			} else {
				$size = San::AlphaNumSpaces($field['size']);
				$sql[] = "{$type}({$size})";
			}
		}
		// charset
		switch ($fieldType) {
		case 'varchar': case 'char':
		case 'text':    case 'longtext':
		case 'enum':    case 'set':
			$sql[] = "CHARACTER SET latin1 COLLATE latin1_swedish_ci";
		}
		// null / not null
		if (!isset($field['nullable']) || $field['nullable'] === NULL) {
			$field['nullable'] = FALSE;
		}
		$sql[] = ($field['nullable'] == FALSE ? 'NOT ' : '').'NULL';
		// default
		if (!\array_key_exists('default', $field)) {
			$field['default'] = NULL;
		}
		if ($field['default'] === NULL) {
			if (isset($field['nullable']) && $field['nullable'] === TRUE) {
				$sql[] = 'DEFAULT NULL';
			}
		} else {
			$default = San::AlphaNumSafeMore($field['default']);
			switch ($fieldType) {
			case 'int': case 'tinyint': case 'smallint':
			case 'mediumint': case 'bigint':
				$default = (int) $default;
				$sql[] = "DEFAULT '{$default}'";
				break;
			case 'decimal': case 'double':
				$default = (double) $default;
				$sql[] = "DEFAULT '{$default}'";
				break;
			case 'float':
				$default = (float) $default;
				$sql[] = "DEFAULT '{$default}'";
				break;
			case 'bit': case 'boolean':
				$default = ($default == 0 ? 0 : 1);
				$sql[] = "DEFAULT '{$default}'";
				break;
			default:
				$sql[] = "DEFAULT '{$default}'";
				break;
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
