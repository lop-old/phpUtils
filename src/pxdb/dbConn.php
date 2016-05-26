<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\pxdb;


class dbConn extends dbPrepared {

	protected $dbName = NULL;
	protected $u      = NULL;
	protected $p      = NULL;
	protected $prefix = NULL;
	protected $dsn    = NULL;

	protected $conn = NULL;
	protected $used = FALSE;



	public function __construct(
		$dbName,
		$driver,
		$host,
		$port,
		$u,
		$p,
		$database,
		$prefix
	) {
		if (empty($dbName)) {
			fail('Database name is required!');
			exit(1);
		}
		$this->dbName = $dbName;
		// build data source name
		$this->dsn = self::BuildDSN(
			$driver,
			$database,
			$host,
			$port
		);
		if (empty($this->dsn)) {
			fail("Failed to generate DSN for database: $dbName");
			exit(1);
		}
		$this->u      = (empty($u) ? 'ro'.'ot' : $u);
		$this->p      = $p;
		$this->prefix = $prefix;
		// connect to database
		try {
			$this->conn = new \PDO(
				$this->dsn,
				$u,
				\base64_decode($p),
				[ \PDO::ATTR_PERSISTENT => TRUE ]
			);
//			if (!$this->isConnected()) {
//				throw new \PDOException();
//			}
		} catch (\PDOException $e) {
			fail("Failed to connect to database: {$dbName} - {$this->dsn}", 1, $e);
			exit(1);
		}
	}
	public function clon() {
//TODO:
fail(__FILE__.' '.__LINE__.' clone() function unfinished!');
		return NULL;
	}



	public function getConn() {
		return $this->conn;
	}
//	public function isConnected() {
//TODO:
//	}
	public function inUse() {
		return $this->used;
	}
	public function lock() {
		if ($this->used == TRUE) {
			fail("Database already locked: {$this->dbName}");
			exit(1);
		}
		$this->used = TRUE;
	}
	public function release() {
		$this->used = FALSE;
		$this->clean();
	}



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
		return $dsn;
	}



}
