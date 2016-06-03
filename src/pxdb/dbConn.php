<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\pxdb;


class dbConn extends dbPrepared {

	const ERROR_MODE_EXCEPTION = FALSE;
	const ERROR_MODE_PASSIVE   = TRUE;

	protected $dbName = NULL;
	protected $u      = NULL;
	protected $p      = NULL;
	protected $prefix = NULL;
	protected $dsn    = NULL;

	protected $conn = NULL;
	protected $used = FALSE;



	public static function Factory(
		$dbName,
		$driver,
		$host,
		$port,
		$u,
		$p,
		$database,
		$prefix
	) {
		// build data source name
		$dsn = self::BuildDSN(
			$driver,
			$database,
			$host,
			$port
		);
		if (empty($dsn)) {
			fail("Failed to generate DSN for database: $dbName");
			exit(1);
		}
		$conn = new self(
			$dbName,
			$dsn,
			$u,
			$p,
			$prefix
		);
		return $conn;
	}
	// new connection
	public function __construct(
		$dbName,
		$dsn,
		$u,
		$p,
		$prefix
	) {
		parent::__construct();
		if (empty($dbName)) {
			fail('Database name is required!');
			exit(1);
		}
		$this->dbName = $dbName;
		$this->dsn    = $dsn;
		$this->u      = (empty($u) ? 'ro'.'ot' : $u);
		$this->p      = $p;
		$this->prefix = $prefix;
		// connect to database
		try {
			$conn = new \PDO(
				$this->dsn,
				$this->u,
				\base64_decode($this->p),
				[ \PDO::ATTR_PERSISTENT => TRUE ]
			);
			$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			$this->conn = $conn;
		} catch (\PDOException $e) {
			fail("Failed to connect to database: {$this->dbName} - {$this->dsn}", 1, $e);
			exit(1);
		}
	}
	public function cloneConn() {
		$conn = new self(
			$this->dbName,
			$this->dsn,
			$this->u,
			$this->p,
			$this->prefix
		);
		return $conn;
	}



	public function getConn() {
		return $this->conn;
	}
	public function getTablePrefix() {
		if (empty($this->prefix)) {
			return '';
		}
		return $this->prefix;
	}



//	public function isConnected() {
//TODO:
//	}
	public function inUse() {
		return $this->used;
	}
	public function isLocked() {
		return $this->inUse();
	}
	public function lock() {
		if ($this->used == TRUE) {
			fail("Database already locked: {$this->dbName}");
			exit(1);
		}
		$this->used = TRUE;
	}
	public function release() {
		$this->clean();
		$this->used = FALSE;
	}
//	public function free() {
//		$this->release();
//	}



	public static function BuildDSN(
		$driver,
		$database,
		$host,
		$port
	) {
		$driver = \strtolower($driver);
		$dsn = "{$driver}:dbname={$database};host={$host}";
		if ($port != NULL && $port > 0 && $port != 3306) {
			$dsn .= ";port={$port}";
		}
		$dsn .= ';charset=utf8mb4';
		return $dsn;
	}



}
