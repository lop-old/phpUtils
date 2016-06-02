<?php
/*
 * PoiXson phpUtils - PHP Utilities Library
 * @copyright 2004-2016
 * @license GPL-3
 * @author lorenzo at poixson.com
 * @link http://poixson.com/
 */
namespace pxn\phpUtils\pxdb;

use pxn\phpUtils\General;


abstract class dbPrepared {

//	const ARG_PRE   = '[';
//	const ARG_DELIM = '|';
//	const ARG_POST  = ']';

	protected $st   = NULL;
	protected $rs   = NULL;
	protected $sql  = NULL;
	protected $desc = NULL;

	protected $row      = NULL;
	protected $args     = array();
	protected $rowCount = -1;
	protected $insertId = -1;
	protected $hasError = FALSE;



	public function __construct() {
		$this->clean();
	}



	public abstract function getConn();
	public abstract function getTablePrefix();



	public function Prepare($sql) {
		if (empty($sql)) {
			$this->setError();
			fail('sql argument is required!');
			exit(1);
		}
		$this->clean();
		try {
			$this->sql = \str_replace(
					'__table__',
					$this->getTablePrefix(),
					$sql
			);
			// prepared statement
			$this->st = $this->getConn()
					->prepare($this->sql);
			return $this;
		} catch (\PDOException $e) {
			$this->setError($e->getMessage());
			fail("Query failed: {$this->sql} - {$this->desc}", 1, $e);
			exit(1);
		}
		$this->clear();
		return NULL;
	}
//	public function prep($sql) {
//		return $this->Prepare($sql);
//	}



	public function Execute($sql=NULL) {
		if ($this->hasError()) {
			return NULL;
		}
		if (!empty($sql)) {
			if ($this->Prepare($sql) == NULL) {
				$this->setError();
				return NULL;
			}
		}
		if (empty($this->sql) || $this->st == NULL) {
			$this->setError();
			return NULL;
		}
		try {
			$pos = \strpos(' ', $this->sql);
			$firstPart = \strtoupper(
				$pos === FALSE
				? $this->sql
				: \substr($this->sql, 0, $pos)
			);
			// run query
			if (!$this->st->execute()) {
				$this->setError();
				return NULL;
			}
			// get insert id
			if ($firstPart == 'INSERT') {
				$this->insertId = $this->conn->lastInsertId();
			// get row count
			} else {
				$this->rowCount = $this->st->rowCount();
			}
			return $this;
		} catch (\PDOException $e) {
			$this->setError($e->getMessage());
			fail("Query failed: {$this->sql} - {$this->desc}", 1, $e);
			exit(1);
		}
		$this->clear();
		return NULL;
	}
//	public function exec($sql=NULL) {
//		return $this->Execute($sql);
//	}



	public function Next() {
		if ($this->hasError() || $this->st == NULL) {
			return FALSE;
		}
		try {
			$this->row = $this->st
					->fetch(\PDO::FETCH_ASSOC);
			// finished
			if ($this->row === FALSE) {
				$this->setError();
				return FALSE;
			}
			return $this->row;
		} catch (\PDOException $e) {
			$this->setError($e->getMessage());
			fail("Query failed: {$this->sql} - {$this->desc}", 1, $e);
			exit(1);
		}
		return FALSE;
	}



	public function getRowCount() {
		if ($this->hasError() || $this->st == NULL || $this->rowCount < 0) {
			return -1;
		}
		return $this->rowCount;
	}
	public function getInsertId() {
		if ($this->hasError() || $this->st == NULL || $this->insertId < 0) {
			return -1;
		}
		return $this->insertId;
	}



	public function desc($desc=NULL) {
		if ($desc != NULL) {
			$this->desc = $desc;
		}
		return $this->desc;
	}



	protected function setError($msg=NULL) {
		$this->hasError =
			empty($msg)
			? TRUE
			: $this->hasError = $msg;
	}
	public function getError() {
		if ($this->hasError === FALSE) {
			return NULL;
		}
		if ($this->hasError === TRUE) {
			return 'Unknown error';
		}
		return $this->hasError;
	}
	public function hasError() {
		return ($this->hasError != FALSE);
	}



	public function clean() {
		$this->st       = NULL;
		$this->rs       = NULL;
		$this->sql      = NULL;
		$this->desc     = NULL;
		$this->row      = NULL;
		$this->args     = array();
		$this->rowCount = -1;
		$this->insertId = -1;
		$this->hasError = FALSE;
	}



	// --------------------------------------------------
	// query parameters



	public function setString($index, $value) {
		if ($this->hasError() || $this->st == NULL) {
			return NULL;
		}
		try {
			$value = General::castType($value, 'str');
			$this->st->bindParam($index, $value);
			$this->args .= " String: {$value}";
			return $this;
		} catch (\PDOException $e) {
			fail("Query failed: {$this->sql} - {$this->desc}", 1, $e);
		}
		return NULL;
	}
	public function setInt($index, $value) {
		if ($this->hasError() || $this->st == NULL) {
			return NULL;
		}
		try {
			$value = General::castType($value, 'int');
			$this->st->bindParam($index, $value);
			$this->args .= " Int: {$value}";
			return $this;
		} catch (\PDOException $e) {
			fail("Query failed: {$this->sql} - {$this->desc}", 1, $e);
		}
		return NULL;
	}
	public function setDouble($index, $value) {
		if ($this->hasError() || $this->st == NULL) {
			return NULL;
		}
		try {
			$value = General::castType($value, 'dbl');
			$this->st->bindParam($index, $value);
			$this->args .= " Dbl: {$value}";
			return $this;
		} catch (\PDOException $e) {
			fail("Query failed: {$this->sql} - {$this->desc}", 1, $e);
		}
		return NULL;
	}
	public function setLong($index, $value) {
		if ($this->hasError() || $this->st == NULL) {
			return NULL;
		}
		try {
			$value = General::castType($value, 'lng');
			$this->st->bindParam($index, $value);
			$this->args .= " Lng: {$value}";
			return $this;
		} catch (\PDOException $e) {
			fail("Query failed: {$this->sql} - {$this->desc}", 1, $e);
		}
		return NULL;
	}
	public function setBool($index, $value) {
		if ($this->hasError() || $this->st == NULL) {
			return NULL;
		}
		try {
			$value = General::castType($value, 'bool');
			$this->st->bindParam($index, $value);
			$this->args .= " Bool: {$value}";
			return $this;
		} catch (\PDOException $e) {
			fail("Query failed: {$this->sql} - {$this->desc}", 1, $e);
		}
		return NULL;
	}
//	public function setDate($index, $value) {
//		if ($this->hasError() || $this->st == NULL) {
//			return NULL;
//		}
//		try {
//			$value = General::castType($value, 'str');
//			$this->st->bindParam($index, $value);
//			$this->args .= " Date: {$value}";
//			return $this;
//		} catch (\PDOException $e) {
//			fail("Query failed: {$this->sql} - {$this->desc}", 1, $e);
//		}
//		return NULL;
//	}



	// --------------------------------------------------
	// get results



	public function getString($index) {
		if ($this->hasError() || $this->row == NULL || !isset($this->row[$index])) {
			return FALSE;
		}
		return General::castType($this->row[$index], 'str');
	}
	public function getInt($index) {
		if ($this->hasError() || $this->row == NULL || !isset($this->row[$index])) {
			return FALSE;
		}
		return General::castType($this->row[$index], 'int');
	}
	public function getDouble($index) {
		if ($this->hasError() || $this->row == NULL || !isset($this->row[$index])) {
			return FALSE;
		}
		return General::castType($this->row[$index], 'dbl');
	}
	public function getLong($index) {
		if ($this->hasError() || $this->row == NULL || !isset($this->row[$index])) {
			return FALSE;
		}
		return General::castType($this->row[$index], 'lng');
	}
	public function getBool($index) {
		if ($this->hasError() || $this->row == NULL || !isset($this->row[$index])) {
			return FALSE;
		}
		return General::castType($this->row[$index], 'bool');
	}
	public function getDate($index, $format=NULL) {
		if ($this->hasError() || $this->row == NULL || !isset($this->row[$index])) {
			return FALSE;
		}
		$value = General::castType($this->row[$index], 'int');
		if ($value === FALSE || $value === NULL) {
			return FALSE;
		}
		if (empty($format)) {
			$format = 'Y-m-d H:i:s';
		}
		return \date($format, $value);
		
		return General::castType($this->row[$index], 'str');
	}



}
