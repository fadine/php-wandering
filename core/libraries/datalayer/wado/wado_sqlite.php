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
 
class WadoSqlite extends WadoSource {

/**
 * Enter description here...
 *
 * @var unknown_type
 */
	var $description = "SQLite WADO Driver";
/**
 * Enter description here...
 *
 * @var unknown_type
 */
	var $startQuote = '"';
/**
 * Enter description here...
 *
 * @var unknown_type
 */
	var $endQuote = '"';
	
/**
 * Keeps the transaction statistics of CREATE/UPDATE/DELETE queries
 *
 * @var array
 * @access protected
 */
	var $_queryStats = array();	
	
/**
 * Base configuration settings for SQLite driver
 *
 * @var array
 */
	var $_baseConfig = array(
		'persistent' => true,
		'database' => null,
		'connect' => 'sqlite_popen'
	);
/**
 * SQLite column definition
 *
 * @var array
 */
	var $columns = array(
		'primary_key' => array('name' => 'integer primary key'),
		'string' => array('name' => 'varchar', 'limit' => '255'),
		'text' => array('name' => 'text'),
		'integer' => array('name' => 'integer', 'limit' => '11', 'formatter' => 'intval'),
		'float' => array('name' => 'float', 'formatter' => 'floatval'),
		'datetime' => array('name' => 'timestamp', 'format' => 'YmdHis', 'formatter' => 'date'),
		'timestamp' => array('name' => 'timestamp', 'format' => 'YmdHis', 'formatter' => 'date'),
		'time' => array('name' => 'timestamp', 'format' => 'His', 'formatter' => 'date'),
		'date' => array('name' => 'date', 'format' => 'Ymd', 'formatter' => 'date'),
		'binary' => array('name' => 'blob'),
		'boolean' => array('name' => 'integer', 'limit' => '1')
	);
/**
 * Connects to the database using config['database'] as a filename.
 *
 * @param array $config Configuration array for connecting
 * @return mixed
 */
	function connect() {
		$config = $this->config;
		$this->connection = $config['connect']($config['database']);
		$this->connected = is_resource($this->connection);

		if ($this->connected) {
			$this->_execute('PRAGMA count_changes = 1;');
		}
		return $this->connected;
	}
/**
 * Disconnects from database.
 *
 * @return boolean True if the database could be disconnected, else false
 */
	function disconnect() {
		@sqlite_close($this->connection);
		$this->connected = false;
		return $this->connected;
	}
/**
 * Executes given SQL statement.
 *
 * @param string $sql SQL statement
 * @return resource Result resource identifier
 */
	function _execute($sql) {
		$result = sqlite_query($this->connection, $sql);

		if (preg_match('/^(INSERT|UPDATE|DELETE)/', $sql)) {
			$this->resultSet($result);
			list($this->_queryStats) = $this->fetchResult();
		}
		return $result;
	}
	
/**
 * Overrides DboSource::execute() to correctly handle query statistics
 *
 * @param string $sql
 * @return unknown
 */
	function execute($sql) {
		$result = parent::execute($sql);
		$this->_queryStats = array();
		return $result;
	}	
	
/**
 * Returns an array of tables in the database. If there are no tables, an error is raised and the application exits.
 *
 * @return array Array of tablenames in the database
 */
	function listSources() {
		$db = $this->config['database'];
		$this->config['database'] = basename($this->config['database']);

		$cache = parent::listSources();
		if ($cache != null) {
			return $cache;
		}

		$result = $this->fetchAll("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;");

		if (!$result || empty($result)) {
			return array();
		} else {
			$tables = array();
			foreach ($result as $table) {
				$tables[] = $table[0]['name'];
			}
			parent::listSources($tables);

			$this->config['database'] = $db;
			return $tables;
		}
		$this->config['database'] = $db;
		return array();
	}

/**
 * Returns a quoted and escaped string of $data for use in an SQL statement.
 *
 * @param string $data String to be prepared for use in an SQL statement
 * @return string Quoted and escaped
 */
	function value ($data, $column = null, $safe = false) {
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
			default:
				$data = sqlite_escape_string($data);
			break;
		}
		return "'" . $data . "'";
	}
/**
 * Begin a transaction
 *
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions).
 */
	function begin () {
		if (parent::begin()) {
			if ($this->execute('BEGIN')) {
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
	function commit () {
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
	function rollback () {
		if (parent::rollback()) {
			return $this->execute('ROLLBACK');
		}
		return false;
	}

/**
 * Returns a formatted error message from previous database operation.
 *
 * @return string Error message
 */
	function lastError() {
		$error = sqlite_last_error($this->connection);
		if ($error) {
			return $error.': '.sqlite_error_string($error);
		}
		return null;
	}
/**
 * Returns number of affected rows in previous database operation. If no previous operation exists, this returns false.
 *
 * @return integer Number of affected rows
 */
	function lastAffected() {
		if ($this->_result) {
			return sqlite_changes($this->connection);
		}
		return false;
	}
/**
 * Returns number of rows in previous resultset. If no previous resultset exists,
 * this returns false.
 *
 * @return integer Number of rows in resultset
 */
	function lastNumRows() {
		if ($this->_result) {
			sqlite_num_rows($this->_result);
		}
		return false;
	}
/**
 * Returns the ID generated from the previous INSERT operation.
 *
 * @return int
 */
	function lastInsertId() {
		return sqlite_last_insert_rowid($this->connection);
	}

/**
 * Enter description here...
 *
 * @param unknown_type $results
 */
	function resultSet(&$results) {
		$this->results =& $results;
		$this->map = array();
		$num_fields = sqlite_num_fields($results);
		$index = 0;
		$j = 0;

		while ($j < $num_fields) {
			$columnName = str_replace('"', '', sqlite_field_name($results, $j));

			if (strpos($columnName, '.')) {
				$parts = explode('.', $columnName);
				$this->map[$index++] = array($parts[0], $parts[1]);
			} else {
				$this->map[$index++] = array(0, $columnName);
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
		if ($row = sqlite_fetch_array($this->results, SQLITE_ASSOC)) {
			$resultRow = array();
			$i = 0;

			foreach ($row as $index => $field) {
				if (strpos($index, '.')) {
					list($table, $column) = explode('.', str_replace('"', '', $index));
					$resultRow[$table][$column] = $row[$index];
				} else {
					$resultRow[0][str_replace('"', '', $index)] = $row[$index];
				}
				$i++;
			}
			return $resultRow;
		} else {
			return false;
		}
	}
	
	
		function fetchRawResult() {
		if ($row = sqlite_fetch_array($this->results, SQLITE_ASSOC)) {
			$resultRow = array();
			$i = 0;

			foreach ($row as $index => $field) {
				if (strpos($index, '.')) {
					list($table, $column) = explode('.', str_replace('"', '', $index));
					$resultRow[$column] = $row[$index];
				} else {
					$resultRow[0][str_replace('"', '', $index)] = $row[$index];
				}
				$i++;
			}
			return $resultRow;
		} else {
			return false;
		}
	}
/**
 * Returns a limit statement in the correct format for the particular database.
 *
 * @param integer $limit Limit of results returned
 * @param integer $offset Offset from which to start results
 * @return string SQL limit/offset statement
 */
	function limit ($limit, $offset = null) {
		if ($limit) {
			$rt = '';
			if (!strpos(strtolower($limit), 'limit') || strpos(strtolower($limit), 'limit') === 0) {
				$rt = ' LIMIT';
			}
			$rt .= ' ' . $limit;
			if ($offset) {
				$rt .= ' OFFSET ' . $offset;
			}
			return $rt;
		}
		return null;
	}
	

	
/**
 * Sets the database encoding
 *
 * @param string $enc Database encoding
 */
	function setEncoding($enc) {
		if (!in_array($enc, array("UTF-8", "UTF-16", "UTF-16le", "UTF-16be"))) {
			return false;
		}
		return $this->_execute("PRAGMA encoding = \"{$enc}\"") !== false;
	}

/**
 * Gets the database encoding
 *
 * @return string The database encoding
 */
	function getEncoding() {
		return $this->fetchRow('PRAGMA encoding');
	}


/**
 * Overrides DboSource::renderStatement to handle schema generation with SQLite-style indexes
 *
 * @param string $type
 * @param array $data
 * @return string
 */
	function renderStatement($type, $data) {
		switch (strtolower($type)) {
			case 'schema':
				extract($data);

				foreach (array('columns', 'indexes') as $var) {
					if (is_array(${$var})) {
						${$var} = "\t" . join(",\n\t", array_filter(${$var}));
					}
				}
				return "CREATE TABLE {$table} (\n{$columns});\n{$indexes}";
			break;
			default:
				return parent::renderStatement($type, $data);
			break;
		}
	}	
	///////////////////////////////
        
        
        /**
        * Generate a database-native column schema string
        *
        * @param array $column An array structured like the following: array('name'=>'value', 'type'=>'value'[, options]),
        * where options can be 'default', 'length', or 'key'.
        * @return string
        */
    function buildColumn($column) {
        $name = $type = null;
        $column = array_merge(array('null' => true), $column);
        extract($column);

        if (empty($name) || empty($type)) {
            trigger_error(__('Column name or type not defined in schema', true), E_USER_WARNING);
            return null;
        }

        if (!isset($this->columns[$type])) {
            trigger_error(sprintf(__('Column type %s does not exist', true), $type), E_USER_WARNING);
            return null;
        }

        $real = $this->columns[$type];
        $out = $this->name($name) . ' ' . $real['name'];
        if (isset($column['key']) && $column['key'] == 'primary' && $type == 'integer') {
            return $this->name($name) . ' ' . $this->columns['primary_key']['name'];
        }
        return parent::buildColumn($column);
    }
        
        
        /**
        * Removes redundant primary key indexes, as they are handled in the column def of the key.
        *
        * @param array $indexes
        * @param string $table
        * @return string
        */
        function buildIndex($indexes, $table = null) {
            $join = array();

            foreach ($indexes as $name => $value) {

                if ($name == 'PRIMARY') {
                    continue;
                }
                $out = 'CREATE ';

                if (!empty($value['unique'])) {
                    $out .= 'UNIQUE ';
                }
                if (is_array($value['column'])) {
                    $value['column'] = implode(', ', array_map(array(&$this, 'name'), $value['column']));
                } else {
                    $value['column'] = $this->name($value['column']);
                }
                $out .= "INDEX {$name} ON {$table}({$value['column']});";
                $join[] = $out;
            }
            return $join;
        }
        
        
        //////////////////////////////
	
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
		// Not supported
		return FALSE;
	}
}

?>