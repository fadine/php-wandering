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

class WadoOracle extends WadoSource {
/**
 * Enter description here...
 *
 * @var unknown_type
 * @access public
 */
	var $config;
/**
 * Enter description here...
 *
 * @var unknown_type
 * @access public
 */
	var $alias = '';
	
	
	/**
 * Sequence names as introspected from the database
 */
	var $_sequences = array();
	
/**
  * The name of the model's sequence
  *
  * @var unknown_type
  */
	var $sequence = '';
/**
 * Transaction in progress flag
 *
 * @var boolean
 */
	var $__transactionStarted = false;
/**
 * Enter description here...
 *
 * @var unknown_type
 * @access public
 */
	var $columns = array(
		'primary_key' => array('name' => 'number NOT NULL'),
		'string' => array('name' => 'varchar2', 'limit' => '255'),
		'text' => array('name' => 'varchar2'),
		'integer' => array('name' => 'numeric'),
		'float' => array('name' => 'float'),
		'datetime' => array('name' => 'date', 'format' => 'Y-m-d H:i:s'),
		'timestamp' => array('name' => 'date', 'format' => 'Y-m-d H:i:s'),
		'time' => array('name' => 'date', 'format' => 'Y-m-d H:i:s'),
		'date' => array('name' => 'date', 'format' => 'Y-m-d H:i:s'),
		'binary' => array('name' => 'bytea'),
		'boolean' => array('name' => 'boolean'),
		'number' => array('name' => 'numeric'),
		'inet' => array('name' => 'inet'));
/**
 * Enter description here...
 *
 * @var unknown_type
 * @access protected
 */
	var $connection;
/**
 * Enter description here...
 *
 * @var unknown_type
 * @access protected
 */
	var $_limit = -1;
/**
 * Enter description here...
 *
 * @var unknown_type
 * @access protected
 */
	var $_offset = 0;
/**
 * Enter description here...
 *
 * @var unknown_type
 * @access protected
 */
	var $_map;
/**
 * Enter description here...
 *
 * @var unknown_type
 * @access protected
 */
	var $_currentRow;
/**
 * Enter description here...
 *
 * @var unknown_type
 * @access protected
 */
	var $_numRows;
/**
 * Enter description here...
 *
 * @var unknown_type
 * @access protected
 */
	var $_results;
/**
 * Connects to the database using options in the given configuration array.
 *
 * @return boolean True if the database could be connected, else false
 * @access public
 */
 
 
 /**
 * Table-sequence map
 *
 * @var unknown_type
 */
	var $_sequenceMap = array();
 
	function connect() {
		$config = $this->config;
		//$connect = $config['connect'];
		$this->connected = false;
		if ($this->config['persistent']) {
			$connect = 'ociplogon';
		} else {
			$connect = 'ocilogon';
		}
		$this->connection = @$connect($config['login'], $config['password'], $config['database'], $config['charset']);

		if ($this->connection) {
			$this->connected = true;
			if (!empty($config['nls_sort'])) {
				$this->execute('ALTER SESSION SET NLS_SORT='.$config['nls_sort']);
			}

			if (!empty($config['nls_comp'])) {
				$this->execute('ALTER SESSION SET NLS_COMP='.$config['nls_comp']);
			}
			$this->execute("ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD HH24:MI:SS'");
		} else {
			$this->connected = false;
			$this->_setError();
		}
		return $this->connected;
	}
	
/**
 * Keeps track of the most recent Oracle error
 *
 */
	function _setError($source = null, $clear = false) {
		if ($source) {
			$e = ocierror($source);
		} else {
			$e = ocierror();
		}
		$this->_error = $e['message'];
		if ($clear) {
			$this->_error = null;
		}
	}	
	
	
/**
 * Sets the encoding language of the session
 *
 * @param string $lang language constant
 * @return bool
 */
	function setEncoding($lang) {
		if (!$this->execute('ALTER SESSION SET NLS_LANGUAGE='.$lang)) {
			return false;
		}
		return true;
	}
/**
 * Gets the current encoding language
 *
 * @return string language constant
 */
	function getEncoding() {
		$sql = 'SELECT VALUE FROM NLS_SESSION_PARAMETERS WHERE PARAMETER=\'NLS_LANGUAGE\'';
		if (!$this->execute($sql)) {
			return false;
		}

		if (!$row = $this->fetchRow()) {
			return false;
		}
		return $row[0]['VALUE'];
	}
/**
 * Disconnects from database.
 *
 * @return boolean True if the database could be disconnected, else false
 * @access public
 */
	function disconnect() {
		if ($this->connection) {
			$this->connected = !ocilogoff($this->connection);
			return !$this->connected;
		}
	}
/**
 * Scrape the incoming SQL to create the association map. This is an extremely
 * experimental method that creates the association maps since Oracle will not tell us.
 *
 * @param string $sql
 * @return false if sql is nor a SELECT
 * @access protected
 */
	function _scrapeSQL($sql) {
		$sql = str_replace("\"", '', $sql);
		$preFrom = preg_split('/\bFROM\b/', $sql);
		$preFrom = $preFrom[0];
		$find = array('SELECT');
		$replace = array('');
		$fieldList = trim(str_replace($find, $replace, $preFrom));
		$fields = preg_split('/,\s+/', $fieldList);//explode(', ', $fieldList);
		$lastTableName	= '';

		foreach($fields as $key => $value) {
			if ($value != 'COUNT(*) AS count') {
				if (preg_match('/\s+(\w+(\.\w+)*)$/', $value, $matches)) {
					$fields[$key]	= $matches[1];

					if (preg_match('/^(\w+\.)/', $value, $matches)) {
						$fields[$key]	= $matches[1] . $fields[$key];
						$lastTableName	= $matches[1];
					}
				}
				/*
				if (preg_match('/(([[:alnum:]_]+)\.[[:alnum:]_]+)(\s+AS\s+(\w+))?$/i', $value, $matches)) {
					$fields[$key]	= isset($matches[4]) ? $matches[2] . '.' . $matches[4] : $matches[1];
				}
				*/
			}
		}
		$this->_map = array();

		foreach($fields as $f) {
			$e = explode('.', $f);
			if (count($e) > 1) {
				$table = $e[0];
				$field = strtolower($e[1]);
			} else {
				$table = 0;
				$field = $e[0];
			}
			$this->_map[] = array($table, $field);
		}
	}
/**
 * Modify a SQL query to limit (and offset) the result set
 *
 * @param integer $limit Maximum number of rows to return
 * @param integer $offset Row to begin returning
 * @return modified SQL Query
 * @access public
 */
	function limit($limit = -1, $offset = 0) {
		$this->_limit = (int) $limit;
		$this->_offset = (int) $offset;
	}
/**
 * Returns number of rows in previous resultset. If no previous resultset exists,
 * this returns false.
 *
 * @return integer Number of rows in resultset
 * @access public
 */
	function lastNumRows() {
		return $this->_numRows;
	}
/**
 * Executes given SQL statement. This is an overloaded method.
 *
 * @param string $sql SQL statement
 * @return resource Result resource identifier or null
 * @access protected
 */
	function _execute($sql) {
		$this->_statementId = ociparse($this->connection, $sql);
		if (!$this->_statementId) {
			return null;
		}

		if ($this->__transactionStarted) {
			$mode = OCI_DEFAULT;
		} else {
			$mode = OCI_COMMIT_ON_SUCCESS;
		}

		if (!ociexecute($this->_statementId, $mode)) {
			return false;
		}

		switch(ocistatementtype($this->_statementId)) {
			case 'DESCRIBE':
			case 'SELECT':
				$this->_scrapeSQL($sql);
			break;
			default:
				return $this->_statementId;
			break;
		}

		if ($this->_limit >= 1) {
			ocisetprefetch($this->_statementId, $this->_limit);
		} else {
			ocisetprefetch($this->_statementId, 3000);
		}
		$this->_numRows = ocifetchstatement($this->_statementId, $this->_results, $this->_offset, $this->_limit, OCI_NUM | OCI_FETCHSTATEMENT_BY_ROW);
		$this->_currentRow = 0;
		$this->limit();
		return $this->_statementId;
	}
/**
 * Enter description here...
 *
 * @return unknown
 * @access public
 */
	function fetchRow() {
		if ($this->_currentRow >= $this->_numRows) {
			ocifreestatement($this->_statementId);
			$this->_map = null;
			$this->_results = null;
			$this->_currentRow = null;
			$this->_numRows = null;
			return false;
		}
		$resultRow = array();

		foreach($this->_results[$this->_currentRow] as $index => $field) {
			list($table, $column) = $this->_map[$index];

			if (strpos($column, ' count')) {
				$resultRow[0]['count'] = $field;
			} else {
				$resultRow[$table][$column] = $this->_results[$this->_currentRow][$index];
			}
		}
		$this->_currentRow++;
		return $resultRow;
	}
	
	
	
	
	function fetchRawResult() {
		if ($this->_currentRow >= $this->_numRows) {
			ocifreestatement($this->_statementId);
			$this->_map = null;
			$this->_results = null;
			$this->_currentRow = null;
			$this->_numRows = null;
			return false;
		}
		$resultRow = array();

		foreach($this->_results[$this->_currentRow] as $index => $field) {
			list($table, $column) = $this->_map[$index];

			if (strpos($column, ' count')) {
				$resultRow[0]['count'] = $field;
			} else {
				$resultRow[$column] = $this->_results[$this->_currentRow][$index];
			}
		}
		$this->_currentRow++;
		return $resultRow;
	}
	
	
/**
 * Fetches the next row from the current result set
 *
 * @return unknown
 */
	function fetchResult() {
		return $this->fetchRow();
	}
	
	
/**
 * Checks to see if a named sequence exists
 *
 * @param string $sequence
 * @return bool
 * @access public
 */
	function sequenceExists($sequence) {
		$sql = "SELECT SEQUENCE_NAME FROM USER_SEQUENCES WHERE SEQUENCE_NAME = '$sequence'";
		if (!$this->execute($sql)) {
			return false;
		}
		return $this->fetchRow();
	}
/**
 * Creates a database sequence
 *
 * @param string $sequence
 * @return bool
 * @access public
 */
	function createSequence($sequence) {
		$sql = "CREATE SEQUENCE $sequence";
		return $this->execute($sql);
	}
/**
 * Enter description here...
 *
 * @param unknown_type $table
 * @return unknown
 * @access public
 */
	function createTrigger($table) {
		$sql = "CREATE OR REPLACE TRIGGER pk_$table" . "_trigger BEFORE INSERT ON $table FOR EACH ROW BEGIN SELECT pk_$table.NEXTVAL INTO :NEW.ID FROM DUAL; END;";
		return $this->execute($sql);
	}
/**
 * Returns an array of tables in the database. If there are no tables, an error is
 * raised and the application exits.
 *
 * @return array tablenames in the database
 * @access public
 */
	function listSources() {
		$cache = parent::listSources();
		if ($cache != null) {
			return $cache;
		}
		$sql = 'SELECT view_name AS name FROM user_views UNION SELECT table_name AS name FROM user_tables';

		if (!$this->execute($sql)) {
			return false;
		}
		$sources = array();

		while($r = $this->fetchRow()) {
			$sources[] = $r[0]['name'];
		}
		parent::listSources($sources);
		return $sources;
	}

	
	
	
/**
 * This method should quote Oracle identifiers. Well it doesn't.
 * It would break all scaffolding and all of Cake's default assumptions.
 *
 * @param unknown_type $var
 * @return unknown
 * @access public
 */
	function name($var) {
		switch($var) {
			case '_create':
			case '_read':
			case '_update':
			case '_delete':
				return "\"$var\"";
			break;
			default:
				return $var;
			break;
		}
	}
/**
 * Begin a transaction
 *
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions).
 */
	function begin() {
		$this->__transactionStarted = true;
		return true;
	}
/**
 * Rollback a transaction
 *
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions,
 * or a transaction has not started).
 */
	function rollback() {
		return ocirollback($this->connection);
	}
/**
 * Commit a transaction
 *
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions,
 * or a transaction has not started).
 */
	function commit() {
		$this->__transactionStarted = false;
		return ocicommit($this->connection);
	}

/**
 * Returns a quoted and escaped string of $data for use in an SQL statement.
 *
 * @param string $data String to be prepared for use in an SQL statement
 * @return string Quoted and escaped
 * @access public
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

		switch($column) {
			case 'datetime':
				$data = date('Y-m-d H:i:s', strtotime($data));
				$data = "TO_DATE('$data', 'YYYY-MM-DD HH24:MI:SS')";
			    break;
			case 'integer' :
			case 'float' :
			case null :
				if (is_numeric($data)) {
					break;
				}
			default:
			    $data = str_replace("'", "''", $data);
			    $data = "'$data'";
			    break;
		}
		return $data;
	}
/**
 * Returns the ID generated from the previous INSERT operation.
 *
 * @param string
 * @return integer
 * @access public
 */
	function lastInsertId($source) {
		$sequence = isset($this->_sequenceMap[$source])?$this->_sequenceMap[$source]:((!empty($this->sequence)) ? $this->sequence : $source . '_seq');
		$sql = "SELECT $sequence.currval FROM dual";

		if (!$this->execute($sql)) {
			return false;
		}

		while($row = $this->fetchRow()) {
			return $row[$sequence]['currval'];
		}
		return false;
	}
/**
 * Returns a formatted error message from previous database operation.
 *
 * @return string Error message with error number
 * @access public
 */
	function lastError() {
		$errors = ocierror();

		if (($errors != null) && (isset($errors["message"]))) {
			return($errors["message"]);
		}
		return null;
	}
/**
 * Returns number of affected rows in previous database operation. If no previous operation exists, this returns false.
 *
 * @return int Number of affected rows
 * @access public
 */
	function lastAffected() {
		return $this->_statementId ? ocirowcount($this->_statementId): false;
	}

	
	
	// --------------------------------------------------------------------

	/**
	 * Show column query
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @access  public
	 * @param   string  the table name
	 * @return  string
	 */
	function columnsSql($table = '')
	{
		return "SELECT COLUMN_NAME AS FIELD, DATA_TYPE AS TYPE  FROM ALL_TAB_COLUMNS WHERE table_name = '$table'";
	}

        
		
	
/**
 * Renders a final SQL statement by putting together the component parts in the correct order
 *
 * @param string $type
 * @param array $data
 * @return string
 */
	function renderStatement($type, $data) {
		extract($data);
		$aliases = null;

		switch (strtolower($type)) {
			case 'select':
				return "SELECT {$fields} FROM {$table} {$alias} {$joins} {$conditions} {$group} {$order} {$limit}";
			break;
			case 'create':
				return "INSERT INTO {$table} ({$fields}) VALUES ({$values})";
			break;
			case 'update':
				if (!empty($alias)) {
					$aliases = "{$this->alias}{$alias} ";
				}
				return "UPDATE {$table} {$aliases}SET {$fields} {$conditions}";
			break;
			case 'delete':
				if (!empty($alias)) {
					$aliases = "{$this->alias}{$alias} ";
				}
				return "DELETE FROM {$table} {$aliases}{$conditions}";
			break;
			case 'schema':
				foreach (array('columns', 'indexes') as $var) {
					if (is_array(${$var})) {
						${$var} = "\t" . implode(",\n\t", array_filter(${$var}));
					}
				}
				if (trim($indexes) != '') {
					$columns .= ',';
				}
				return "CREATE TABLE {$table} (\n{$columns}{$indexes})";
			break;
			case 'alter':
				break;
		}
	}
		
	
		

    public function setLimit($sql, $limit = false, $offset = 0){
	if ($limit) {
            
//            $sql = preg_replace(array('/\n/', '/\t/'), array(' ', ' '),$sql);
//
//            $myFromPos = stripos($sql, ' from ');
//            $myFromPos = stripos($sql, ' from ');
//
//            if ($mySelectPos===false) return $sql;
//            $sql = substr($sql, $mySelectPos,7)."";

            //oracle nha que vai chuong
            

            $sql = "SELECT t2.*
            FROM (
                SELECT t1.*, ROWNUM AS \"pa_count\"
                FROM (
                    " . $sql . "
                ) t1
            ) t2
            WHERE z2.\"pa_count\" BETWEEN " . ($offset+1) . " AND " . ($offset+$limit);
	}
	return  $sql;
    }



    function getDuplicateSql($myBean, $uniqueKey = array()) {
            //unset unique key

            //merge sql
            $sql = " MERGE INTO movie_ratings mr
			USING (
			  SELECT rating, mid, aid
			  WHERE mid = 1 AND aid = 3) mri
			ON (mr.movie_ratings_id = mri.movie_ratings_id)

			WHEN MATCHED THEN
			  UPDATE SET mr.rating = 8 WHERE mr.mid = 1 AND mr.aid = 3

			WHEN NOT MATCHED THEN
			  INSERT (mr.rating, mr.mid, mr.aid)
			  VALUES (1, 3, 8)  ";
            return $sql;
    }


	
}

?>