<?php
/*
 * PoiXson phpUtils - Website Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\pxdb;


class dbPool {

	const dbNameDefault = 'main';
	const MaxConnections = 5;  // max connections per pool

	// pool[name][index]
	protected static $pool = array();

	protected $dbName = NULL;
	protected $conn   = NULL;



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
		$conn = new dbConn(
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
		self::$pool[$dbName] = $db;
	}
	public function __construct($dbName, $conn) {
		$this->dbName = $dbName;
		$this->conn   = $conn;
	}



	public static function getDB($dbName=NULL) {
		// default db
		if (empty($dbName))
			$dbName = self::dbNameDefault;
		// db doesn't exist
		if (!self::dbExists($dbName)) {
			fail("Database isn't loaded: $dbName");
			exit(1);
		}
		// get db connection
		$found = NULL;
		$hasDisconnected = FALSE;
		// find unused
		foreach (self::$pool[$dbName] as $db) {
			if (!$db->isConnected()) {
				$hasDisconnected = TRUE;
				continue;
			}
			if ($db->inUse()) {
				continue;
			}
			$found = $db;
		}
		// clone if in use
		if ($found === NULL) {
			if (count(self::$pool[$dbName]) >= self::MaxConnections) {
				fail("Max connections reached for database: {$dbName}");
				exit(1);
			}
			$found = \reset(self::$pool[$dbName])->clone();
		}
		// remove disconnected
		if ($hasDisconnected) {
			$disCount = 0;
			foreach (self::$pool[$dbName] as $k => $db) {
				if (!$db->isConnected()) {
					unset(self::$pool[$dbName][$k]);
					$disCount++;
				}
			}
			//log(Removed $disCount disconnected db conns from pool: $dbName)
		}
		$found->setUsed();
		$found->clean();
		return $found;
	}



	public static function dbExists($dbName=NULL) {
		if (empty($dbName))
			$dbName = self::$dbNameDefault;
		if (!isset(self::$pool[$dbName]))
			return FALSE;
		return (\count(self::$pool) > 0);
	}



}
