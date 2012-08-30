<?php
defined( '_WAEXEC' ) or die( 'Restricted access' );
/**
* Wandering PHP Framework
*
* PHP 5
*
* @package Wandering
* @author Nowayforback<nowayforback@gmail.com>
* @copyright Copyright (c) 2012, Nowayforback, (http://nowayforback.com) 
* @license http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
* @link http://nowayforback.com
* @since Version 1.0
* @filesource
*/
	

class DatabaseManager {
/**
 * Holds a loaded instance of the Connections object
 *
 * @var object
 * @access public
 */
	var $config = null;
/**
 * Holds instances DataSource objects
 *
 * @var array
 * @access protected
 */
	var $_dataSources = array();
/**
 * Contains a list of all file and class names used in Connection settings
 *
 * @var array
 * @access protected
 */
	var $_connectionsEnum = array();
/**
 * Constructor.
 *
 */
	function __construct() {
	
		//quanghuy
		if (file_exists(WAPATH_SYSTEM. 'config/database'.EXT)) {
			require_once (WAPATH_SYSTEM.'config/database'.EXT);
		} else {
			show_error('The configuration file database'.EXT.' does not exist.');
		}
		
		$this->config = new DATABASE_CONFIG();
		log_message('debug', "ConnectionManager Class Initialized");
	
	}
	

/**
 * Gets a reference to a DataSource object
 *
 * @param string $name The name of the DataSource, as defined in app/config/connections
 * @return object Instance
 * @access public
 * @static
 */
	function &getDataSource($name) {
		if (in_array($name, array_keys($this->_dataSources))) {
			return $this->_dataSources[$name];
		}
		$connections = get_object_vars($this->config);
		
		if (in_array($name, array_keys($connections))) {
			$config = $connections[$name];
			if (is_array($config) && !isset($config["name_source"])) $config["name_source"] = $name;
			$myDriver = $this->__getDriver($config);
			$class = $myDriver['classname'];
			$fileName = $myDriver['filename'];
			require_once(dirname(__FILE__).DS. "datalayer".DS.$fileName.EXT);
			if (class_exists($class)) $this->_dataSources[$name] = new $class($config);
			$this->_dataSources[$name]->configKeyName = $name;
		} else {
			show_error("Haven't a DataSource name is ".$name);
		}

		return $this->_dataSources[$name]; //can return by null object
	}


/**
 * Dynamically creates a DataSource object at runtime, with the given name and settings
 *
 * @param string $name The DataSource name
 * @param array $config The DataSource configuration settings
 * @return object A reference to the DataSource object, or null if creation failed
 * @access public
 * @static
 */
	function &create($name = '', $config = array()) {
		$connections = get_object_vars($this->config);
		if (empty($name) || empty($config) || array_key_exists($name, $connections)) {
			$null = null;
			return $null;
		}

		$this->config->{$name} = $config;
		return $this->getDataSource($name);
	}
/**
 * Returns the file, class name, and parent for the given driver.
 *
 * @return array An indexed array with: filename, classname, and parent
 * @access private
 */
	function __getDriver($config) {

		if (!isset($config['datasource'])) {
			$config['datasource'] = 'pado';
		}

		if (isset($config['driver']) && $config['driver'] != null && !empty($config['driver'])) {
			$filename = $config['datasource'] . DS . $config['datasource'] . '_' . $config['driver'];
			$classname = fileNameToClassName(strtolower($config['datasource'] . '_' . $config['driver']));
			$parent = $this->__getDriver(array('datasource' => $config['datasource']));
		} else {
			$filename = $config['datasource'] . '_source';
			$classname = fileNameToClassName(strtolower($config['datasource'] . '_source'));
			$parent = null;
		}
		return array('filename'  => $filename, 'classname' => $classname, 'parent' => $parent);
	}

}

?>
