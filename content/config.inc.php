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



class config {

	var $config = array();

	/**
	 * Creates a new object to access the specified configuration file.
	 *
	 * @param string $configFile
	 * @return config
	 */
	function __construct($configFile = false) {
		if (!$configFile) {
			$configFile = PATH_PREFIX.PATH_CONFIG.PATH_CONFIG_FILE;
		}
		$this->config = parse_ini_file($configFile);
	}


	/**
	 * Return a value from the configuration.
	 *
	 * @param string $key
	 * @param string $defaultValue
	 * @return string
	 */
	function value($key, $defaultValue = '') {
		$key = str_replace(' ', '', $key);
		if (array_key_exists($key, $this->config)) {
			return $this->config[$key];
		} else {
			return $defaultValue;
		}
	}

}

?>
