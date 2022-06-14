<?php

/*
  Content Management System
  van Bergen Kolpa

  Copyright (C) 2006 Pyrrhon Software GbR

  Lutz und Arnd Ißler GbR
  Paul-Klee-Str. 54
  47877 Willich
  GERMANY

  Web:    www.pyrrhon.com
  Email:  info@pyrrhon.com

  Permission granted to use this file only on the server of van Bergen Kolpa.
*/



require_once('config.inc.php');



class database {

	// Connection-Handle für die Datenbank
	var $handle;

	// Reihe eines Results
	var $row;

	// Datenbankverbindung herstellen
	function database() {
		$this->handle = @mysql_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD) or false;
		if ($this->connected()) {
			mysql_select_db(DB_DATABASE) or $this->error();
		}
	}

	// Datenbankverbindung trennen
	function close() {
		mysql_close($this->handle);
	}

	function query($query) {
		$this->result = mysql_query($query,$this->handle);
		return $this->result;
	}

	function simplequery($query) {
		return mysql_query($query,$this->handle);
	}

	function next_row() {
		if ($this->result) {
			$this->row = @mysql_fetch_object($this->result);
			return $this->row;
		} else {
			return FALSE;
		}
	}

	function next_row_array() {
		if ($this->result) {
			$this->row = @mysql_fetch_array($this->result);
			return $this->row;
		} else {
			return FALSE;
		}
	}

	function error() {
		print 'Database error!';
	}

	function affected_rows() {
		return mysql_affected_rows($this->handle);
	}

	function num_rows() {
		return $this->result ? mysql_num_rows($this->result) : 0;
	}

	function insert_id() {
		return mysql_insert_id($this->handle);
	}

	function new_id() {
		$query = "UPDATE id SET ID=LAST_INSERT_ID(ID+1)";
		$this->simplequery($query);
		return $this->insert_id();
	}

	function connected() {
		return ($this->handle===false ? false : true);
	}

}

// Automatisch bei Einbindung des Moduls verbinden
$db = new database;



?>
