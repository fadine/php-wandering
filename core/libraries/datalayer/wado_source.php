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
 
  
class WadoSource {

/**
 * Are we connected to the DataSource?
 *
 * @var boolean
 * @access public
 */
	var $connected = false;
/**
 * Print full query debug info?
 *
 * @var boolean
 * @access public
 */
	var $fullDebug = false;
/**
 * Error description of last query
 *
 * @var unknown_type
 * @access public
 */
	var $error = null;
/**
 * String to hold how many rows were affected by the last SQL operation.
 *
 * @var string
 * @access public
 */
	var $affected = null;
/**
 * Number of rows in current resultset
 *
 * @var int
 * @access public
 */
	var $numRows = null;
/**
 * Time the last query took
 *
 * @var int
 * @access public
 */
	var $took = null;
/**
 * Enter description here...
 *
 * @var array
 * @access private
 */
	var $_result = null;
/**
 * Queries count.
 *
 * @var int
 * @access private
 */
	var $_queriesCnt = 0;
/**
 * Total duration of all queries.
 *
 * @var unknown_type
 * @access private
 */
	var $_queriesTime = null;
/**
 * Log of queries executed by this DataSource
 *
 * @var unknown_type
 * @access private
 */
	var $_queriesLog = array();
/**
 * Maximum number of items in query log, to prevent query log taking over
 * too much memory on large amounts of queries -- I we've had problems at
 * >6000 queries on one system.
 *
 * @var int Maximum number of queries in the queries log.
 * @access private
 */
	var $_queriesLogMax = 200;
/**
 * Caches serialzed results of executed queries
 *
 * @var array Maximum number of queries in the queries log.
 * @access private
 */
	var $_queryCache = array();
/**
 * The default configuration of a specific DataSource
 *
 * @var array
 * @access public
 */
	var $_baseConfig = array();
/**
 * Holds references to descriptions loaded by the DataSource
 *
 * @var array
 * @access private
 */
	var $__descriptions = array();
/**
 * Holds a list of sources (tables) contained in the DataSource
 *
 * @var array
 * @access protected
 */
	var $_sources = null;
/**
 * A reference to the physical connection of this DataSource
 *
 * @var array
 * @access public
 */
	var $connection = null;
/**
 * The DataSource configuration
 *
 * @var array
 * @access public
 */
	var $config = array();
/**
 * The DataSource configuration key name
 *
 * @var string
 * @access public
 */
	var $configKeyName = null;
/**
 * Whether or not this DataSource is in the middle of a transaction
 *
 * @var boolean
 * @access protected
 */
	var $_transactionStarted = false;
/**
 * Enter description here...
 *
 * @var boolean
 */
       var $cacheSources = true;
	   
	   
	   
/**
* List of table engine specific parameters used on table creating
*
* @var array
* @access public
*/
var $tableParameters = array();	   
	   
	   
	   
	   
	   


/**
 * Description string for this Database Data Source.
 *
 * @var unknown_type
 */
	var $description = "Database Data Source";
/**
 * index definition, standard cake, primary, index, unique
 *
 * @var array
 */
	var $index = array('PRI'=> 'primary', 'MUL'=> 'index', 'UNI'=>'unique');
/**
 * Enter description here...
 *
 * @var unknown_type
 */
	var $startQuote = null;
/**
 * Enter description here...
 *
 * @var unknown_type
 */
	var $endQuote = null;
/**
 * Enter description here...
 *
 * @var unknown_type
 */
	var $alias = 'AS ';


/**
 * The set of valid SQL operations usable in a WHERE statement
 *
 * @var array
 */
	var $__sqlOps = array('like', 'ilike', 'or', 'not', 'in', 'between', 'regexp', 'similar to');
/**
 * Constructor
 */
	function __construct($config = null, $autoConnect = true) {
		
		if (func_num_args() > 0) {
			$this->setConfig(func_get_arg(0));
		}
		
		$this->fullDebug = config_item('enable_debug');
		
		if ($autoConnect) {
			return $this->connect();
		} else {
			return true;
		}
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////
	
	
/**
 * Caches/returns cached results for child instances
 *
 * @return array
 */
	function listSources($data = null) {
		if ($this->cacheSources === false) {
			return null;
		}
		if ($this->_sources != null) {
			return $this->_sources;
		}

		if (Configure::read() > 0) {
			$expires = "+30 seconds";
		} else {
			$expires = "+999 days";
		}

		if ($data != null) {
			$data = serialize($data);
		}
		$filename = (isset($this->config['name_source'])?$this->config['name_source']:"unknown_source") . '_' . preg_replace("/[^A-Za-z0-9_-]/", "_", $this->config['database']) . '_list';
		$new = cache('models' . DS . $filename, $data, $expires);

		if ($new != null) {
			$new = unserialize($new);
			$this->_sources = $new;
		}
		return $new;
	}


/**
 * Begin a transaction
 *
 * @return boolean True
 */
	function begin() {
		return true;
	}
/**
 * Commit a transaction
 *
 * @return boolean True
 */
	function commit() {
		return true;
	}
/**
 * Rollback a transaction
 *
 * @return boolean True
 */
	function rollback() {
		return true;
	}

/**
 * Returns the ID generated from the previous INSERT operation.
 *
 * @param unknown_type $source
 * @return in
 */
	function lastInsertId($source = null) {
		return false;
	}
/**
 * Returns the ID generated from the previous INSERT operation.
 *
 * @param unknown_type $source
 * @return in
 */
	function lastNumRows($source = null) {
		return false;
	}
/**
 * Returns the ID generated from the previous INSERT operation.
 *
 * @param unknown_type $source
 * @return in
 */
	function lastAffected($source = null) {
		return false;
	}
/**
 * Returns true if the DataSource supports the given interface (method)
 *
 * @param string $interface The name of the interface (method)
 * @return boolean True on success
 */
	function isInterfaceSupported($interface) {
		$methods = get_class_methods(get_class($this));
		$methods = strtolower(implode('|', $methods));
		$methods = explode('|', $methods);
		$return = in_array(strtolower($interface), $methods);
		return $return;
	}
/**
 * Sets the configuration for the DataSource
 *
 * @param array $config The configuration array
 */
	function setConfig($config) {
		if (is_array($this->_baseConfig)) {
			$this->config = $this->_baseConfig;
			foreach ($config as $key => $val) {
				$this->config[$key] = $val;
			}
		}
	}
/**
 * Cache the DataSource description
 *
 * @param string $object The name of the object (model) to cache
 * @param mixed $data The description of the model, usually a string or array
 */
	function __cacheDescription($object, $data = null) {
		if ($this->cacheSources === false) {
			return null;
		}
		if (Configure::read() > 0) {
			$expires = "+15 seconds";
		} else {
			$expires = "+999 days";
		}

		if ($data !== null) {
			$this->__descriptions[$object] =& $data;
			$cache = serialize($data);
		} else {
			$cache = null;
		}
		$new = cache('models' . DS . (isset($this->config['name_source'])?$this->config['name_source']:"unknown_source") . '_' . $object, $cache, $expires);

		if ($new != null) {
			$new = unserialize($new);
		}
		return $new;
	}

	
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
/**
 * Reconnects to database server with optional new settings
 *
 * @param array $config An array defining the new configuration settings
 * @return boolean True on success, false on failure
 */
	function reconnect($config = null) {
		$this->disconnect();
		if ($config != null) {
			$this->config = array_merge($this->_baseConfig, $config);
		}
		return $this->connect();
	}
/**
 * Prepares a value, or an array of values for database queries by quoting and escaping them.
 *
 * @param mixed $data A value or an array of values to prepare.
 * @return mixed Prepared value or array of values.
 */
	function value($data, $column = null, $safe = false) {
		if (is_array($data) && !empty($data)) {
			return array_map(
				array(&$this, 'value'),
				$data, array_fill(0, count($data), $column), array_fill(0, count($data), $safe)
			);
		} elseif (is_object($data) && isset($data->type)) {
			if ($data->type == 'identifier') {
				return $this->name($data->value);
			} elseif ($data->type == 'expression') {
				return $data->value;
			}
		}

		if ($data === null || (is_array($data) && empty($data))) {
			return 'NULL';
		}
		
		if ($data === '') {
			return  "''";
		}

		if (empty($column)) {
			$column = $this->introspectType($data);
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
				$data = "'" . mysql_real_escape_string($data, $this->connection) . "'";
			break;
		}
		return $data;
	}
	
	
	
/**
 * Guesses the data type of an array
 *
 * @param string $value
 * @return void
 */
	public function introspectType($value) {
		if (!is_array($value)) {
			if ($value === true || $value === false) {
				return 'boolean';
			}
			if (is_float($value) && floatval($value) === $value) {
				return 'float';
			}
			if (is_int($value) && intval($value) === $value) {
				return 'integer';
			}
			if (is_string($value) && strlen($value) > 255) {
				return 'text';
			}
			return 'string';
		}

		$isAllFloat = $isAllInt = true;
		$containsFloat = $containsInt = $containsString = false;
		foreach ($value as $key => $valElement) {
			$valElement = trim($valElement);
			if (!is_float($valElement) && !preg_match('/^[\d]+\.[\d]+$/', $valElement)) {
				$isAllFloat = false;
			} else {
				$containsFloat = true;
				continue;
			}
			if (!is_int($valElement) && !preg_match('/^[\d]+$/', $valElement)) {
				$isAllInt = false;
			} else {
				$containsInt = true;
				continue;
			}
			$containsString = true;
		}

		if ($isAllFloat) {
			return 'float';
		}
		if ($isAllInt) {
			return 'integer';
		}

		if ($containsInt && !$containsString) {
			return 'integer';
		}
		return 'string';
	}
	
/**
 * Executes given SQL statement.
 *
 * @param string $sql SQL statement
 * @return unknown
 */
	function rawQuery($sql) {
		$this->took = $this->error = $this->numRows = false;
		return $this->execute($sql);
	}
/**
 * Queries the database with given SQL statement, and obtains some metadata about the result
 * (rows affected, timing, any errors, number of rows in resultset). The query is also logged.
 * If DEBUG is set, the log is shown all the time, else it is only shown on errors.
 *
 * @param string $sql
 * @return unknown
 */
	function execute($sql) {
	
		if (!function_exists('getMicrotime')) {
/**
 * Returns microtime for execution time checking
 *
 * @return float Microtime
 */
		function getMicrotime() {
			list($usec, $sec) = explode(" ", microtime());
			return ((float)$usec + (float)$sec);
		}
	}
	
	
		$t = getMicrotime();
		$this->_result = $this->_execute($sql);
		$this->affected = $this->lastAffected();
		$this->took = round((getMicrotime() - $t) * 1000, 0);
		$this->error = $this->lastError();
		$this->numRows = $this->lastNumRows($this->_result);

		
		if (true===$this->fullDebug) {
			$this->logQuery($sql);
		}
		
		
		
		if ($this->error && true===$this->fullDebug) {
			$this->showQuery($sql);
			return false;
		} else {
			return $this->_result;
		}
	}
/**
 * DataSource Query abstraction
 *
 * @return resource Result resource identifier
 */
	function query() {
		$args     = func_get_args();
		$fields   = null;
		$order    = null;
		$limit    = null;
		$page     = null;
		$recursive = null;

		if (count($args) == 1) {
			return $this->fetchAll($args[0]);

		} elseif (count($args) > 1 && (strpos(strtolower($args[0]), 'findby') === 0 || strpos(strtolower($args[0]), 'findallby') === 0)) {
			$params = $args[1];

			if (strpos(strtolower($args[0]), 'findby') === 0) {
				$all  = false;
				$field = Inflector::underscore(preg_replace('/findBy/i', '', $args[0]));
			} else {
				$all  = true;
				$field = Inflector::underscore(preg_replace('/findAllBy/i', '', $args[0]));
			}

			$or = (strpos($field, '_or_') !== false);
			if ($or) {
				$field = explode('_or_', $field);
			} else {
				$field = explode('_and_', $field);
			}
			$off = count($field) - 1;

			if (isset($params[1 + $off])) {
				$fields = $params[1 + $off];
			}

			if (isset($params[2 + $off])) {
				$order = $params[2 + $off];
			}

			if (!array_key_exists(0, $params)) {
				return false;
			}

			$c = 0;
			$query = array();
			foreach ($field as $f) {
				if (!is_array($params[$c]) && !empty($params[$c]) && $params[$c] !== true && $params[$c] !== false) {
					$query[$args[2]->alias . '.' . $f] = '= ' . $params[$c];
				} else {
					$query[$args[2]->alias . '.' . $f] = $params[$c];
				}
				$c++;
			}

			if ($or) {
				$query = array('OR' => $query);
			}

			if ($all) {

				if (isset($params[3 + $off])) {
					$limit = $params[3 + $off];
				}

				if (isset($params[4 + $off])) {
					$page = $params[4 + $off];
				}

				if (isset($params[5 + $off])) {
					$recursive = $params[5 + $off];
				}
				return $args[2]->findAll($query, $fields, $order, $limit, $page, $recursive);
			} else {
				if (isset($params[3 + $off])) {
					$recursive = $params[3 + $off];
				}
				return $args[2]->find($query, $fields, $order, $recursive);
			}
		} else {
			if (isset($args[1]) && $args[1] === true) {
				return $this->fetchAll($args[0], true);
			}
			return $this->fetchAll($args[0], false);
		}
	}
/**
 * Returns a row from current resultset as an array .
 *
 * @return array The fetched row as an array
 */
	function fetchRow($sql = null) {

		if (!empty($sql) && is_string($sql) && strlen($sql) > 5) {
			if (!$this->execute($sql)) {
				return null;
			}
		}

		if (is_resource($this->_result) || is_object($this->_result)) {
			$this->resultSet($this->_result);
			$resultRow = $this->fetchResult();
			return $resultRow;
		} else {
			return null;
		}
	}
	
	
	function fetchRawRow($sql = null) {

		if (!empty($sql) && is_string($sql) && strlen($sql) > 5) {
			if (!$this->execute($sql)) {
				return null;
			}
		}

		if (is_resource($this->_result) || is_object($this->_result)) {
			$this->resultSet($this->_result);
			$resultRow = $this->fetchRawResult();
			return $resultRow;
		} else {
			return null;
		}
	}
	
/**
 * Returns an array of all result rows for a given SQL query.
 * Returns false if no rows matched.
 *
 * @param string $sql SQL statement
 * @param boolean $cache Enables returning/storing cached query results
 * @return array Array of resultset rows, or false if no rows matched
 */
	function fetchAll($sql) {
		if ($this->execute($sql)) {
			$out = array();

			while ($item = $this->fetchRow()) {
				$out[] = $item;
			}
			return $out;

		} else {
			return false;
		}
	}
	
	
	function fetchRawAll($sql) {
		if ($this->execute($sql)) {
			$out = array();

			while ($item = $this->fetchRawRow()) {
				$out[] = $item;
			}
			return $out;

		} else {
			return false;
		}
	}
	

/**
 * Returns a quoted name of $data for use in an SQL statement.
 * Strips fields out of SQL functions before quoting.
 *
 * @param string $data
 * @return string SQL field
 */
	function name($data) {
		if (preg_match_all('/([^(]*)\((.*)\)(.*)/', $data, $fields)) {
			$fields = Set::extract($fields, '{n}.0');
			if (!empty($fields[1])) {
				if (!empty($fields[2])) {
					return $fields[1] . '(' . $this->name($fields[2]) . ')' . $fields[3];
				} else {
					return $fields[1] . '()' . $fields[3];
				}
			}
		}
		if ($data == '*') {
			return '*';
		}
		$data = $this->startQuote . str_replace('.', $this->endQuote . '.' . $this->startQuote, $data) . $this->endQuote;
		$data = str_replace($this->startQuote . $this->startQuote, $this->startQuote, $data);

		if (!empty($this->endQuote) && $this->endQuote == $this->startQuote) {
			$oddMatches = substr_count($data, $this->endQuote);
			if ($oddMatches % 2 == 1) {
				$data = trim($data, $this->endQuote);
			}
		}
		return str_replace($this->endQuote . $this->endQuote, $this->endQuote, $data);
	}
/**
 * Checks if it's connected to the database
 *
 * @return boolean True if the database is connected, else false
 */
	function isConnected() {
		return $this->connected;
	}

/**
 * Log given SQL query.
 *
 * @param string $sql SQL statement
 * @todo: Add hook to log errors instead of returning false
 */
	function logQuery($sql) {
		$this->_queriesCnt++;
		$this->_queriesTime += $this->took;
		$this->_queriesLog[] = array('query' => $sql,
					'error'		=> $this->error,
					'affected'	=> $this->affected,
					'numRows'	=> $this->numRows,
					'took'		=> $this->took
		);
		if (count($this->_queriesLog) > $this->_queriesLogMax) {
			array_pop($this->_queriesLog);
		}
		if ($this->error) {
			return false;
		}
	}
	
	
	/**
 * Outputs the contents of the queries log.
 *
 * @param boolean $sorted
 */
	function showLog($sorted = false) {
		if ($sorted) {
			$log = sortByKey($this->_queriesLog, 'took', 'desc', SORT_NUMERIC);
		} else {
			$log = $this->_queriesLog;
		}

		if ($this->_queriesCnt > 1) {
			$text = 'queries';
		} else {
			$text = 'query';
		}

		if (PHP_SAPI != 'cli') {
			print ("<table class=\"cake-sql-log\" id=\"dummeSqlLog_" . preg_replace('/[^A-Za-z0-9_]/', '_', uniqid(time(), true)) . "\" summary=\"Cake SQL Log\" cellspacing=\"0\" border = \"0\">\n<caption>({$this->configKeyName}) {$this->_queriesCnt} {$text} took {$this->_queriesTime} ms</caption>\n");
			print ("<thead>\n<tr><th>Nr</th><th>Query</th><th>Error</th><th>Affected</th><th>Num. rows</th><th>Took (ms)</th></tr>\n</thead>\n<tbody>\n");

			foreach ($log as $k => $i) {
				print ("<tr><td>" . ($k + 1) . "</td><td>" . sprintf($i['query']) . "</td><td>{$i['error']}</td><td style = \"text-align: right\">{$i['affected']}</td><td style = \"text-align: right\">{$i['numRows']}</td><td style = \"text-align: right\">{$i['took']}</td></tr>\n");
			}
			print ("</tbody></table>\n");
		} else {
			foreach ($log as $k => $i) {
				print (($k + 1) . ". {$i['query']} {$i['error']}\n");
			}
		}
	}
	
	
/**
 * Output information about an SQL query. The SQL statement, number of rows in resultset,
 * and execution time in microseconds. If the query fails, an error is output instead.
 *
 * @param string $sql Query to show information on.
 */
	function showQuery($sql) {
		$error = $this->error;
		if (strlen($sql) > 200 && !$this->fullDebug ) {
			$sql = substr($sql, 0, 200) . '[...]';
		}
		if (true===$this->fullDebug) {
			$out = null;
			if ($error) {
				trigger_error("<span style = \"color:Red;text-align:left\"><b>SQL Error:</b> {$this->error}</span>", E_USER_WARNING);
			} else {
				$out = ("<small>[Aff:{$this->affected} Num:{$this->numRows} Took:{$this->took}ms]</small>");
			}
			print(sprintf("<p style = \"text-align:left\"><b>Query:</b> %s %s</p>", $sql, $out));
		}
	}

	

/**
 * Renders a final SQL statement by putting together the component parts in the correct order
 *
 * @param string $type type of query being run.  e.g select, create, update, delete, schema, alter.
 * @param array $data Array of data to insert into the query.
 * @return string Rendered SQL expression to be run.
 */
	public function renderStatement($type, $data) {
		extract($data);
		$aliases = null;

		switch (strtolower($type)) {
			case 'select':
				return "SELECT {$fields} FROM {$table} {$alias} {$joins} {$conditions} {$group} {$order} {$limit}";
			break;
			case 'create':
				return "INSERT INTO {$table} ({$fields}) VALUES ({$values}) ".(isset($unique_duplicate)?$unique_duplicate:"");
			break;
			case 'update':
				if (!empty($alias)) {
					$aliases = "{$this->alias}{$alias} {$joins} ";
				}
				return "UPDATE {$table} {$aliases}SET {$fields} {$conditions}";
			break;
			case 'delete':
				if (!empty($alias)) {
					$aliases = "{$this->alias}{$alias} {$joins} ";
				}
				return "DELETE {$alias} FROM {$table} {$aliases}{$conditions}";
			break;
			case 'schema':
				foreach (array('columns', 'indexes', 'tableParameters') as $var) {
					if (is_array(${$var})) {
						${$var} = "\t" . join(",\n\t", array_filter(${$var}));
					} else {
						${$var} = '';
					}
				}
				if (trim($indexes) != '') {
					$columns .= ',';
				}
				return "CREATE TABLE {$table} (\n{$columns}{$indexes}){$tableParameters};";
			break;
			case 'alter':
			break;
		}
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

			if ($offset) {
				$rt .= ' ' . $offset . ',';
			}

			$rt .= ' ' . $limit;
			return $rt;
		}
		return null;
	}

	
/**
 * Disconnects database, kills the connection and says the connection is closed,
 * and if DEBUG is turned on, the log for this object is shown.
 *
 */
	function close() {
		if (true===$this->fullDebug) {
			$this->showLog();
		}
	
		$this->disconnect();
	}
/**
 * Checks if the specified table contains any record matching specified SQL
 *
  * @param string $sql SQL 
 * @return boolean True if the table has a matching record, else false
 */
	function hasAny($sql) {
		$out = $this->fetchRow($sql);

		if (is_array($out)) {
			count($out[0]);
		} else {
			return false;
		}
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
 * Translates between PHP boolean values and Database (faked) boolean values
 *
 * @param mixed $data Value to be translated
 * @return mixed Converted boolean value
 */
	function boolean($data) {
		if ($data === true || $data === false) {
			if ($data === true) {
				return 1;
			}
			return 0;

		} else {
			if (!empty($data)) {
				return true;
			}
			return false;
		}
	}
/**
 * Destructor. Closes connection to the database.
 *
 */
	function __destruct() {
		if ($this->_transactionStarted) {
			$null = null;
			$this->rollback();
		}
		
		if ($this->connected) {
			$this->close();
		}
		
	}
/**
 * Inserts multiple values into a table
 *
 * @param string $table
 * @param string $fields
 * @param array $values
 */
	public function insertMulti($table, $fields, $values) {
		$holder = implode(',', array_fill(0, count($fields), '?'));
		$fields = implode(', ', array_map(array(&$this, 'name'), $fields));

		$count = count($values);
		$sql = "INSERT INTO {$table} ({$fields}) VALUES ({$holder})";
		$statement = $this->_connection->prepare($sql);
		$this->begin();
		for ($x = 0; $x < $count; $x++) {
			$statement->execute($values[$x]);
			$statement->closeCursor();
		}
		return $this->commit();
	}


        
        /////////////////////////////////////////////

        
    /**
     * Generate a database-native schema for the given Schema object
     *
     * @param array $schema about columns define
     * @param string $tableName Optional. If specified only the table name given will be generated.
     * Otherwise, all tables defined in the schema are generated.
     * @return string
     * @access public
     */
    function createSchema($data_def, $tableName = null) {
        if (!is_array($data_def)) {
            trigger_error(__('Invalid table data define', true), E_USER_WARNING);
            return null;
        }
        $out = '';
        
        if ($tableName!=null && $tableName!="") {
            $cols = $colList = $indexes = $tableParameters = array();
            $primary = null;
            $table = $tableName;

            foreach ($data_def as $name => $col) {
                if (is_string($col)) {
                    $col = array('type' => $col);
                }
                if (isset($col['key']) && $col['key'] == 'primary') {
                    $primary = $name;
                }
                if ($name !== 'indexes' && $name !== 'tableParameters' && $name!=="deleteKey" && $name!=="tableKeyName") {
                    $col['name'] = $name;
                    if (!isset($col['type'])) {
                        $col['type'] = 'string';
                    }
                    $cols[] = $this->buildColumn($col);
                } elseif ($name == 'indexes') {
                    $indexes = array_merge($indexes, $this->buildIndex($col, $table));
                } elseif ($name == 'tableParameters') {
                    $tableParameters = array_merge($tableParameters, $this->buildTableParameters($col, $table));
                }
            }
            if (empty($indexes) && !empty($primary)) {
                $col = array('PRIMARY' => array('column' => $primary, 'unique' => 1));
                $indexes = array_merge($indexes, $this->buildIndex($col, $table));
            }
            $columns = $cols;
            $out .= $this->renderStatement('schema', compact('table', 'columns', 'indexes', 'tableParameters')) . "\n\n";
        }
        
        return $out;
    }
    
    
    /**
    * Generate a "drop table" statement for the given Schema object
    *
    * @param string $table. If specified only the table name given will be generated.
    * Otherwise, all tables defined in the schema are generated.
    * @return string
    * @access public
    */
        function dropSchema($table) {
            if (!isset($table) || $table ==="") {
                trigger_error(__('Invalid table name', true), E_USER_WARNING);
                return null;
            }
            $out .= 'DROP TABLE ' . $table . ";\n";
            
            return $out;
        }

        
        
    /**
     * Generate a database-native column schema string
     *
     * @param array $column An array structured like the following: array('name'=>'value', 'type'=>'value'[, options]),
     * where options can be 'default', 'length', or 'key'.
     * @return string
     * @access public
     */
    function buildColumn($column) {
        $name = $type = null;
        extract(array_merge(array('null' => true), $column));

        if (empty($name) || empty($type)) {
            trigger_error(__('Column name or type not defined in schema', true), E_USER_WARNING);
            return null;
        }

        if (!isset($this->columns[$type])) {
            trigger_error(sprintf(__('Column type %s does not exist', true), $type), E_USER_WARNING);
            return null;
        }

		if (!isset($column['key']) || $column['key'] != 'primary') {
			$real = $this->columns[$type];
			$out = $this->name($name) . ' ' . $real['name'];

			if (isset($real['limit']) || isset($real['length']) || isset($column['limit']) || isset($column['length'])) {
				if (isset($column['length'])) {
					$length = $column['length'];
				} elseif (isset($column['limit'])) {
					$length = $column['limit'];
				} elseif (isset($real['length'])) {
					$length = $real['length'];
				} else {
					$length = $real['limit'];
				}
				$out .= '(' . $length . ')';
			}
		}else{
			$out = $this->name($name);
		}
		

        if (($column['type'] == 'integer' || $column['type'] == 'float' ) && isset($column['default']) && $column['default'] === '') {
            $column['default'] = null;
        }
        $out = $this->_buildFieldParameters($out, $column, 'beforeDefault');

        if (isset($column['key']) && $column['key'] == 'primary' && $type == 'integer') {
            $out .= ' ' . $this->columns['primary_key']['name'];
        } elseif (isset($column['key']) && $column['key'] == 'primary') {
            $out .= ' NOT NULL';
        } elseif (isset($column['default']) && isset($column['null']) && $column['null'] == false) {
            $out .= ' DEFAULT ' . $this->value($column['default'], $type) . ' NOT NULL';
        } elseif (isset($column['default'])) {
            $out .= ' DEFAULT ' . $this->value($column['default'], $type);
        } elseif ($type !== 'timestamp' && !empty($column['null'])) {
            $out .= ' DEFAULT NULL';
        } elseif ($type === 'timestamp' && !empty($column['null'])) {
            $out .= ' NULL';
        } elseif (isset($column['null']) && $column['null'] == false) {
            $out .= ' NOT NULL';
        }
        if ($type == 'timestamp' && isset($column['default']) && strtolower($column['default']) == 'current_timestamp') {
            $out = str_replace(array("'CURRENT_TIMESTAMP'", "'current_timestamp'"), 'CURRENT_TIMESTAMP', $out);
        }
        $out = $this->_buildFieldParameters($out, $column, 'afterDefault');
        return $out;
    }
    
    
    /**
	* QuangHuy -- ham nay thuc te cha co tac dung dech gi vi tham so fieldParameters ko ton tai -- TODO
    * Build the field parameters, in a position
    *
    * @param string $columnString The partially built column string
    * @param array $columnData The array of column data.
    * @param string $position The position type to use. 'beforeDefault' or 'afterDefault' are common
    * @return string a built column with the field parameters added.
    * @access public
    */
    function _buildFieldParameters($columnString, $columnData, $position) {
        if (isset($this->fieldParameters)) {
			foreach ($this->fieldParameters as $paramName => $value) {
				if (isset($columnData[$paramName]) && $value['position'] == $position) {
					if (isset($value['options']) && !in_array($columnData[$paramName], $value['options'])) {
						continue;
					}
					$val = $columnData[$paramName];
					if ($value['quote']) {
						$val = $this->value($val);
					}
					$columnString .= ' ' . $value['value'] . $value['join'] . $val;
				}
			}
		}
        return $columnString;
    }
    
    
    
    /**
     * Format indexes for create table
     *
     * @param array $indexes
     * @param string $table
     * @return array
     * @access public
     */
    function buildIndex($indexes, $table = null) {
        $join = array();
        foreach ($indexes as $name => $value) {
            $out = '';
            if ($name == 'PRIMARY') {
                $out .= 'PRIMARY ';
                $name = null;
            } else {
                if (!empty($value['unique'])) {
                    $out .= 'UNIQUE ';
                }
                $name = $this->startQuote . $name . $this->endQuote;
            }
            if (is_array($value['column'])) {
                $out .= 'KEY ' . $name . ' (' . implode(', ', array_map(array(&$this, 'name'), $value['column'])) . ')';
            } else {
                $out .= 'KEY ' . $name . ' (' . $this->name($value['column']) . ')';
            }
            $join[] = $out;
        }
        return $join;
    }
    
    
    /**
    * Format parameters for create table
    *
    * @param array $parameters
    * @param string $table
    * @return array
    * @access public
    */
    function buildTableParameters($parameters, $table = null) {
        $result = array();
        foreach ($parameters as $name => $value) {
            if (isset($this->tableParameters[$name])) {
                if ($this->tableParameters[$name]['quote']) {
                    $value = $this->value($value);
                }
                $result[] = $this->tableParameters[$name]['value'] . $this->tableParameters[$name]['join'] . $value;
            }
        }
        return $result;
    }
    
    
///////////////////////////////////////






    /*
     * build condition
     *
     * @param <array> $condition
     * @example
     * $condition = array(
     *      array(
     *          'field'     =>  'member_id',
     *          'math'      =>  '>=',
     *          'value'     =>  '50'
     *      ),
     *      array(
     *          'keyword'   =>  'AND',
     *          'field'     =>  'member_dept_id',
     *          'math'      =>  'IN',
     *          'value'     =>  '(1,2,3)',
     *          'value_raw' =>  true
     *      )
     *      ),
     *
     * @return String
     * @example
     *      "WHERE member_id >= 50 AND member_dept_id IN (1,2,3)
     */

    protected function buildConditions($condition, $typeFields = array()) {
        $sql = '';
        $firstCondition = null;
        if (true === isset($condition[0])) {
            //create sql (contain ?) and array bin
            foreach ($condition as $key => $sub) {
                //is WHERE
                if (isset($sub['keyword']) && $sub['keyword'] == 'WHERE') {
                    $firstCondition = $sub;
                    continue;
                } elseif (!isset($sub['keyword']) && !isset($firstCondition)) {
                    $sub['keyword'] = 'WHERE';
                    $firstCondition = $sub;
                    continue;
                } elseif (!isset($sub['keyword']) && isset($firstCondition)) {
                    $sub['keyword'] = 'AND';
                }
                $sql .= (" " . $sub['keyword'] . " " . $sub['field'] . " " . $sub['math'] . " ") . ((isset($sub['value_raw']) && $sub['value_raw'] === true) ? $sub['value'] : ((isset($typeFields[$sub['field']])) ? ($this->value($sub['value'], $typeFields[$sub['field']], false)) : $sub['value']));
            }
            if (isset($firstCondition)) {
                $sql = $firstCondition['keyword'] . " " . $firstCondition['field'] . " " . $firstCondition['math'] . " " . ((isset($firstCondition['value_raw']) && $firstCondition['value_raw'] === true) ? $firstCondition['value'] : ((isset($typeFields[$firstCondition['field']])) ? ($this->value($firstCondition['value'], $typeFields[$firstCondition['field']], false)) : $firstCondition['value'])) . " " . $sql;
            } else {
                $ssql = "WHERE " . $sql;
            }
        }
        return $sql;
    }


	/**
	 * @params Array or String orders like $orders = array('my_name'=>'ASC', 'colum_id'=>'DESC') or like $orders = "my_name ASC, column DESC"
	**/
	protected function buildOrders($orders) {
        $sql = '';
		if (isset($orders)){
			if (!is_array(!$orders) && $orders!="") {
				$ordersCache = preg_replace('/,\s+/', ',',$orders);
				$ordersCache = explode(",", $ordersCache);
				$orders = array();
				foreach ($ordersCache as $item){
					$itemArr = explode(" ", $item);
					if (isset($itemArr[1])) {
						$orders[$itemArr[0]] = $itemArr[1];
					}else {
						$orders[$itemArr[0]] = 'ASC';
					}
				}
			}		
			
			if (count($orders)) {
				$str = "";
				foreach($orders as $k=>$v){
					$str .= ($k ." " .$v.", ");
				}
				if ($str!="") $str =  substr($str, 0, (strlen($str) - strlen($str) - 2));
				$sql = "ORDER BY ".$str;
			}
		}
		return $sql;
	}
	



        /**
         *
         * @param <String> $mainTable manin table name
         * @param <type> $joins
         * @return string
         */
        protected function buildJoins($mainTable, $joins) {
            $sql = '';
            if ($mainTable !="" && isset($joins[0])){
                foreach($joins as $k=>$v){
                        $sql .= " " . (isset($v['type'])?$v['type']:"") . " JOIN " . $k ." ON ".$mainTable.".".$v['main_key']." = ".$k.".".$v['join_key']." ";
                }

            }
            return $sql;
	}


	/**
         *
         * @param <type> $groups is an array of name all fields for group
         * @return string
         */
	protected function buildGroups($groups) {
        $sql = '';
		if (isset($groups)){
			if (!is_array(!$groups) && $groups!="") {
				$groups = preg_replace('/,\s+/', ',',$groups);
				$groups = explode(",", $groups);
			}
			
			if (count($groups)) {
				$str = "";
				foreach($groups as $v){
					$str .= ($v.", ");
				}
				if ($str!="") $str =  substr($str, 0, (strlen($str) - strlen($str) - 2));
				$sql = "GROUP BY ".$str;
			}
		}
		return $sql;
	}
	
	
	
//default for mysql
public function setLimit($sql, $limit = false, $offset = false){
	if ($limit) {
		$rt = '';
		if (!strpos(strtolower($limit), 'limit') || strpos(strtolower($limit), 'limit') === 0) {
			$rt = ' LIMIT';
		}

		if ($offset) {
			$rt .= ' ' . $offset . ',';
		}

		$rt .= ' ' . $limit;
		$sql = $sql . $rt;
	}
	return  $sql;
}

	
	
//quanghuy
function insert($myTable, $myBean, $uniqueKey = null)
{
	$query = array();
	$query['table'] = $myTable;
	
	$myTypeArr = $this->getColsVirtualTypeArr($myTable);
	$fields = array();
	$values = array();
	
	foreach ($myBean as $key => $val) {
		$fields[] = $key;
		$values[] = $this->value($val, $myTypeArr[$key], false);
	}
	$query['fields'] = implode(', ', $fields);
	$query['values'] = implode(', ', $values);
	
	if (isset($uniqueKey) && true === is_array($uniqueKey)) {
		$query['unique_duplicate'] = $this->getDuplicateSql($myBean, $uniqueKey);
	}
	
	
	if ($this->execute($this->renderStatement('create', $query))) {
		return true;
	} else {
		return false;
	}

}


    public function update($myTable, $myBean, $conditions = null) {

        $query = array();
		$table = $myTable;

		$myTypeArr = $this->getColsVirtualTypeArr($myTable);
        $myUpdateContent = "";

		foreach ($myBean as $key => $val) {
			$field = $key;
			$value = $this->value($val, $myTypeArr[$key], false);
					$myUpdateContent .=  $field." = ".$value.", ";
		}
        $fields = substr($myUpdateContent, 0, (strlen($myUpdateContent)-2));
        $conditions = $this->buildConditions($conditions, $myTypeArr);
        $alias = $joins = null;
        $query = compact('table', 'alias', 'joins', 'fields', 'conditions');
		
        if (!$this->execute($this->renderStatement('update', $query))) {
            return false;
        }
        return true;

    }


	
	public function delete($myTable, $conditions = null) {
		$alias = $joins = null;
		$table = $myTable;
		$conditions = $this->buildConditions($conditions);

		if ($conditions === false) {
			return false;
		}

		if ($this->execute($this->renderStatement('delete', compact('alias', 'table', 'joins', 'conditions'))) === false) {
			return false;
		}
		return true;
	}
	
	

	
	

/*
Ham truu tuong hoa: lay du lieu, tuong duong cau truy van select dang don gian
*Tham so:
	$$tTableName: ten doi tuong du lieu, co the hieu la ten bang
	$tNameProperties: mang ten thuoc tinh doi tuong, co the hieu la ten cua cac truong can truy van
	$$tWhereClause: chuoi dieu kien cua ham, hay cua cau truy van
*/
function simpleSelect($myTable, $myFields, $conditions = null, $orders = null, $groups = null, $limit = false, $offset = false){
	$returnArr = array();
	$table = $myTable;
	$alias = $joins = $order = $group = $limit = "";
	$fields = "";
	if (is_array($myFields)){
		$fields = implore(', ',$myFields);
	}else{
		$fields = $myFields;
	}
	$myTypeArr = $this->getColsVirtualTypeArr($myTable);
	
	$conditions = $this->buildConditions($conditions, $myTypeArr);
	$order = $this->buildOrders($orders);
	$group = $this->buildGroups($groups);
	
	$query = compact('table', 'alias', 'joins', 'fields', 'conditions', 'group', 'order', 'limit');
	$sql = $this->renderStatement('select',$query);
	$sql = $this->setLimit($sql, $limit, $offset);
	
	$returnArr = $this->fetchAll($sql);
        
	return $returnArr;
}	


function simpleRawSelect($myTable, $myFields, $conditions = null, $orders = null, $groups = null, $limit = false, $offset = false){
	$returnArr = array();
	$table = $myTable;
	$alias = $joins = $order = $group = $limit = "";
	$fields = "";
	if (is_array($myFields)){
		$fields = implore(', ',$myFields);
	}else{
		$fields = $myFields;
	}
	
	$myTypeArr = $this->getColsVirtualTypeArr($myTable);
	
	$conditions = $this->buildConditions($conditions, $myTypeArr);
	$order = $this->buildOrders($orders);
	$group = $this->buildGroups($groups);
	
	$query = compact('table', 'alias', 'joins', 'fields', 'conditions', 'group', 'order', 'limit');
	$sql = $this->renderStatement('select',$query);
	$sql = $this->setLimit($sql, $limit, $offset);
	
	$returnArr = $this->fetchRawAll($sql);
	return $returnArr;
}





function complexRawSelect($myTable, $myFields, $mJoins = null, $conditions = null, $orders = null, $groups = null, $limit = false, $offset = false){
	$returnArr = array();
	$table = $myTable;
	$alias = $joins = $order = $group = $limit = "";
	$fields = "";
	if (is_array($myFields)){
		$fields = implore(', ',$myFields);
	}else{
		$fields = $myFields;
	}
	
	$myTypeArr = $this->getColsVirtualTypeArr($myTable);
	
	$conditions = $this->buildConditions($conditions, $myTypeArr);
        $joins = $this->buildJoins($myTable, $mJoins);
	$order = $this->buildOrders($orders);
	$group = $this->buildGroups($groups);
	
	$query = compact('table', 'alias', 'joins', 'fields', 'conditions', 'group', 'order', 'limit');
	$sql = $this->renderStatement('select',$query);
	$sql = $this->setLimit($sql, $limit, $offset);
	
	$returnArr = $this->fetchRawAll($sql);
	return $returnArr;
}


/*
QuangHuy - 22/12/2008
Ham lay du lieu phuc hop
Tham so:
	$tArrTablesDesc la mang cua mang: Array('table_name'=>'table name or alias name','table_type'=>'main or left Join or right Join or ....','key_relation'=> 'field name in relation with other table', 'table_desc'=>'query and alias name or null'
	$tArrFieldNamesla mang cua mang:Arr('table_name'=>'name of table or alias', 'field_name'=>'name of field or aliasname')
	$tWhereClause
	$tAddClause
*/	
function getMyDataComplex($tArrTablesDesc, $tArrFieldNamesla, $tWhereClause = "", $tAddClause = "") {
	$mSQL = " SELECT ";
	
	$mFields = "";
	$mMainTable = "";
	$mKeyMain = "";
	$mOtherTable = "";
	
	if ($tArrFieldNamesla.length > 0) {
		foreach ($tArrFieldNamesla as $aMyField) {
			if ($aMyField['table_name']!="") {
				$mFields +=  ($aMyField['table_name'].".".$aMyField['field_name'].", ");
			} else {
				$mFields +=  ($aMyField['field_name'].", ");
			}
		}
	} else {
		$mFields += " *   ";
	}
	
	$mSQL .= substr($mFields, 0, (strlen($mFields)-2));
	
	foreach ($tArrTablesDesc as $aMyTable) {
		if ($aMyTable['table_type']=="main") {
			if ($aMyTable['table_desc']=="") $mMainTable = $aMyTable['table_name'];
			else $mMainTable = $aMyTable['table_desc'];
			$mKeyMain = $aMyTable['table_name'].".".$aMyTable['key_relation'];
		} else {
			if ($aMyTable['table_desc']=="") $mOtherTable .= " " . $aMyTable['table_type']." " . $aMyTable['table_name']. " ON __MYMAINKEY__ = ".$aMyTable['table_type'].".".$aMyTable['key_relation'];
			else $mOtherTable .=  " " . $aMyTable['table_type']. " " .  $aMyTable['table_desc']. " ON __MYMAINKEY__ = ".$aMyTable['table_type'].".".$aMyTable['key_relation'] . " ";
			
		}
	}
	
	$mOtherTable = str_replace("__MYMAINKEY__", $mKeyMain,$mOtherTable);
	
	if (($mMainTable!="")&&($mOtherTable)) $mSQL .= " FROM ".$mMainTable.$mOtherTable;
	
	if (strlen($tWhereClause) > 0) $mSQL += (" " + $tWhereClause + " ");
	if (strlen($tAddClause) > 0) $mSQL += (" " + $tAddClause + " ");
	
	//return $mSQL;
	return $this->execute($mSQL);
}



	/**
	 * @author Nowayforback <nowayforback@pachay.com>
	 */
	function getColVirtualType($atualType){
		$myVirtualType = 'string';
		foreach ($this->columns as $key=>$val) {
			$myPos = stripos($atualType, $val['name']);
			if ($myPos!==false) {
				$myVirtualType = $key;
				break;
			}
		}
		return $myVirtualType;
	}
	
	/**
	* @author Nowayforback <nowayforback@pachay.com>
	@ham nay chi co tac dung voi MySQL, MySQLi, ODBC
	*/
	function getColsVirtualTypeArr($tableName){
		$results = array();
	
		$cachePath = "data_layer".DS.(isset($this->config['name_source'])?$this->config['name_source']:"unknown_source").DS.$tableName.EXT;
		if (file_exists($cachePath)) {
			require($cachePath);
			$results = ${($tableName."Columns")};
			if (isset($results)) return $results;
		}
		
		$mySql = $this->columnsSql($tableName);
		if ($this->execute($mySql)) {
			while ($item = $this->fetchRawRow()) {
				$results[$item['Field']] = $this->getColVirtualType($item['Type']);
			}
		
			ob_start();
			var_export($results);
			$myContent = ob_get_contents();
			ob_end_clean();
			
			$myContent = '$'.($tableName."Columns = ").$myContent;
			cache($cachePath, $myContent);
		}
		
		return $results;
	}
	
	
	/**
	* @author Nowayforback <nowayforback@pachay.com>
	@ham nay chi co tac dung voi MySQL, MySQLi, ODBC
	*/
	function getDuplicateSql($myBean, $uniqueKey = array()) {
		//unset unique key
		foreach ($uniqueKey as $unique) {
			unset($myBean[$unique]);
		}
		//build update string
		$set = '';
		foreach ($myBean as $field => $value) {
			$set .= $field . " = '" . $this->escape($value) . "', ";
		}
		$set = substr($set, 0, -2); //trim last ","

		//merge sql
		$sql = " ON DUPLICATE KEY UPDATE " . $set;
		return $sql;	
	}

}
?>
