<?php

/*
  Content Management System

  Copyright (C) 2006�2008 Systemantics

  Systemantics,
  Bureau for Informatics
  Mauerstr. 10-12
  47877 Willich
  GERMANY

  Web:    www.systemantics.net
  Email:  mail@systemantics.net

  This file is NOT free software. Modification or usage of this file without
  buying an appropriate license from Systemantics breaks international
  copyright laws.
*/



require_once('database.inc.php');



/**
 * Provides information for the Pyrrhon CMS.
 * @author Lutz I�ler
 */
class content {

	/**
	 * @var database
	 */
	var $db = false;

	var $site = false;

	var $hostname = false;
	var $database = false;
	var $username = false;
	var $password = false;
	var $tablePrefix = '';

	/**
	 * @var lang
	 */
	var $lang = false;

	var $uid = false;

	/**
	 * Connects to the database and sets the descriptor to use.
	 *
	 * @param string $siteFile Name of the site file to use
	 * @param string $hostname Database hostname
	 * @param string $database Database name
	 * @param string $username Database user name
	 * @param string $password Database password
	 * @param string $tablePrefix Table prefix (without trailing underscore)
	 */
	function content($siteFile, $hostname, $database, $username, $password, $tablePrefix = false) {
		// Save login data
		$this->hostname = $hostname;
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;

		// Save table prefix
		if ($tablePrefix) {
			$this->tablePrefix = $tablePrefix.'_';
		}

		// Read site information file
		$site = $this->_xml2array(implode('', file($siteFile)));
		if (!$site || !$site['site'][0]) {
			// Site definition could not be read
			$this->site = false;
			return;
		}
		$this->site = $site['site'][0];
		$metaAtom = $this->_xml2array('<atom name="metadata" fields="author">
				<field name="author" type="character">
					<param name="size" value="200" />
				</field>
				<field name="publisher" type="character">
					<param name="size" value="200" />
				</field>
				<field name="contributors" type="character">
					<param name="size" value="200" />
				</field>
				<field name="description" type="plaintext">
					<param name="size" value="5" />
				</field>
				<field name="keywords" type="plaintext">
					<param name="size" value="5" />
				</field>
				<field name="copyright" type="string">
					<param name="size" value="200" />
				</field>
			</atom>');
		$metaList = $this->_xml2array('<list name="metadata" target="metadata" singleton="true" />');
		$this->site['atom'][] = @reset($metaAtom['atom']);
		$this->site['list'][] = @reset($metaList['list']);

		// Connect to the database
		$this->db = new database($hostname, $database, $username, $password);

		// Check wether the site definition file was changed
		// since the last database initialization
		$modified = filemtime($siteFile);
		$timestampFile = $siteFile.PATH_TIMESTAMP_SUFFIX;
		if (is_readable($timestampFile)) {
			$file = fopen($timestampFile, 'r');
			$lastInit = fread($file, 12);
			fclose($file);
		} else {
			$lastInit = 0;
		}
		if ($modified>$lastInit) {
			// The site definition file was changed; update database
			$this->_initDatabase();
			// Write timestamp of database initialization
			$file = fopen($timestampFile, 'w');
			fwrite($file, time());
			fclose($file);
		}
	}

	/**
	 * Converts an XML string to an array.
	 *
	 * @param string $xmlString
	 * @return array
	 */
	function _xml2array($xmlString) {
		$xmlValues = array();
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, $xmlString, $xmlValues);
		xml_parser_free($parser);

		$path = array();
		$values = array();
		$index = array();
		foreach ($xmlValues as $xmlValue) {
			if ($xmlValue['type']=='complete' || $xmlValue['type']=='open') {
				array_push($path, $xmlValue['tag']);
				if (isset($index[count($path)])) {
					$index[count($path)]++;
				} else {
					$index[count($path)] = 0;
				}
				array_push($path, $index[count($path)]);
				if (!is_null($xmlValue['attributes'])) {
					eval('$values[\''.implode('\'][\'', $path).'\'] = $xmlValue[\'attributes\'];');
				}
			}
			if ($xmlValue['type']=='complete' || $xmlValue['type']=='close') {
				unset($index[count($path)]);
				array_pop($path);
				array_pop($path);
			}
		}

		return $values;
	}

	/**
	 * Return the site name as specified in the site definition file.
	 *
	 * @return string
	 */
	function getSiteName() {
		return $this->site['name'];
	}

	/**
	 * Return the site identifier as specified in the site definition file.
	 * The identifier is usually the URL of the site's homepage.
	 *
	 * @return string
	 */
	function getSiteIdentifier() {
		return $this->site['identifier'];
	}

	/**
	 * Return the object used for database access.
	 *
	 * @return Database
	 */
	function getDatabase() {
		return $this->db;
	}

	/**
	 * Checks the connection status to the database.
	 *
	 * @return boolean <code>true</code> if there is a connection to the database,
	 * <code>false</code> otherwise.
	 */
	function isConnected() {
		return $this->db->connected();
	}

	/**
	 * Return an array of ISO language codes the site should
	 * support (as specified in the site definition file).
	 *
	 * @return Array(string)
	 */
	function getLanguages() {
		$langs = explode(';', $this->site['languages']);
		return $langs ? $langs : array('');
	}

	/**
	 * Return the ISO language code of this site's default language.
	 * This is the first language code specified in the site
	 * definition file.
	 *
	 * @return string
	 */
	function getDefaultLanguage() {
		return @reset($this->getLanguages());
	}

	/**
	 * Return the currently selected ISO language code. If the selected
	 * language is available as content language for the website, this
	 * language is returned. Otherwise, the default language is returned.
	 *
	 * @return string
	 */
	function getSelectedLanguage() {
		if ($this->lang) {
			return array_search($this->lang, $this->getLanguages())
				? $this->lang
				: $this->getDefaultLanguage();
		} else {
			return $this->getDefaultLanguage();
		}
	}

	/**
	 * Set the currently selected ISO language code.
	 *
	 * @param lang string
	 *
	 * @return string
	 */
	function setSelectedLanguage($lang) {
		$this->lang = $lang;
	}

	/**
	 * Initialize the database according to the specifications in the
	 * site definition file. This involves only creation and altering
	 * of existing tables but no DROP or DELETE operations. Nevertheless
	 * this function can cause data loss because the altering SQL column
	 * types will not always keep the data.
	 *
	 * @return boolean
	 */
	function _initDatabase() {
		// Create table for id
		$query = 'CREATE TABLE IF NOT EXISTS '.$this->tablePrefix.'_id ('
			.'_id INT NOT NULL auto_increment,'
			.'PRIMARY KEY (_id)'
			.')';
		$this->db->simplequery($query);
		$query = 'SELECT _id FROM '.$this->tablePrefix.'_id LIMIT 1';
		$this->db->query($query);
		if (!$this->db->next_row()) {
			$query = 'INSERT INTO '.$this->tablePrefix.'_id VALUES (0)';
			$this->db->simplequery($query);
		}

		// Create dictionary table
		$query = 'CREATE TABLE IF NOT EXISTS '.$this->tablePrefix.'_dictionary ('
			.'_id INT NOT NULL DEFAULT -1,'
			.'_slug VARCHAR(250) NOT NULL,'
			.'_type VARCHAR(50) NOT NULL,'
			.'PRIMARY KEY (_id),'
			.'INDEX (_slug)'
			.')';
		$this->db->simplequery($query);

		// Create rights table
		$query = 'CREATE TABLE IF NOT EXISTS '.$this->tablePrefix.'_rights ('
			.'_owner INT NOT NULL DEFAULT -1,'
			.'_list VARCHAR(50) NOT NULL,'
			.'_uid VARCHAR(50) NOT NULL,'
			.'PRIMARY KEY (_owner,_list,_uid)'
			.')';
		$this->db->simplequery($query);

		// Create user table
		$query = 'CREATE TABLE IF NOT EXISTS '.$this->tablePrefix.'_users ('
			.'_uid VARCHAR(50) NOT NULL,'
			.'_password VARCHAR(50) NOT NULL,'
			.'PRIMARY KEY (_uid)'
			.')';
		$this->db->simplequery($query);

		// Create log
		$query = 'CREATE TABLE IF NOT EXISTS '.$this->tablePrefix.'_log ('
			.'_id INT NOT NULL,'
			.'_action VARCHAR(50) NOT NULL,'
			.'_uid VARCHAR(50) NOT NULL,'
			.'_time TIMESTAMP NOT NULL,'
			.'PRIMARY KEY (_id,_action,_time)'
			.')';
		$this->db->simplequery($query);

		// Check for a root user
		$query = 'SELECT _password FROM '.$this->tablePrefix.'_users WHERE _uid="root"';
		$this->db->query($query);
		if (!$this->db->next_row()) {
			// No root user specified yet
			// Add a root user
			$query = 'INSERT INTO '.$this->tablePrefix.'_users SET _uid="root", _password="root"';
			$this->db->simplequery($query);
			// Add general access rights for the root user
			$query = 'INSERT INTO '.$this->tablePrefix.'_rights SET _owner=-1,_list="*",_uid="root"';
			$this->db->simplequery($query);
		}

		// Create tables for each atom
		foreach ($this->site['atom'] as $atomInfo) {
			// Get information about an possibly existing atom table
			$table = $this->_makeTableName($atomInfo['name']);
			$fields = array();
			$query = "SHOW FIELDS FROM `$table`";
			$this->db->query($query);
			while ($this->db->next_row()) {
				$fields[strtolower($this->db->row->Field)] = true;
			}

			// Create the table
			$query = 'CREATE TABLE `'.$table.'` ('
				.'_id INT NOT NULL DEFAULT -1,'
				.'_owner INT NOT NULL DEFAULT -1,'
				.'_list VARCHAR(50) NOT NULL,'
				.'_order INT NOT NULL,'
				.'PRIMARY KEY (_id)'
				.') DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';
			$this->db->simplequery($query);

			// Create or change the fields
			foreach ($this->getAtomFields($atomInfo['name']) as $fieldInfo) {
				switch ($fieldInfo->_type) {
					case FIELD_TYPE_BOOLEAN:
						$type = 'BOOL NOT NULL';
						break;
					case FIELD_TYPE_ENUM:
						$type = "ENUM('".implode("','", explode(';', $fieldInfo->_params[FIELD_PARAM_VALUES]))."') NOT NULL";
						break;
					case FIELD_TYPE_LINK:
						$type = 'INT NOT NULL DEFAULT -1';
						break;
					case FIELD_TYPE_MULTILINK:
						$type = 'VARCHAR(100) NOT NULL';
						break;
					case FIELD_TYPE_DATE:
						$type = 'VARCHAR(10) NOT NULL';
						break;
					case FIELD_TYPE_COLOR:
						$type = 'VARCHAR(6) NOT NULL';
						break;
					case FIELD_TYPE_TEXT:
					case FIELD_TYPE_PLAINTEXT:
						$type = 'TEXT NOT NULL';
						break;
					case FIELD_TYPE_INTEGER:
						$type = 'INT NULL';
						break;
					default:
						// All other types are represented as VARCHAR columns
						$type = 'VARCHAR('.($fieldInfo->_params[FIELD_PARAM_SIZE]
							? $fieldInfo->_params[FIELD_PARAM_SIZE]
							: DB_DEFAULT_VARCHAR_LENGTH).') NOT NULL';
				}
				$definedFields = array();
				if ($fieldInfo->_type==FIELD_TYPE_STRING
					|| $fieldInfo->_type==FIELD_TYPE_TEXT
					|| $fieldInfo->_type==FIELD_TYPE_PLAINTEXT) {
					// Multi-language field
					foreach ($this->getLanguages() as $lang) {
						$definedFields[$fieldInfo->_name.($lang=='' ?  '' : '_'.$lang)] = $type;
					}
				} else {
					// Just one field
					$definedFields[$fieldInfo->_name] = $type;
				}
				foreach ($definedFields as $fieldName=>$type) {
					$operation = $fields[strtolower($fieldName)] ? 'CHANGE `'.$fieldName.'`' : 'ADD';
					$query = 'ALTER TABLE `'.$table.'` '.$operation.' `'.$fieldName.'` '.$type;
					$this->db->simplequery($query);
				}
			}
		}

		return true;
	}

	/**
	 * Returns a string dump of the database that can be used for
	 * backup means.
	 *
	 * @param string $mysqldump Path to the mysqldump application
	 * (complete path and file name)
	 * @return string/integer The string dump or the return code of
	 * mysqldump if something went wrong.
	 */
	function backup($mysqldump) {
		$ret = 0;
		$backup = '';
		exec($mysqldump.' -u'.$this->username.' -p'.$this->password.' -h'.$this->hostname.' '.$this->database, $backup, $ret);
		if ($ret==0) {
			return implode("\n", $backup);
		} else {
			return $ret;
		}
	}

	/**
	 * Create a new atom.
	 *
	 * @param string $type Type of the atom to create
	 * @param Atom $owner Owning atom
	 * @param string $listName
	 * @param boolean $newId Specifies wether to create an new ID
	 * for this atom or not (defaults to true)
	 * @return Atom The newly created atom.
	 */
	function newAtom($type, $owner, $listName, $newId = true) {
		$atom = $this->_createAtom($type, (object)null);
		if ($newId) {
			$query = "UPDATE ".$this->tablePrefix."_id SET _id=LAST_INSERT_ID(_id+1)";
			$this->db->simplequery($query);
			$atom->_id = $this->db->insert_id();
		} else {
			$atom->_id = ID_NEW;
		}
		$atom->_owner = $owner->_id;
		$atom->_list = $listName;
		return $atom;
	}

	/**
	 * Retrieves an atom.
	 * @param id The id of the atom to retrieve (or the slug, alternatively)
	 * @return Atom the atom object, or
	 * <code>false</code> iff there is no such id.
	 */
	function getAtom($id, $type = false) {
		if (!$id) {
			return false;
		}

		// Get dictionary info for this atom
		if (is_numeric($id)) {
			$info = false;
		} else {
			// Treat $id as slug
			$info = $this->_getDictionaryInfoBySlug($id, $type);
		}
		if (!$info) {
			// Treat $id as id
			$info = $this->_getDictionaryInfoById($id);
			if (!$info) {
				// The id does not exist
				return false;
			}
		}

		if ($info->_type==SITE) {
			// Create fake atom for SITE
			$row = (object)null;
			$row->_id = $id;
			$row->_type = SITE;
		} else {
			// Retrieve the atom
			$table = $this->_makeTableName($info->_type);
			$query = "SELECT * FROM `$table` WHERE _id='$info->_id'";
			$this->db->query($query);
			if ($this->db->next_row()) {
				$row = $this->db->row;
				$row->_slug = $info->_slug;
			} else {
				return false;
			}
		}

		return $this->_createAtom($info->_type, $row);
	}

	/**
	 * Get information about the atom as specified in the site
	 * definition file.
	 *
	 * @param string $type
	 * @return array
	 */
	function getAtomInfo($type) {
		if ($type==SITE) {
			return $this->site;
		}
		foreach ($this->site['atom'] as $atomInfo) {
			if ($atomInfo['name']==$type) {
				return $atomInfo;
			}
		}
		return false;
	}

	/**
	 * Get the label pattern of the specified atom type
	 * as specified in the site definition, or a default label
	 * pattern if there is no label pattern defined.
	 *
	 * @param string $type
	 * @return string
	 */
	function _getLabelPattern($type) {
		$atomInfo = $this->getAtomInfo($type);
		if ($atomInfo==$this->site) {
			return false;
		} else {
			if ($atomInfo['label']) {
				return $atomInfo['label'];
			} else {
				$firstField = @reset($atomInfo['field']);
				return '%'.$firstField['name'].'%';
			}
		}
	}

	/**
	 * Get the slug pattern of the specified atom type
	 * as specified in the site definition, or <code>false</code>
	 * if there is no slug pattern defined.
	 *
	 * @param string $type
	 * @return string
	 */
	function _getSlugPattern($type) {
		$atomInfo = $this->getAtomInfo($type);
		if ($atomInfo==$this->site) {
			return false;
		} else {
			return $atomInfo['slug'] ? $atomInfo['slug'] : false;
		}
	}

	/**
	 * Saves the specified atom.
	 *
	 * @param Atom $atom The atom to save
	 * @return boolean <code>true</code> if the operation succeeded,
	 * <code>false</code> otherwise
	 */
	function putAtom(&$atom) {
		$table = $this->_makeTableName($atom->_type);

		if ($this->_getDictionaryInfoById($atom->_id)) {
			// Remove the atom from the database
			$this->_removeAtom($atom);
		} else {
			// The atom is new, determine its placement
			if ($atom->_order) {
				// Position specified
				// Make room at this position
		   		$query = "UPDATE $table SET _order=_order+1 WHERE _owner='".$atom->_owner."' AND _list='".$atom->_list."' AND _order>=".$atom->_order;
				$this->db->simplequery($query);
			} else {
				// No position specified, create article as last article
		    	// Get number of articles with the same parent
	   			$query = "SELECT COUNT(_id) AS cnt FROM `$table` WHERE _owner='".$atom->_owner."' AND _list='".$atom->_list."'";
				$this->db->query($query);
		    	$this->db->next_row();
	    		$atom->_order = $this->db->row->cnt+1;
			}
		}

		// Collect the atom's field data
		$fields = array();
		foreach ($this->getAtomFields($atom->_type) as $field) {
			if ($field->_type==FIELD_TYPE_STRING
				|| $field->_type==FIELD_TYPE_TEXT
				|| $field->_type==FIELD_TYPE_PLAINTEXT) {
				// Multi-language fields
				foreach ($this->getLanguages() as $lang) {
					$fieldNameLang = $field->_name.($lang=='' ? '' : '_'.$lang);
					$fields[] = $this->_setField($fieldNameLang, $atom->{$fieldNameLang});
				}
			} else {
				$fields[] = $this->_setField($field->_name, $atom->{$field->_name});
			}
		}

		// Add the internal atom data
		$fields[] = $this->_setField('_id', $atom->_id);
		$fields[] = $this->_setField('_owner', $atom->_owner);
		$fields[] = $this->_setField('_list', $atom->_list);
		$fields[] = $this->_setField('_order', $atom->_order);

		$this->logAction($atom, CMS_ACTION_PUT);

		// Save the atom
		$query = "INSERT INTO `".$table."` SET ".implode(',', $fields);
		$this->db->simplequery($query);

		// Create a slug
		$this->_createSlug($atom);

		// Store the id in the dictionary
		$query = "INSERT INTO ".$this->tablePrefix."_dictionary SET _id='$atom->_id',_type='".$atom->_type."',_slug='".$atom->_slug."'";
		$this->db->simplequery($query);

		// Create a label
		$this->_createLabel($atom);

		// We assume that everything went ok
		return true;
	}

	/**
	 * Remove an atom. The atoms that are owned by this atom are
	 * removed, too.
	 *
	 * @param Atom $atom The atom to remove.
	 * @return <code>true</code> if the operation succeeded,
	 * <code>false</code> otherwise
	 */
	function removeAtom($atom) {
		// Remove the atom from the database
		$this->_removeAtom($atom);

		// Correct the atom's position
		$table = $this->_makeTableName($atom->_type);
		$query = "UPDATE $table SET _order=_order-1 WHERE _owner='$atom->_owner' AND _list='$atom->_list' AND _order>$atom->_order";
		$this->db->simplequery($query);

		// Remove the hull
		// Iterate over all lists with the current type as owner
		foreach ($this->getLists($atom->_type) as $list) {
			// Delete all owned objects from that list
			foreach ($this->getAtoms($atom->_id, $list) as $ownedAtom) {
				$this->removeAtom($ownedAtom);
			}
		}

		// We assume that everything is ok
		return true;
	}

	/**
	 * Remove an atom from the database. Only the atom itself
	 * is removed. Atoms owned by this atom stay in the database.
	 *
	 * @param Atom $atom
	 * @return boolean true if the operation succeeded.
	 */
	function _removeAtom($atom) {
		$this->logAction($atom, CMS_ACTION_REMOVE);

		// Remove the atom
		$table = $this->_makeTableName($atom->_type);
		$query = "DELETE FROM `$table` WHERE _id='".$atom->_id."'";
		$this->db->simplequery($query);

		// Remove id from the dictionary
		$query = "DELETE FROM ".$this->tablePrefix."_dictionary WHERE _id='".$atom->_id."'";
		$this->db->simplequery($query);

		return true;
	}

	/**
	 * Retrieves all atoms that belong to a specific owner.
	 *
	 * @param integer $owner The owner's id to retrieve the atoms of
	 * @param ListInfo $listInfo The list to retrieve the atoms of
	 * @param integer $first Index of the first item to return
	 * @param integer $number Number of items to return
	 * @param string $where Additional WHERE conditions
	 * @param string $order ORDER BY conditions (replaces original conditions)
	 * @return array
	 */
	function getAtoms($owner, $listInfo, $first = false, $number = false, $where = false, $order = false) {
		// Calculate atom table name
		$atomType = $listInfo->_target;
		$atomTable = $this->_makeTableName($atomType);

		if (is_object($owner)) {
			$owner = $owner->_id;
		}

		// Get the sorting
		if (!$order) {
			$order = '';
			if (!isset($listInfo->_ordering) || $listInfo->_ordering==LIST_ORDERING_CUSTOM) {
				$order .= '_order ASC';
			} else {
				$order .= $listInfo->_order;
			}
		}
		if (strlen($order)>0) {
			$order .= ',';
		}
		$order = 'ORDER BY '.$order.'_id ASC';

		// Get all atoms
		$query = "SELECT `$atomTable`.*,dictionary._slug FROM `$atomTable` JOIN ".$this->tablePrefix."_dictionary AS dictionary ON (`$atomTable`._id=dictionary._id) WHERE (_owner='$owner') AND (_list='$listInfo->_name')";
		if ($where) {
			$query .= ' AND ('.str_replace(
					'_id',
					"`$atomTable`._id",
					$where
				).')';
		}
		$query .= ' '.$order;
		if ($first!==false) {
			$query .= " LIMIT $first,$number";
		}
		$this->db->query($query);
		$rows = array();
		while ($this->db->next_row()) {
			$rows[] = $this->db->row;
		}
		$ret = array();
		foreach ($rows as $row) {
			$ret[] = $this->_createAtom($atomType, $row);
		}

		return $ret;
	}

	/**
	 * Retrieves all atoms that belong to a particular type.
	 *
	 * @param string $type
	 * @return array An array of all atoms that belong to the specified type.
	 */
	function getAllAtoms($type) {
		// Calculate atom table name
		$atomTable = $this->_makeTableName($type);

		// Get the sorting
		$order = $atomInfo['order'];
		if (strlen($order)>0) {
			$order .= ',';
		}

		// Get all atoms
		$query = "SELECT * FROM `$atomTable` ORDER BY ".$order."_id ASC";
		$this->db->query($query);
		$rows = array();
		while ($this->db->next_row()) {
			$rows[] = $this->db->row;
		}
		$ret = array();
		foreach ($rows as $row) {
			$ret[] = $this->_createAtom($type, $row);
		}

		return $ret;
	}

	/**
	 * Return the number of atoms that are owned by the
	 * specified atom via the specified list.
	 *
	 * @param Atom $owner
	 * @param ListInfo $listInfo
	 * @param string $where Additional WHERE conditions
	 * @return integer
	 */
	function getAtomCount($owner, $listInfo, $where = false) {
		$query = 'SELECT COUNT(_id) AS num FROM `'.$this->_makeTableName($listInfo->_target).'` WHERE _owner='.$owner->_id;
		if ($where) {
			$query .= " AND ($where)";
		}
		$this->db->query($query);
		$this->db->next_row();
		return (int)$this->db->row->num;
	}

	/**
	 * Get all lists (as ListInfo objects) for a specified owner.
	 *
	 * @param owner The owner (atom name)
	 * @return array
	 */
	function getLists($owner) {
		// Get the list info
		$root = $this->getAtomInfo($owner);
		$ret = array();
		if (is_array($root['list'])) {
			foreach ($root['list'] as $listInfo) {
				$listInfo['owner'] = $owner;
				$ret[] = $this->_createListInfo($listInfo);
			}
		}
		return $ret;
	}

	/**
	 * Get the ListInfo object for the specified list name.
	 *
	 * @param string $owner Atom type of the list source
	 * @param string $listName
	 * @return ListInfo
	 */
	function getListInfo($owner, $listName) {
		// Get the list info
		$root = $this->getAtomInfo($owner);
		if (is_array($root['list'])) {
			foreach ($root['list'] as $listInfo) {
				if ($listInfo['name']==$listName) {
					$listInfo['owner'] = $owner;
					return $this->_createListInfo($listInfo);
				}
			}
		}
		return false;
	}

	/**
	 * Get the list that owns the current atom. The owning list is the list
	 * that connects the owner and the atom, ie. the list the atom was
	 * created as a member of.
	 *
	 * @param Atom $atom
	 * @return ListInfo The list info of the owning list.
	 */
	function getOwningList($atom) {
		$parentInfo = $this->getParentInfo($atom);
		return $parentInfo['list'];
	}

	/**
	 * Get information about the parent of a list. The parent is the owning
	 * list and the atom that owns this list.
	 *
	 * @param Atom $atom The atom to get the parent of.
	 * @return array The parent of the specified atom as an array with the elements
	 *         "atom" and "list", containing the list and the atom info.
	 */
	function getParentInfo($atom) {
		if (!$atom || $atom->_id==ID_NULL) {
			return false;
		}
		do {
			$owner = $this->getAtom($atom->_owner);
			if ($owner) {
				$listInfo = $this->getListInfo($owner->_type, $atom->_list);
			} else {
				$listInfo = $this->getListInfo(SITE, $atom->_list);
			}
			$atom = $owner;
		} while (!$listInfo);
		return array(
			'atom' => $atom->_id,
			'list' => $listInfo->_name,
		);
	}

	/**
	 * Move atom by $delta positions
	 * (warning: no checking is done wether $delta makes sense).
	 *
	 * @param Atom $atom
	 * @param integer $delta
	 * @return nothing
	 */
	function moveAtom($atom, $delta) {
		// Calculate the new position
		$newOrd = $atom->_order+$delta;

		$table = $this->_makeTableName($atom->_type);

		$this->logAction($atom, $delta==1 ? CMS_ACTION_DOWN : CMS_ACTION_UP);

		// Move the atom to the new position
		$query = 'UPDATE `'.$table.'` SET _order='.$newOrd.' WHERE _id='.$atom->_id;
		$this->db->simplequery($query);

		// Move the atom that was at the new position to the old position
		$query = 'UPDATE `'.$table.'` SET _order='.$atom->_order.' WHERE _owner='.$atom->_owner.' AND _list=\''.$atom->_list.'\' AND _order='.$newOrd.' AND _id!='.$atom->_id;
		$this->db->simplequery($query);
	}

	/**
	 * Create a field object from the field info from the site definition file.
	 *
	 * @param array $fieldInfo
	 * @return Field
	 */
	function _createField($fieldInfo) {
		$field = (object)null;
		foreach ($fieldInfo as $name=>$value) {
			if (!is_array($value)) {
				$field->{"_$name"} = $value;
			}
		}
		$field->_params = $this->_getParams($fieldInfo['param']);
		return $field;
	}

	/**
	 * Get the fields of the specified atom type.
	 * @param string $type The atom name to return the fields of
	 * @return array An array of all fields of the specified atom, or
	 * <code>null</code> iff there is no such atom.
	 */
	function getAtomFields($type) {
		if ($type==SITE) {
			return array();
		}

		// Get field info
		$atomInfo = $this->getAtomInfo($type);
		if (!$atomInfo) {
			return false;
		}

		$ret = array();
		foreach ($atomInfo['field'] as $index=>$fieldInfo) {
			$ret[$index] = $this->_createField($fieldInfo);
		}
		return $ret;
	}

	/**
	 * Parse the XML array structure of parameters to an array
	 * with key/value pairs.
	 *
	 * @param array $params
	 * @return array
	 */
	function _getParams($params) {
		$ret = array();
		if (!is_null($params)) {
			foreach ($params as $param) {
				$ret[$param['name']] = $param['value'];
			}
		}
		return $ret;
	}

	/**
	 * Get the dictionary info for an atom by its id.
	 *
	 * @param integer $id The atom's id
	 * @return Object Dictionary entry for the given id, or
	 * <code>false</code> iff there is no such id.
	 */
	function _getDictionaryInfoById($id) {
		if ($id==SITE_ID) {
			$r = (object)null;
			$r->_id = SITE_ID;
			$r->_type = SITE;
			$r->_slug = false;
			return $r;
		}

		// Have a look in the dictionary
		$query = "SELECT _id,_type,_slug FROM ".$this->tablePrefix."_dictionary WHERE _id='$id'";
		$this->db->query($query);
		if ($this->db->next_row()) {
			return $this->db->row;
		} else {
			return false;
		}
	}

	/**
	 * Get the dictionary info for an atom by its slug.
	 *
	 * @param string $slug The atom's slug
	 * @return Object Dictionary entry for the given slug, or
	 * <code>false</code> iff there is no such slug.
	 */
	function _getDictionaryInfoBySlug($slug, $type = false) {
		// Have a look in the dictionary
		$query = "SELECT _id,_type,_slug FROM ".$this->tablePrefix."_dictionary WHERE _slug='$slug'".($type ? " AND _type='$type'" : '');
		$this->db->query($query);
		if ($this->db->next_row()) {
			return $this->db->row;
		} else {
			return false;
		}
	}

	/**
	 * Create an atom.
	 *
	 * @param string $type The type of the new atom
	 * @param object $row The data for the atom
	 * @return Atom an atom of the specified type, holding the specified data.
	 */
	function _createAtom($type, $row) {
		// Create the atom and fill it with data
		$atom = $row;
		$atom->_type = $type;
		foreach ($this->getAtomFields($atom->_type) as $field) {
			if ($field->_type==FIELD_TYPE_STRING || $field->_type==FIELD_TYPE_TEXT || $field->_type==FIELD_TYPE_PLAINTEXT) {
				foreach ($this->getLanguages() as $lang) {
					if (isset($atom->{$field->_name.'_'.$lang})) {
						$atom->{$field->_name.'_'.$lang} = stripslashes($atom->{$field->_name.'_'.$lang});
					}
				}
			} else {
				$atom->{$field->_name} = stripslashes($atom->{$field->_name});
			}
		}

		// Create the atom slug
		if (!$atom->_slug) {
			$this->_createSlug($atom);
		}

		// Create the atom label
		$this->_createLabel($atom);

		return $atom;
	}

	/**
	 * Extend an atom, ie. add the thumb filename if thumb images exist and add the pathnames to images and files.
	 *
	 * @param Atom $atom
	 * @param string $lang
	 * @return Atom the extended atom.
	 */
	function _extendAtom($atom, $lang) {
		if ($atom && $atom->_id!=SITE_ID) {
			foreach ($this->getAtomFields($atom->_type) as $field) {
				if ($field->_type==FIELD_TYPE_IMAGE && $atom->{$field->_name}) {
					if ($field->_params[FIELD_PARAM_THUMB_WIDTH] || $field->_params[FIELD_PARAM_THUMB_HEIGHT]) {
						$atom->{$field->_name.'Thumb'} = $field->_params[FIELD_PARAM_PATH].PATH_THUMB_PREFIX.$atom->{$field->_name};
					}
					if ($field->_params[FIELD_PARAM_PREVIEW_WIDTH] || $field->_params[FIELD_PARAM_PREVIEW_HEIGHT]) {
						$atom->{$field->_name.'Preview'} = $field->_params[FIELD_PARAM_PATH].PATH_PREVIEW_PREFIX.$atom->{$field->_name};
					}
					$atom->{$field->_name} = $field->_params[FIELD_PARAM_PATH].$atom->{$field->_name};
				} else if ($field->_type==FIELD_TYPE_FILE && $atom->{$field->_name}) {
					$atom->{$field->_name} = $field->_params[FIELD_PARAM_PATH].$atom->{$field->_name};
				} else if (isset($atom->{$field->_name.'_'.$lang})) {
					$atom->{$field->_name} = $atom->{$field->_name.'_'.$lang};
				}
			}
		}
		return $atom;
	}

	/**
	 * Add a label string to the specified atom.
	 *
	 * @param Atom $atom
	 * @return nothing
	 */
	function _createLabel(&$atom) {
		// Create label
		$fields = $this->getAtomFields($atom->_type);
		$pattern = $this->_getLabelPattern($atom->_type);
		preg_match_all('/%(.+)%/U', $pattern, $matches);
		$replacements = array();
		foreach ($matches[1] as $fieldName) {
			// Replace this field
			foreach ($fields as $fieldInfo) {
				if ($fieldInfo->_name==$fieldName) {
					if ($fieldInfo->_type==FIELD_TYPE_LINK) {
						// Get the linked atom
						$linkedAtom = $this->getAtom($atom->{$fieldInfo->_name});
						if ($linkedAtom->_id==SITE_ID) {
							$replacements[] = '';
						} else {
							$replacements[] = $linkedAtom->_label;
						}
					} else if ($fieldInfo->_type==FIELD_TYPE_IMAGE) {
						// Get image
						if ($atom->{$fieldInfo->_name}) {
							$replacements[] = '<img src="thumbnail/'.$fieldInfo->_params[FIELD_PARAM_PATH].$atom->{$fieldInfo->_name}.'" alt="" />';
						} else {
							$replacements[] = '';
						}
					} else {
						// Get the textual contents
						$r = $atom->{$fieldInfo->_name};
						if (!$r) {
							// Try a field name extended by the currently selected language
							$r = $atom->{$fieldName.'_'.$this->getSelectedLanguage()};
						}
						if ($fieldInfo->_type==FIELD_TYPE_TEXT) {
							// Remove HTML tags
							$r = strip_tags($r);
						}
						$replacements[] = htmlspecialchars($r);
					}
				}
			}
		}
		$atom->_label = trim(str_replace($matches[0], $replacements, $pattern));
	}

	/**
	 * Add a slug string to the specified atom.
	 *
	 * @param Atom $atom
	 * @return nothing
	 */
	function _createSlug(&$atom) {
		// Create slug
		$fields = $this->getAtomFields($atom->_type);
		$pattern = $this->_getSlugPattern($atom->_type);
		if (!$pattern) {
			// Fallback to label pattern to create the slug
			$pattern = $this->_getLabelPattern($atom->_type);
		}
		preg_match_all('/%(.+)%/U', $pattern, $matches);
		$replacements = array();
		$languages = $this->getLanguages();
		array_unshift($languages, false);
		foreach ($matches[1] as $fieldName) {
			// Replace this field
			foreach ($fields as $fieldInfo) {
				foreach ($languages as $language) {
					$extendedName = $fieldInfo->_name.($language ? '_'.$language : '');
					if ($extendedName==$fieldName) {
						// Get the textual contents
						$r = $atom->{$extendedName};
						if (!$r && !$language) {
							// Try a field name extended by the currently selected language
							$r = $atom->{$fieldName.'_'.$this->getSelectedLanguage()};
						}
						if ($r) {
							$replacements[] = strip_tags($r);
						}
					}
				}
			}
		}
		if ($replacements) {
			// The slug would not be empty
			$s = strtolower(trim(str_replace($matches[0], $replacements, $pattern)));
			$c1 = "áàâéèêíìîóòôúùû";
			$c2 = "aaaeeeiiiooouuu";
			$s = str_replace(array('ä', 'ö', 'ü'), array('ae', 'oe', 'ue'), $s);
			for ($i=0; $i<strlen($s); $i++) {
				$s = str_replace($c1[$i], $c2[$i], $s);
			}
			$slug = preg_replace(
				array(
					'/[^a-z0-9]/',
					'/_+/',
					'/(^_|_$)/',
				),
				array(
					'_',
					'_',
					'',
				),
				$s
			);

			// Check whether the slug already exists
			$info = $this->_getDictionaryInfoBySlug($slug, $atom->_type);
			if ($info && $info->_id!=$atom->_id) {
				// If the slug already exists and doesn't lead to
				// the atom being saved, make the slug unique
				// by adding the atom's ID
				$slug .= "_$atom->_id";
			}
		} else {
			// The slug would be empty, use the atom id
			$slug = $atom->_id;
		}

		$atom->_slug = $slug;
	}

	/**
	 * Calculate a table name from an identifier.
	 *
	 * @param string $ident An identifier
	 * @return string The table name for the identifier.
	 */
	function _makeTableName($ident) {
		// Remove all characters except underscores, plain letters and digits
		$table = preg_replace('/[^a-z0-9_]/', '', strtolower($ident));
		if (substr($table, -1)!='s') {
			// Attach a trailing plural "s"
			$table .= 's';
		}
		return $this->tablePrefix.$table;
	}

	/**
	 * Build a name-value-pair.
	 *
	 * @param string name A name
	 * @param mixed value A value
	 * @return string A string of the form <code>&lt;name&gt;='&lt;value&gt;'</code>.
	 */
	function _setField($name, $value) {
		return "`$name`='".mysql_real_escape_string($value)."'";
	}

	/**
	 * Construct a ListInfo object from the specified data row object.
	 *
	 * @param object $row
	 * @return ListInfo
	 */
	function _createListInfo($row) {
		if (is_array($row)) {
			$listInfo = (object)null;
			foreach($row as $name=>$value) {
				$listInfo->{"_$name"} = $value;
			}
		} else {
			$listInfo = $row;
		}

		// Get info about the target atom type
		$atomInfo = $this->getAtomInfo($listInfo->_target);
		if (!$atomInfo) {
			return false;
		}

		// Check wether the list is defined as singleton
		$listInfo->_singleton = strtolower($listInfo->_singleton)=='true';

		// Check wether the list is fixed
		$listInfo->_fixed = strtolower($listInfo->_fixed)=='true';

		// Get the fields for the list view
		$fields = array();
		$orderFields = array();
		if ($atomInfo['fields']=='') {
			// Field definition is empty, so take the label
			if (is_array($atomInfo['field'])) {
				foreach ($atomInfo['field'] as $field) {
					if ($field['type']==FIELD_TYPE_STRING
						|| $field['type']==FIELD_TYPE_TEXT) {
						$fields[] = $field['name'];
						$orderFields[] = $field['name'].' '.ORDER_ASC;
					}
				}
			}
		} else {
			$fieldNames = array();
			foreach ($atomInfo['field'] as $fieldName) {
				$fieldNames[] = strtolower($fieldName['name']);
			}
			$fieldInfo = $this->getAtomFields($listInfo->_target);
			foreach (explode(';', strtolower($atomInfo['fields'])) as $field) {
				$field = trim($field);
				// Check the ordering
				$sorting = substr($field, -1);
				if ($sorting=='+' || $sorting=='-') {
					$field = trim(substr($field, 0, -1));
				} else {
					$sorting = false;
				}
				// Check field for existence and get field info
				foreach ($fieldInfo as $otherField) {
					if ($otherField->_name==$field || $field=='_label') {
						// Field exists
						$fields[] = $field;
						// Determine order query string
						if ($field!='_label' && $sorting) {
							if ($otherField->_type==FIELD_TYPE_STRING
								|| $otherField->_type==FIELD_TYPE_TEXT
								|| $otherField->_type==FIELD_TYPE_PLAINTEXT) {
								$extendedField = $field.'_'.$this->getSelectedLanguage();
							} else {
								$extendedField = $field;
							}
							$orderFields[] = $extendedField.' '.($sorting=='-' ? ORDER_DESC : ORDER_ASC);
						}
						break;
					}
				}
			}
		}
		$listInfo->_fields = $fields;
		$listInfo->_order = implode(',', $orderFields);

		// Return the full view info
		return $listInfo;
	}

	function checkPassword($uid, $password) {
		// Check whether a user was specified in the config file
		$query = 'SELECT _uid FROM '.$this->_makeTableName('_users')." WHERE (_uid='$uid') AND (PASSWORD('$password')=_password)";
		$this->db->query($query);
		return $this->db->next_row() ? true : false;
	}

	/**
	 * Checks whether the currently logged in UID can access the given
	 * owner/list combination, either directly or via an ancestor.
	 *
	 * @param object $owner The owner atom to check
	 * @param string $listName The list name to check
	 */
	function canAccess($owner, $listName, $uid) {
		$table = $this->_makeTableName('_rights');

		$found = false;
		do {
			foreach (array($listName, '*') as $testList) {
				foreach (array($uid, '*') as $testUid) {
					$query = "SELECT _owner FROM $table WHERE (_owner='$owner->_id') AND (_list='$testList') AND (_uid='$testUid')";
					$this->db->query($query);
					if ($this->db->num_rows()==1) {
						// Found
						$found = true;
						break 2;
					}
				}
			}
			// Not found; try the owner's owner
			$listName = $owner->_list;
			$owner = $this->getAtom($owner->_owner);
		} while ($owner);

		return $found;
	}

	function setUsername($uid) {
		$this->uid = $uid;
	}

	function getUsername() {
		return $this->uid;
	}

	function logAction($atom, $action) {
		$table = $this->tablePrefix.'_log';
		$query = "INSERT INTO $table SET _id='$atom->_id',_action='$action',_time=NULL,_uid='".$this->getUsername()."'";
		$this->db->simplequery($query);
	}

	/**
	 * Generate all slugs.
	 *
	 */
	function generateSlugs() {
		$query = 'SELECT _id FROM '.$this->tablePrefix.'_dictionary';
		$this->db->query($query);
		$ids = array();
		while ($this->db->next_row()) {
			$ids[] = intval($this->db->row->_id);
		}
		foreach ($ids as $id) {
			$atom = $this->getAtom($id);
			$this->putAtom($atom);
		}
		echo count($ids).' slugs generated.';
	}

	function getAtomTimestamp($id) {
		$lastEdit = $this->getLastEdit($id);
		return $lastEdit ? $lastEdit['timestamp'] : false;
	}

	function getLastEdit($id) {
		$info = is_numeric($id)
			? $this->_getDictionaryInfoById($id)
			: $this->_getDictionaryInfoBySlug(mysql_real_escape_string($id));
		if (!$info) {
			// There is no such atom
			return false;
		}

		$table = $this->tablePrefix.'_log';
		$query = "SELECT UNIX_TIMESTAMP(_time) AS ts,_uid FROM $table WHERE _id='$info->_id' ORDER BY _time DESC LIMIT 0,1";
		$this->db->query($query);
		return $this->db->next_row()
			? array('timestamp' => intval($this->db->row->ts), 'uid' => $this->db->row->_uid)
			: false;
	}

} // class

?>
