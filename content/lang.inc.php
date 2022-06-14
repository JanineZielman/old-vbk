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



class lang {

	var $strings = array();
	var $lang = false;
	var $availableLanguages = array();
	var $acceptedLanguages = array();

	/**
	 * Determine the languages accepted by the user client
	 * and load the according language files of the CMS
	 * and the site definition.
	 *
	 * @return boolean (always true)
	 */
	function __construct($lang = false, $path = false, $fileMask = false) {
		if ($lang) {
			$this->acceptedLanguages[] = $lang;
		} else {
			// Get the accepted languages
			foreach(explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $value) {
				$value=explode(';', $value);
				$this->acceptedLanguages[] = trim($value[0]);
			}
		}

		if ($path && $fileMask) {
			$this->lang = $this->loadStrings($path, $fileMask);
		} else {
			// Load the CMS and site language strings
			$this->lang = $this->loadStrings(PATH_LANG, PATH_LANG_FILE);
			$this->loadStrings(PATH_PREFIX.PATH_CONFIG.PATH_LANG, PATH_LANG_FILE);
		}

		return true;
	}

	/**
	 * Return the language string for the specified key. If the key
	 * is not found in any of the loaded language files, the key itself
	 * enclosed in square brackets is returned.
	 *
	 * @param string $key
	 * @param string $default
	 * @return string
	 */
	function l($key, $default = false) {
		$key = str_replace(' ', '', $key);
		if (array_key_exists($key, $this->strings)) {
			return $this->strings[$key];
		} else {
			return $default===false ? "[$key]" : $default;
		}
	}

	/**
	 * Loads language strings for the first available language
	 * that is acceptable according to the specified
	 * accepted languages. The language files are searched in
	 * the specified path according to the file mask. The file
	 * mask should contain a %s character as placeholder for the
	 * ISO language code.
	 *
	 * @param string $path
	 * @param string $fileMask
	 * @return string The ISO language code of the loaded language,
	 * or the default language code if no language could be loaded.
	 */
	function loadStrings($path, $fileMask) {
		// Get available languages from the existing language files
		$availableLanguages = array();
		$handle = @opendir($path);
		if (!$handle) {
			// There's no language directory - no need to proceed any further
			return false;
		}
		while ($file = readdir($handle)) {
			if (preg_match('/^'.sprintf(str_replace('.', '\.', $fileMask), '(.+)').'$/i', $file, $matches)) {
				$availableLanguages[$matches[1]] = $matches[1];
			}
		}
		closedir($handle);

		// Select a language for the CMS
		$acceptedLanguages = $this->acceptedLanguages;
		$acceptedLanguages[] = LANG_DEFAULT;
		$lang = $this->negotiate($acceptedLanguages, $availableLanguages);
		if (!$lang && count($availableLanguages)>0) {
			$lang = @reset($availableLanguages);
		}

		$this->availableLanguages += $availableLanguages;

		// Load the language file
		$this->strings = array_merge($this->strings, @parse_ini_file($path.sprintf($fileMask, $lang)));
		return $lang;
	}

	/**
	 * Negotiate the specified asked languages (which are those the
	 * user agent denoted as acceptable) against the languages that
	 * are available.
	 *
	 * @param array $ask_lang
	 * @param array $accept_lang
	 * @return string The negotiated language code or false if no
	 * negotiation could be done.
	 */
	function negotiate($ask_lang, $accept_lang) {
		if (!(is_array($ask_lang) && is_array($accept_lang))) return false;

		foreach($ask_lang as $lang) {
			if (in_array($lang, $accept_lang)) {
				return $lang;
			}
			$short_lang = substr($lang, 0, 2);
			if (in_array($short_lang, $accept_lang)) {
				return $short_lang;
			}
		}

		return false;
	}

	/**
	 * Returns the language code of the selected language.
	 *
	 * @return string The ISO language code of the selected language.
	 */
	function getSelectedLanguage() {
		return $this->lang;
	}

	function getAvailableLanguages() {
		return $this->availableLanguages;
	}

}

?>
