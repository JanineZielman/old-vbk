<?php

/*
  Content Management System

  Copyright (C) 2006–2008 Systemantics

  Systemantics,
  Bureau for Informatics
  Mauerstr. 10-12
  52064 Aachen
  GERMANY

  Web:    www.systemantics.net
  Email:  mail@systemantics.net

  This file is NOT free software. Modification or usage of this file without
  buying an appropriate license from Systemantics breaks international
  copyright laws.
*/



require_once('constants.inc.php');



class database {

	var $handle;

	var $row;

	/**
	 * Connect to the database.
	 *
	 * @param string $hostname
	 * @param string $database
	 * @param string $username
	 * @param string $password
	 * @return database
	 */
	function __construct($hostname, $database, $username, $password) {
		$this->handle = @mysqli_connect($hostname, $username, $password) or false;
		if ($this->connected()) {
			mysqli_select_db($this->handle, $database) or $this->error();
			mysqli_set_charset($this->handle, 'utf8');
		}
	}

	/**
	 * Close the database connection.
	 *
	 * @return nothing
	 */
	function close() {
		mysqli_close($this->handle);
	}

	/**
	 * Perform an SQL query on the database.
	 *
	 * @param string $query
	 * @return nothing
	 */
	function query($query) {
		$this->result = mysqli_query($this->handle, $query);
	}

	/**
	 * Perform an SQL query on the database without
	 * affecting the internal result set resource. This is
	 * useful for all queries where there is no result
	 * like INSERT and DELETE.
	 *
	 * @param string $query
	 * @return boolean
	 */
	function simplequery($query) {
		return mysqli_query($this->handle, $query);
	}

	/**
	 * Return the next row of the result set as object.
	 *
	 * @return object The result row object or false if there
	 * are no more rows in the result set.
	 */
	function next_row() {
		if ($this->result) {
			$this->row = @mysqli_fetch_object($this->result);
			return $this->row;
		} else {
			return FALSE;
		}
	}

	/**
	 * Print out a very inconvenient error message if there
	 * is a database error.
	 *
	 * @return nothing
	 */
	function error() {
		//print 'Database error!';
	}

	/**
	 * Reutnr the number of rows affected by the last query.
	 *
	 * @return integer
	 */
	function affected_rows() {
		return mysqli_affected_rows($this->handle);
	}

	/**
	 * Returns the number of rows in the current result set.
	 *
	 * @return integer
	 */
	function num_rows() {
		return mysqli_num_rows($this->result);
	}

	/**
	 * Return the last value filled in an AUTO_INCREMENT column.
	 *
	 * @return string
	 */
	function insert_id() {
		return mysqli_insert_id($this->handle);
	}

	/**
	 * Check wether the object is currently connected
	 * to a database.
	 *
	 * @return boolean
	 */
	function connected() {
		return ($this->handle===false ? false : true);
	}

}

?>
