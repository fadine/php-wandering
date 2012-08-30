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
 
class WadoPostgres extends WadoSource {

	var $description = "PostgreSQL PADO Driver";

	var $_baseConfig = array(
		'connect'	=> 'pg_pconnect',
		'persistent' => true,
		'host' => 'localhost',
		'login' => 'root',
		'password' => '',
		'database' => 'wadb',
		'schema' => 'public',
		'port' => 5432,
		'encoding' => ''
	);

	var $columns = array(
		'primary_key' => array('name' => 'serial NOT NULL'),
		'string' => array('name'  => 'varchar', 'limit' => '255'),
		'text' => array('name' => 'text'),
		'integer' => array('name' => 'integer', 'formatter' => 'intval'),
		'float' => array('name' => 'float', 'formatter' => 'floatval'),
		'datetime' => array('name' => 'timestamp', 'format' => 'Y-m-d H:i:s', 'formatter' => 'date'),
		'timestamp' => array('name' => 'timestamp', 'format' => 'Y-m-d H:i:s', 'formatter' => 'date'),
		'time' => array('name' => 'time', 'format' => 'H:i:s', 'formatter' => 'date'),
		'date' => array('name' => 'date', 'format' => 'Y-m-d', 'formatter' => 'date'),
		'binary' => array('name' => 'bytea'),
		'boolean' => array('name' => 'boolean'),
		'number' => array('name' => 'numeric'),
		'inet' => array('name'  => 'inet')
	);

	var $startQuote = '"';

	var $endQuote = '"';
/**
 * Contains mappings of custom auto-increment sequences, if a table uses a sequence name
 * other than what is dictated by convention.
 *
 * @var array
 */
	var $_sequenceMap = array();
/**
 * Connects to the database using options in the given configuration array.
 *
 * @return True if successfully connected.
 */
	function connect() {

		$config = $this->config;
		//$connect = $config['connect'];
		//$this->connection = $connect("host='{$config['host']}' port='{$config['port']}' dbname='{$config['database']}' user='{$config['login']}' password='{$config['password']}'");
		
		$conn  = "host='{$config['host']}' port='{$config['port']}' dbname='{$config['database']}' ";
		$conn .= "user='{$config['login']}' password='{$config['password']}'";

		if (!$config['persistent']) {
			$this->connection = pg_connect($conn, PGSQL_CONNECT_FORCE_NEW);
		} else {
			$this->connection = pg_pconnect($conn);
		}
		$this->connected = false;

		if ($this->connection) {
			$this->connected = true;
			$this->_execute("SET search_path TO " . $config['schema']);
		} else {
			$this->connected = false;
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
		if ($this->hasResult()) {
			pg_free_result($this->_result);
		}
		if (is_resource($this->connection)) {
			$this->connected = !pg_close($this->connection);
		} else {
			$this->connected = false;
		}
		return !$this->connected;
	}

/**
 * Executes given SQL statement.
 *
 * @param string $sql SQL statement
 * @return resource Result resource identifier
 */
	function _execute($sql) {
		return pg_query($this->connection, $sql);
	}
/**
 * Returns an array of tables in the database. If there are no tables, an error is raised and the application exits.
 *
 * @return array Array of tablenames in the database
 */
	function listSources() {
		$cache = parent::listSources();

		if ($cache != null) {
			return $cache;
		}

		$schema = $this->config['schema'];
		$sql = "SELECT table_name as name FROM INFORMATION_SCHEMA.tables WHERE table_schema = '{$schema}';";
		$result = $this->fetchAll($sql);

		if (!$result) {
			return array();
		} else {
			$tables = array();

			foreach ($result as $item) {
				$tables[] = $item[0]['name'];
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
 * @param boolean $read Value to be used in READ or WRITE context
 * @return string Quoted and escaped
 * @todo Add logic that formats/escapes data based on column type
 */
	function value($data, $column = null, $read = true) {

		$parent = parent::value($data, $column);
		if ($parent != null) {
			return $parent;
		}

		if ($data === null) {
			return 'NULL';
		}
		if (empty($column)) {
			$column = $this->introspectType($data);
		}

		switch($column) {
			case 'inet':
			case 'float':
			case 'integer':
			case 'date':
			case 'datetime':
			case 'timestamp':
				if ($data === '') {
					return $read ? 'NULL' : 'DEFAULT';
				}
			case 'binary':
				$data = pg_escape_bytea($data);
			break;
			case 'boolean':
				if ($data === true || $data === 't' || $data === 'true') {
					return 'TRUE';
				} elseif ($data === false || $data === 'f' || $data === 'false') {
					return 'FALSE';
				}
				return (!empty($data) ? 'TRUE' : 'FALSE');
			break;
			default:
				$data = pg_escape_string($data);
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
	function begin() {
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
 * @return string Error message
 */
	function lastError() {
		$last_error = pg_last_error($this->connection);
		if ($last_error) {
			return $last_error;
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
			$return = pg_affected_rows($this->_result);
			return $return;
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
			$return = pg_num_rows($this->_result);
			return $return;
		}
		return false;
	}
/**
 * Returns the ID generated from the previous INSERT operation.
 *
 * @param string $source Name of the database table
 * @param string $field Name of the ID database field. Defaults to "id"
 * @return integer
 */
	function lastInsertId($source, $field = 'id') {
		foreach ($this->__descriptions[$source] as $name => $sourceinfo) {
			if (strcasecmp($name, $field) == 0) {
				break;
			}
		}

		if (isset($this->_sequenceMap[$source])) {
			$seq = $this->_sequenceMap[$source];
		} elseif (preg_match('/^nextval\(\'(\w+)\'/', $sourceinfo['default'], $matches)) {
			$seq = $matches[1];
		} else {
			$seq = "{$source}_{$field}_seq";
		}

		$res = $this->rawQuery("SELECT last_value AS max FROM \"{$seq}\"");
		$data = $this->fetchRow($res);
		return $data[0]['max'];
	}


	
/**
 * Prepares field names to be quoted by parent
 *
 * @param string $data
 * @return string SQL field
 */
	function name($data) {
		if (is_string($data)) {
			$data = str_replace('"__"', '__', $data);
		}
		return parent::name($data);
	}	
	
	

	
/**
 * Returns a limit statement in the correct format for the particular database.
 *
 * @param integer $limit Limit of results returned
 * @param integer $offset Offset from which to start results
 * @return string SQL limit/offset statement
 */
	function limit($limit, $offset = null) {
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
 * Gets the length of a database-native column description, or null if no length
 *
 * @param string $real Real database-layer column type (i.e. "varchar(255)")
 * @return int An integer representing the length of the column
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
		$num_fields = pg_num_fields($results);
		$index = 0;
		$j = 0;

		while ($j < $num_fields) {
			$columnName = pg_field_name($results, $j);

			if (strpos($columnName, '__')) {
				$parts = explode('__', $columnName);
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
		if ($row = pg_fetch_row($this->results)) {
			$resultRow = array();
			$i = 0;

			foreach ($row as $index => $field) {
				list($table, $column) = $this->map[$index];
				$resultRow[$table][$column] = $row[$index];
				$i++;
			}
			return $resultRow;
		} else {
			return false;
		}
	}
	
	
	
	function fetchRawResult() {
		if ($row = pg_fetch_row($this->results)) {
			$resultRow = array();
			$i = 0;

			foreach ($row as $index => $field) {
				list($table, $column) = $this->map[$index];
				$resultRow[$column] = $row[$index];
				$i++;
			}
			return $resultRow;
		} else {
			return false;
		}
	}
	
/**
 * Translates between PHP boolean values and PostgreSQL boolean values
 *
 * @param mixed $data Value to be translated
 * @param boolean $quote	True to quote value, false otherwise
 * @return mixed Converted boolean value
 */
	function boolean($data, $quote = true) {
		$result = null;

		if ($data === true || $data === false) {
			$result = $data;
		} elseif (is_string($data) && !is_numeric($data)) {
			if (strpos(strtolower($data), 't') !== false) {
				$result = true;
			} else {
				$result = false;
			}
		} else {
			$result = (bool)$data;
		}
		return $result;
	}
/**
 * Sets the database encoding
 *
 * @param mixed $enc Database encoding
 * @return boolean True on success, false on failure
 */
	function setEncoding($enc) {
		return pg_set_client_encoding($this->connection, $enc) == 0;
	}
/**
 * Gets the database encoding
 *
 * @return string The database encoding
 */
	function getEncoding() {
		return pg_client_encoding($this->connection);
	}


	
/**
 * Overrides DboSource::renderStatement to handle schema generation with Postgres-style indexes
 *
 * @param string $type
 * @param array $data
 * @return string
 */
	function renderStatement($type, $data) {
		switch (strtolower($type)) {
			case 'schema':
				extract($data);

				foreach ($indexes as $i => $index) {
					if (preg_match('/PRIMARY KEY/', $index)) {
						unset($indexes[$i]);
						$columns[] = $index;
						break;
					}
				}
				$join = array('columns' => ",\n\t", 'indexes' => "\n");

				foreach (array('columns', 'indexes') as $var) {
					if (is_array(${$var})) {
						${$var} = join($join[$var], array_filter(${$var}));
					}
				}
				return "CREATE TABLE {$table} (\n\t{$columns}\n);\n{$indexes}";
			break;
			default:
				return parent::renderStatement($type, $data);
			break;
		}
	}	

        
        ///////////////////////////////
        
        
        
        /**
        * Generate a Postgres-native column schema string
        *
        * @param array $column An array structured like the following:
        * array('name'=>'value', 'type'=>'value'[, options]),
        * where options can be 'default', 'length', or 'key'.
        * @return string
        */
    function buildColumn($column) {
        $col = $this->columns[$column['type']];
        if (!isset($col['length']) && !isset($col['limit'])) {
            unset($column['length']);
        }
        $out = preg_replace('/integer\([0-9]+\)/', 'integer', parent::buildColumn($column));
        $out = str_replace('integer serial', 'serial', $out);
        if (strpos($out, 'timestamp DEFAULT')) {
            if (isset($column['null']) && $column['null']) {
                $out = str_replace('DEFAULT NULL', '', $out);
            } else {
                $out = str_replace('DEFAULT NOT NULL', '', $out);
            }
        }
        if (strpos($out, 'DEFAULT DEFAULT')) {
            if (isset($column['null']) && $column['null']) {
                $out = str_replace('DEFAULT DEFAULT', 'DEFAULT NULL', $out);
            } elseif (in_array($column['type'], array('integer', 'float'))) {
                $out = str_replace('DEFAULT DEFAULT', 'DEFAULT 0', $out);
            } elseif ($column['type'] == 'boolean') {
                $out = str_replace('DEFAULT DEFAULT', 'DEFAULT FALSE', $out);
            }
        }
        return $out;
    }

    /**
     * Format indexes for create table
     *
     * @param array $indexes
     * @param string $table
     * @return string
     */
    function buildIndex($indexes, $table = null) {
        $join = array();
        if (!is_array($indexes)) {
            return array();
        }
        foreach ($indexes as $name => $value) {
            if ($name == 'PRIMARY') {
                $out = 'PRIMARY KEY (' . $this->name($value['column']) . ')';
            } else {
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
            }
            $join[] = $out;
        }
        return $join;
    }
        
        
        ///////////////////////////////
	
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
		return "SELECT column_name AS Field, data_type AS Type FROM information_schema.columns WHERE table_name ='".$table."'";
	}
}

?>