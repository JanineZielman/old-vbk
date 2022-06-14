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



// URL "action" parameter values
define('ACTION_VIEW', 'view');
define('ACTION_EDIT', 'edit');
define('ACTION_DELETE', 'delete');
define('ACTION_SAVE', 'save');
define('ACTION_UP', 'up');
define('ACTION_DOWN', 'down');
define('ACTION_EXPAND', 'expand');
define('ACTION_BACKUP', 'backup');
define('ACTION_GENERATESLUGS', 'generateslugs');

// Button types
define("BUTTON_BACK",'back');
define("BUTTON_NEW",'new');
define("BUTTON_OK",'ok');
define("BUTTON_SAVE", 'save');
define("BUTTON_CANCEL",'cancel');
define("BUTTON_YES",'yes');
define("BUTTON_NO",'no');
define("BUTTON_DISPLAY",'display');

// CMS Actions
define('CMS_ACTION_DELETE', 'delete');
define('CMS_ACTION_DOWN', 'down');
define('CMS_ACTION_EDIT', 'edit');
define('CMS_ACTION_NEW', 'new');
define('CMS_ACTION_PUT', 'put');
define('CMS_ACTION_REMOVE', 'remove');
define('CMS_ACTION_UP', 'up');

// CMS
define('CMS_LOGO', 'P+');
define('CMS_VERSION', '2.2.0');
define('CMS_REQUIRED_PHP_VERSION', '4.3.0');

// Configuration items
define('CONFIG_DATABASE_HOSTNAME', 'database.hostname');
define('CONFIG_DATABASE_DATABASE', 'database.database');
define('CONFIG_DATABASE_USERNAME', 'database.username');
define('CONFIG_DATABASE_PASSWORD', 'database.password');
define('CONFIG_CMS_COLOR', 'cms.color');
define('CONFIG_CMS_COLOR_HIGHLIGHT', 'cms.highlight');
define('CONFIG_CMS_USERNAME', 'cms.username');
define('CONFIG_CMS_PASSWORD', 'cms.password');
define('CONFIG_CMS_SITE_FILE', 'cms.sitefile');
define('CONFIG_CMS_SITE_CSS', 'cms.sitecss');
define('CONFIG_CMS_LOGO', 'cms.logo');
define('CONFIG_CMS_LISTENER_FILES', 'cms.listeners');
define('CONFIG_CMS_ACTIONS', 'cms.actions');
define('CONFIG_CMS_PATH_IMAGE', 'cms.imagepath');
define('CONFIG_CMS_PATH_FILE', 'cms.filepath');
define('CONFIG_CMS_PATH_MYSQLDUMP', 'cms.mysqldump');
define('CONFIG_CMS_LINKABLE_ATOM_TYPES', 'cms.linkabletypes');
define('CONFIG_CMS_FIXED_LANGUAGE', 'cms.fixedlanguage');
define('CONFIG_CMS_PREVIEW_URL', 'cms.previewurl');
define('CONFIG_CMS_PREVIEW_TYPES', 'cms.previewtypes');
define('CONFIG_CMS_TABLEPREFIX', 'cms.tableprefix');
define('CONFIG_EDITOR_BUTTONS', 'editor.buttons');
define('CONFIG_EDITOR_BLOCKFORMATS', 'editor.blockformats');
define('CONFIG_EDITOR_ELEMENTS', 'editor.elements');
define('CONFIG_LDAP_SERVER', 'ldap.server');
define('CONFIG_LDAP_BASEDN', 'ldap.basedn');

// Configuration values
define('CONFIG_VALUE_YES', 'yes');

// Database
define('DB_DEFAULT_VARCHAR_LENGTH', 100);

// Default values
define('DEFAULT_AMOUNT', 1500);

// Event types
define('EVENT_TYPE_BEFORESAVE', 'beforesave');
define('EVENT_TYPE_SAVED', 'saved');
define('EVENT_TYPE_DELETE', 'delete');

// Field types
define('FIELD_TYPE_STRING', 'string');
define('FIELD_TYPE_TEXT', 'text');
define('FIELD_TYPE_PLAINTEXT', 'plaintext');
define('FIELD_TYPE_IMAGE', 'image');
define('FIELD_TYPE_BOOLEAN', 'boolean');
define('FIELD_TYPE_INTEGER', 'integer');
define('FIELD_TYPE_ENUM', 'enum');
define('FIELD_TYPE_LINK', 'link');
define('FIELD_TYPE_MULTILINK', 'multilink');
define('FIELD_TYPE_FILE', 'file');
define('FIELD_TYPE_CHARACTER', 'character');
define('FIELD_TYPE_VARIANT', 'variant');
define('FIELD_TYPE_DATE', 'date');
define('FIELD_TYPE_COLOR', 'color');

// Field parameter
define('FIELD_PARAM_SIZE', 'size');
define('FIELD_PARAM_MULTIPLICITY', 'multiplicity');
define('FIELD_PARAM_TARGET', 'target');
define('FIELD_PARAM_VALUES', 'values');
define('FIELD_PARAM_WIDTH', 'width');
define('FIELD_PARAM_HEIGHT', 'height');
define('FIELD_PARAM_THUMB_WIDTH', 'thumbwidth');
define('FIELD_PARAM_THUMB_HEIGHT', 'thumbheight');
define('FIELD_PARAM_PREVIEW_WIDTH', 'previewwidth');
define('FIELD_PARAM_PREVIEW_HEIGHT', 'previewheight');
define('FIELD_PARAM_MODE', 'mode');
define('FIELD_PARAM_FORMAT', 'format');
define('FIELD_PARAM_PATH', 'path');
define('FIELD_PARAM_LINES', 'lines');
define('FIELD_PARAM_FIRST', 'first');
define('FIELD_PARAM_LAST', 'last');
define('FIELD_PARAM_DEFAULT', 'default');
define('FIELD_PARAM_READONLY', 'readonly');

// Field parameter values
define('FIELD_VALUE_YES', 'yes');

// Special IDs
define('ID_NEW', 'new');
define('ID_NULL', -1);

// ID prefix for construction element IDs
define('ID_PREFIX_A', 'a');
define('ID_PREFIX_ROW', 'row');

// ID separator
define('ID_SEPARATOR', ',');

// Language settings
define('LANG_DEFAULT', 'en');
define('LANG_PREFIX_ACTION', 'action.');
define('LANG_PREFIX_ATOM', 'atom.');
define('LANG_PREFIX_LIST', 'list.');
define('LANG_PREFIX_LANGUAGE', 'language.');
define('LANG_SUFFIX_FORMAT', '.format');

// List orderings
define('LIST_ORDERING_DEFAULT', 'default');
define('LIST_ORDERING_CUSTOM', 'custom');

// List parameters
define('LIST_PARAM_ORDERING', 'ordering');
define('LIST_PARAM_MAXDEPTH', 'maxdepth');

// List multiplicity
define('MULTIPLICITY_MANY', '*');

// SQL ordering
define('ORDER_ASC', 'ASC');
define('ORDER_DESC', 'DESC');

// URL parameters
define('PARAM_ACTION', 'action');
define('PARAM_CALLING_FIELD', 'field');
define('PARAM_LIST', 'list');
define('PARAM_OWNER', 'owner');
define('PARAM_ID', 'id');
define('PARAM_TYPE', 'type');
define('PARAM_ORDER', 'order');
define('PARAM_LOGIN', 'login');
define('PARAM_USERNAME', 'username');
define('PARAM_PASSWORD', 'password');
define('PARAM_SOURCE', 'src');
define('PARAM_HIDE_UPLOAD_MSG', 'cmshideuploadmsg');
define('PARAM_EXPAND', 'expand');
define('PARAM_PLUGIN', 'plugin');
define('PARAM_ROOT', 'root');
define('PARAM_INDEX', 'index');
define('PARAM_AMOUNT', 'amount');
define('PARAM_FILTER', 'filter');
define('PARAM_VALUE', 'value');

// Path settings
define('PATH_PREFIX', '../');
define('PATH_CONFIG', 'site/');
define('PATH_LANG', 'lang/');
define('PATH_LANG_FILE', '%s.ini');
define('PATH_CONFIG_FILE', 'config.ini');
define('PATH_SITE_FILE', 'site.xml');
define('PATH_MYSQLDUMP', '');
define('PATH_TIMESTAMP_SUFFIX', '.initialized');
define('PATH_THUMB_PREFIX', 's_');
define('PATH_PREVIEW_PREFIX', 'p_');

$PATH_IMAGE_SUFFIX = array(
	1 => '.gif',
	2 => '.jpg',
	3 => '.png',
);

// Session variables
define('SESSION_EXPANDED', 'expanded');
define('SESSION_USERNAME', 'username');
define('SESSION_PASSWORD', 'password');
define('SESSION_HIDE_UPLOAD_MSG', 'hideuploadmsg');
define('SESSION_ROOT', 'root');
define('SESSION_EXTERNALLOGIN', 'externallogin');

// Site identification
define('SITE', 'SITE');
define('SITE_ID', -1);

// Several widths
define('WIDTH_INDENT_LIST', '55');
define('WIDTH_INDENT_NAVIGATION', '20');

?>