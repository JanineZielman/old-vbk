<?php

/*
  Content Management System

  Copyright (C) 2006 Pyrrhon Software GbR

  Lutz und Arnd Ißler GbR
  Paul-Klee-Str. 54
  47877 Willich
  GERMANY

  Web:    www.pyrrhon.com
  Email:  info@pyrrhon.com

  This file is NOT free software. Modification or usage of this file without
  buying an appropriate license from Pyrrhon Software breaks international
  copyright laws.
*/



require_once('config.inc.php');
require_once('content.inc.php');
require_once('lang.inc.php');



define('PREFIX_PORTAL', 'portal.');

if (!defined('SITE')) {
	define('SITE', 'SITE');
}
if (!defined('SITE_ID')) {
	define('SITE_ID', -1);
}



class portal {

	/**
	 * @var content
	 */
	var $content = false;

	/**
	 * @var config
	 */
	var $config = false;

	/**
	 * @var string
	 */
	var $defaultLang = false;

	/**
	 * @var lang
	 */
	var $lang = false;

	/**
	 * @var string
	 */
	var $uri = false;

	/**
	 * @var string
	 */
	var $title = false;

	/**
	 * @var string
	 */
	var $description = false;

	/**
	 * Creates a portal object. The specified path prefix can
	 * either be a relative or absolute path specification
	 * that points to the document root of the website
	 * from the URL where the scripts are located.
	 *
	 * @param string $pathPrefix
	 * @return Portal
	 */
	function __construct($pathPrefix = './') {
		// Read the config file
		$configFile = $pathPrefix.PATH_CONFIG.PATH_CONFIG_FILE;
		$this->config = new config($configFile);

		// Create the content access object
		$siteFile = $pathPrefix.PATH_CONFIG.$this->config->value(CONFIG_CMS_SITE_FILE, PATH_SITE_FILE);
		$this->content = new content(
			$siteFile,
			$this->config->value(CONFIG_DATABASE_HOSTNAME),
			$this->config->value(CONFIG_DATABASE_DATABASE),
			$this->config->value(CONFIG_DATABASE_USERNAME),
			$this->config->value(CONFIG_DATABASE_PASSWORD),
			$this->config->value(CONFIG_CMS_TABLEPREFIX, false)
		);
	}

	/**
	 * Set the language code all language-based atom fields
	 * should be retrieved in.
	 *
	 * @param string $lang a language code as specified in
	 * the site definition file
	 */
	function setLanguage($lang) {
		$this->defaultLang = $lang;
		$this->lang = new lang($this->defaultLang, PATH_CONFIG.PATH_LANG, PATH_LANG_FILE);
		$this->content->setSelectedLanguage($this->lang->getSelectedLanguage());
	}

	function getLanguage() {
		return $this->content->getSelectedLanguage();
	}

	/**
	 * Return the language string for the specified key. The specified key
	 * is first prefixed with "portal.". If neither this extended key or
	 * the specified key itself is not found in any of the loaded language
	 * files, the key itself enclosed in square brackets is returned.
	 *
	 * @param string $key
	 * @return string
	 */
	function l($key) {
		if ($this->lang) {
			$extKey = PREFIX_PORTAL.$key;
			$l = $this->lang->l($extKey);
			if ($l=="[$extKey]") {
				return $this->lang->l($key);
			} else {
				return $l;
			}
		} else {
			return "[$key]";
		}
	}

	/**
	 * Returns the atom with the specified ID, or <code>false</code>
	 * if there is no atom with that ID.
	 *
	 * @param integer $id
	 * @return Atom
	 */
	function getAtom($id) {
		if (!$lang && $this->defaultLang) {
			$lang = $this->defaultLang;
		}
		return $this->content->_extendAtom($this->content->getAtom($id), $lang);
	}

	/**
	 * Does nothing (present here only for compatibility).
	 *
	 * @param Atom $atom
	 * @param string $lang
	 * @return Atom the translated atom
	 */
	function translateAtom($atom, $lang = false) {
		return $atom;
	}

	/**
	 * Returns the number of atoms connected to the specified atom
	 * via the specified list, or <code>false</code>
	 * if the atom has no connection to the target type.
	 *
	 * @param integer $ownerId
	 * @param string $listName
	 * @param string $where Additional WHERE conditions
	 * @return integer
	 */
	function getAtomCount($ownerId, $listName, $where = false) {
		$owner = $this->getAtom($ownerId);
		$listInfo = $this->content->getListInfo($owner->_type, $listName);
		if (!$listInfo) {
			// Maybe this list is inline
			$parentInfo = $this->content->getParentInfo($owner);
			if ($parentInfo) {
				// The list seems to be inline, so get further information
				// from the parent
				$parent = $this->getAtom($parentInfo['atom']);
				$listInfo = $this->content->getListInfo($parent->_type, $parentInfo['list']);
			}
		}
		if ($listInfo) {
			return $this->content->getAtomCount($owner, $listInfo, $where);
		} else {
			return false;
		}
	}

	/**
	 * Returns the atoms connected to the specified atom
	 * via the specified list, or <code>false</code>
	 * if the atom has no connection to the target type.
	 * Please note that the returned atoms are not automatically
	 * translated to the default language.
	 *
	 * @param integer $ownerId
	 * @param string $listName
	 * @param integer $first Index of the first item to return
	 * @param integer $number Number of items to return
	 * @param string $where Additional WHERE conditions
	 * @param string $order ORDER BY conditions (replaces original conditions)
	 * @return array(Atom)
	 */
	function getAtoms($ownerId, $listName, $first = false, $number = false, $where = false, $order = false) {
		$owner = $this->getAtom($ownerId);
		$listInfo = $this->content->getListInfo($owner->_type, $listName);
		if (!$listInfo) {
			// Maybe this list is inline
			$parentInfo = $this->content->getParentInfo($owner);
			if ($parentInfo) {
				// The list seems to be inline, so get further information
				// from the parent
				$parent = $this->getAtom($parentInfo['atom']);
				$listInfo = $this->content->getListInfo($parent->_type, $parentInfo['list']);
			}
		}
		if (!$lang && $this->defaultLang) {
			$lang = $this->defaultLang;
		}
		$atoms = array();
		if ($listInfo) {
			foreach ($this->content->getAtoms($owner->_id, $listInfo, $first, $number, $where, $order) as $atom) {
				$atoms[] = $this->content->_extendAtom($atom, $lang);
			}
		}
		return $atoms;
	}

	/**
	 * Returns a singleton atom. It is assumed that the singleton's
	 * owner is the site root.
	 *
	 * @param integer $ownerId (optional)
	 * @param string $listName
	 * @return Atom
	 */
	function getSingleton($ownerId, $listName = false) {
		if ($listName===false) {
			$listName = $ownerId;
			$ownerId = SITE_ID;
		}
		list($singleton) = $this->getAtoms($ownerId, $listName);
		return $this->translateAtom($singleton);
	}

	/**
	 * Get the ListInfo object for the specified list name.
	 *
	 * @param string $owner Atom type of the list source
	 * @param string $listName
	 * @return ListInfo
	 */
	function getListInfo($owner, $listName) {
		return $this->content->getListInfo($owner, $listName);
	}

	/**
	 * Returns an array of ISO language codes the site should
	 * support (as specified in the site definition file).
	 *
	 * @return Array(string)
	 */
	function getLanguages() {
		return $this->content->getLanguages();
	}

	/**
	 * Returns the name of the site as specified in the site
	 * definition file.
	 *
	 * @return string
	 */
	function getSitename() {
		return $this->content->getSitename();
	}

	/**
	 * Returns the database object for direct access to the dabase contents.
	 *
	 * @return Database
	 */
	function getDatabase() {
		return $this->content->getDatabase();
	}

	/**
	 * Print out the site's metadata headers in HTML. This consists of
	 * the Author, Copyright, Description, Keywords and Publisher <meta>
	 * tags of HTML plus a bunch of applicable Dublin Core meta tags.
	 *
	 * @param string $lang The two-character ISO language code used to
	 * identify the language of the site description.
	 *
	 * @return nothing
	 */
	function printMetadata() {
		$meta = $this->getMetadata();
?>	<meta name="Description" content="<?php print htmlentities($meta->description); ?>" />
	<meta name="Keywords" content="<?php print htmlentities($meta->keywords); ?>" />
	<meta name="Author" content="<?php print htmlentities($meta->author); ?>" />
	<meta name="Copyright" content="<?php print htmlentities($meta->author); ?>" />
	<meta name="Publisher" content="<?php print htmlentities($meta->publisher); ?>" />
	<meta name="DC.TITLE" content="<?php print htmlentities($meta->title); ?>" />
	<meta name="DC.CREATOR" content="<?php print htmlentities($meta->author); ?>" />
	<meta name="DC.SUBJECT" content="<?php print htmlentities($meta->keywords); ?>" />
	<meta name="DC.DESCRIPTION" content="<?php print htmlentities($meta->description); ?>" />
	<meta name="DC.PUBLISHER" content="<?php print htmlentities($meta->publisher); ?>" />
	<meta name="DC.CONTRIBUTORS" content="<?php print htmlentities($meta->contributors); ?>" />
	<meta name="DC.DATE" content="<?php print $meta->date; ?>" />
	<meta name="DC.TYPE" content="Interactive Resource" />
	<meta name="DC.FORMAT" content="text/html" />
	<meta name="DC.IDENTIFIER" content="<?php print $meta->identifier; ?>" />
	<meta name="DC.LANGUAGE" content="<?php print $this->getLanguage(); ?>" />
	<meta name="DC.RIGHTS" content="<?php print htmlentities($meta->copyright); ?>" />
<?php
	}

	function getMetadata() {
		$meta = @reset($this->getAtoms(SITE_ID, 'metadata'));
		$meta->language = $this->getLanguage();
		$meta->date = date('r');
		$meta->title = $this->getPageTitle();
		$description = $this->getPageDescription();
		if ($description) {
			$meta->description = $description;
		}
		$meta->identifier = $this->getPageURI() ? $this->getPageURI() : $this->content->getSiteIdentifier();
		return $meta;
	}

	function getOwningPath($atom) {
		$path = array();
		while ($atom && $atom->_list) {
			$path[] = $atom->_list;
			$path[] = $atom->_owner;
			$atom = $this->getAtom($atom->_owner);
		}
		return array_reverse($path);
	}

	/**
	 * Set the identifier (URI) of the currently viewed page.
	 *
	 * @param string $uri
	 */
	function setPageURI($uri) {
		$this->uri = $uri;
	}

	/**
	 * Return the identifier (URI) of the currently viewed page.
	 *
	 * @return string
	 */
	function getPageURI() {
		return $this->uri;
	}

	/**
	 * Set the title of the currently viewed page.
	 *
	 * @param string $title
	 */
	function setPageTitle($title) {
		$this->title = $title;
	}

	/**
	 * Return the title of the currently viewed page.
	 *
	 * @return string
	 */
	function getPageTitle() {
		return $this->title;
	}

	/**
	 * Set the description of the currently viewed page.
	 *
	 * @param string $text
	 */
	function setPageDescription($text) {
		$this->description = $text;
	}

	/**
	 * Return the description of the currently viewed page.
	 */
	function getPageDescription() {
		return $this->description;
	}

	function getVersionString() {
		return CMS_LOGO.' '.CMS_VERSION;
	}

}
