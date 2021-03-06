<?php
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
require_once(dirname(dirname(__FILE__)).DS. "wado_source.php");
 
class WadoMysqli extends WadoSource {
/**
 * Enter description here...
 *
 * @var unknown_type
 */
	var $description = "Mysqli WADO Driver";
/**
 * Enter description here...
 *
 * @var unknown_type
 */
	var $startQuote = "`";
/**
 * Enter description here...
 *
 * @var unknown_type
 */
	var $endQuote = "`";
        
        
/**
* List of table engine specific parameters used on table creating
*
* @var array
* @access public
*/
var $tableParameters = array(
'charset' => array('value' => 'DEFAULT CHARSET', 'quote' => false, 'join' => '=', 'column' => 'charset'),
'collate' => array('value' => 'COLLATE', 'quote' => false, 'join' => '=', 'column' => 'Collation'),
'engine' => array('value' => 'ENGINE', 'quote' => false, 'join' => '=', 'column' => 'Engine')
);

        
/**
 * Base configuration settings for Mysqli driver
 *
 * @var array
 */
	var $_baseConfig = array('persistent' => true,
								'host' => 'localhost',
								'login' => 'root',
								'password' => '',
								'database' => 'wadb',
								'port' => '3306',
								'connect' => 'mysqli_connect');
/**
 * Mysqli column definition
 *
 * @var array
 */
	var $columns = array('primary_key' => array('name' => 'int(11) DEFAULT NULL auto_increment'),
						'string' => array('name' => 'varchar', 'limit' => '255'),
						'text' => array('name' => 'text'),
						'integer' => array('name' => 'int', 'limit' => '11', 'formatter' => 'intval'),
						'float' => array('name' => 'float', 'formatter' => 'floatval'),
						'datetime' => array('name' => 'datetime', 'format' => 'Y-m-d H:i:s', 'formatter' => 'date'),
						'timestamp' => array('name' => 'timestamp', 'format' => 'Y-m-d H:i:s', 'formatter' => 'date'),
						'time' => array('name' => 'time', 'format' => 'H:i:s', 'formatter' => 'date'),
						'date' => array('name' => 'date', 'format' => 'Y-m-d', 'formatter' => 'date'),
						'binary' => array('name' => 'blob'),
						'boolean' => array('name' => 'tinyint', 'limit' => '1'));
/**
 * Connects to the database using options in the given configuration array.
 *
 * @return boolean True if the database could be connected, else false
 */
	function connect() {
		$config = $this->config;
		$this->connected = false;
		$this->connection = mysqli_connect($config['host'], $config['login'], $config['password'], $config['database'], $config['port']);

		if ($this->connection !== false) {
			$this->connected = true;
		}

		if (!empty($config['encoding'])) {
			$this->setEncoding($config['encoding']);
		}
		return $this->connected;
	}
/**
 * Disconnects from database.
 *
 * @return boolean True if the database could be disconnected, else false
 */
	function disconnect() {
		@mysqli_free_result($this->results);
		$this->connected = !@mysqli_close($this->connection);
		return !$this->connected;
	}
/**
 * Executes given SQL statement.
 *
 * @param string $sql SQL statement
 * @return resource Result resource identifier
 * @access protected
 */
	function _execute($sql) {
		if (preg_match('/^\s*call/i', $sql)) {
			return $this->_executeProcedure($sql);
		} else {
			return mysqli_query($this->connection, $sql);
		}
	}
/**
 * Executes given SQL statement (procedure call).
 *
 * @param string $sql SQL statement (procedure call)
 * @return resource Result resource identifier for first recordset
 * @access protected
 */
	function _executeProcedure($sql) {
	    $answer = mysqli_multi_query($this->connection, $sql);

	    $firstResult = mysqli_store_result($this->connection);

        if (mysqli_more_results($this->connection)) {
            while($lastResult = mysqli_next_result($this->connection));
        }

        return $firstResult;
	}
/**
 * Returns an array of sources (tables) in the database.
 *
 * @return array Array of tablenames in the database
 */
	function listSources() {
		$cache = parent::listSources();
		if ($cache != null) {
			return $cache;
		}
		$result = $this->_execute('SHOW TABLES FROM ' . $this->name($this->config['database']) . ';');

		if (!$result) {
			return array();
		} else {
			$tables = array();

			while ($line = mysqli_fetch_array($result)) {
				$tables[] = $line[0];
			}
			parent::listSources($tables);
			return $tables;
		}
	}

/**
 * Returns a quoted and escaped string of $data for use in an SQL statement.
 *
 * @param string $data String to be prepared for use in an SQL statement
 * @param string $column The column into which this data will be inserted
 * @param boolean $safe Whether or not numeric data should be handled automagically if no column data is provided
 * @return string Quoted and escaped data
 */
	function value($data, $column = null, $safe = false) {
		$parent = parent::value($data, $column, $safe);

		if ($parent != null) {
			return $parent;
		}

		if ($data === null) {
			return 'NULL';
		}

		if ($data === '') {
			return  "''";
		}

		switch ($column) {
			case 'boolean':
				$data = $this->boolean((bool)$data);
			break;
			case 'integer' :
			case 'float' :
			case null :
				if (is_numeric($data) && strpos($data, ',') === false && $data[0] != '0' && strpos($data, 'e') === false) {
					break;
				}
			default:
				$data = "'" . mysqli_real_escape_string($this->connection, $data) . "'";
			break;
		}

		return $data;
	}
/**
 * Begin a transaction
 *
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions).
 */
	function begin() {
		if (parent::begin()) {
			if ($this->execute('START TRANSACTION')) {
				$this->_transactionStarted = true;
				return true;
			}
		}
		return false;
	}
/**
 * Commit a transaction
 *
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions,
 * or a transaction has not started).
 */
	function commit() {
		if (parent::commit()) {
			$this->_transactionStarted = false;
			return $this->execute('COMMIT');
		}
		return false;
	}
/**
 * Rollback a transaction
 *
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions,
 * or a transaction has not started).
 */
	function rollback() {
		if (parent::rollback()) {
			return $this->execute('ROLLBACK');
		}
		return false;
	}
/**
 * Returns a formatted error message from previous database operation.
 *
 * @return string Error message with error number
 */
	function lastError() {
		if (mysqli_errno($this->connection)) {
			return mysqli_errno($this->connection).': '.mysqli_error($this->connection);
		}
		return null;
	}
/**
 * Returns number of affected rows in previous database operation. If no previous operation exists,
 * this returns false.
 *
 * @return integer Number of affected rows
 */
	function lastAffected() {
		if ($this->_result) {
			return mysqli_affected_rows($this->connection);
		}
		return null;
	}
/**
 * Returns number of rows in previous resultset. If no previous resultset exists,
 * this returns false.
 *
 * @return integer Number of rows in resultset
 */
	function lastNumRows() {
		if ($this->_result and is_object($this->_result)) {
			return @mysqli_num_rows($this->_result);
		}
		return null;
	}
/**
 * Returns the ID generated from the previous INSERT operation.
 *
 * @param unknown_type $source
 * @return in
 */
	function lastInsertId($source = null) {
		$id = $this->fetchRow('SELECT LAST_INSERT_ID() AS insertID', false);
		if ($id !== false && !empty($id) && !empty($id[0]) && isset($id[0]['insertID'])) {
			return $id[0]['insertID'];
		}

		return null;
	}

/**
 * Gets the length of a database-native column description, or null if no length
 *
 * @param string $real Real database-layer column type (i.e. "varchar(255)")
 * @return integer An integer representing the length of the column
 */
	function length($real) {
		$col = str_replace(array(')', 'unsigned'), '', $real);
		$limit = null;

		if (strpos($col, '(') !== false) {
			list($col, $limit) = explode('(', $col);
		}

		if ($limit != null) {
			return intval($limit);
		}
		return null;
	}
/**
 * Enter description here...
 *
 * @param unknown_type $results
 */
	function resultSet(&$results) {
		$this->results =& $results;
		$this->map = array();
		$num_fields = mysqli_num_fields($results);
		$index = 0;
		$j = 0;
		while ($j < $num_fields) {
			$column = mysqli_fetch_field_direct($results, $j);
			if (!empty($column->table)) {
				$this->map[$index++] = array($column->table, $column->name);
			} else {
				$this->map[$index++] = array(0, $column->name);
			}
			$j++;
		}
	}
/**
 * Fetches the next row from the current result set
 *
 * @return unknown
 */
	function fetchResult() {
		if ($row = mysqli_fetch_row($this->results)) {
			$resultRow = array();
			$i = 0;
			foreach ($row as $index => $field) {
				@list($table, $column) = $this->map[$index];
				$resultRow[$table][$column] = $row[$index];
				$i++;
			}
			return $resultRow;
		} else {
			return false;
		}
	}
	
	
	
	function fetchRawResult() {
		if ($row = mysqli_fetch_row($this->results)) {
			$resultRow = array();
			$i = 0;
			foreach ($row as $index => $field) {
				@list($table, $column) = $this->map[$index];
				$resultRow[$column] = $row[$index];
				$i++;
			}
			return $resultRow;
		} else {
			return false;
		}
	}
	
/**
 * Sets the database encoding
 *
 * @param string $enc Database encoding
 */
	function setEncoding($enc) {
		return $this->_execute('SET NAMES ' . $enc) != false;
	}
/**
 * Gets the database encoding
 *
 * @return string The database encoding
 */
	function getEncoding() {
		return mysqli_client_encoding($this->connection);
	}
	
        
        ///////////////////////
        
    /**
     * Generate a "drop table" statement for the given Schema object
     *
     * @param string $table. If specified only the table name given will be generated.
     * Otherwise, all tables defined in the schema are generated.
     * @return string
     * @access public
     */
    function dropSchema($table) {
        if (!isset($table) || $table === "") {
            trigger_error(__('Invalid table name', true), E_USER_WARNING);
            return null;
        }
        $out .= 'DROP TABLE IF EXISTS ' . $table . ";\n";

        return $out;
    }
    
    //////////////////////
        
        
        
	// --------------------------------------------------------------------

	/**
	 * Show column query
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	string
	 */
	function columnsSql($table = '')
	{
		return "SHOW COLUMNS FROM ".$table;
	}
}
?>