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

static $config_item = array();


// ------------------------------------------------------------------------

/**
* Determines if the current version of PHP is greater then the supplied value
*
* Since there are a few places where we conditionally test for PHP > 5
* we'll set a static variable.
*
* @access	public
* @param	string
* @return	bool
*/
function is_php($version = '5.0.0')
{
	static $_is_php;
	$version = (string)$version;
	
	if ( ! isset($_is_php[$version]))
	{
		$_is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
	}

	return $_is_php[$version];
}

// ------------------------------------------------------------------------

/**
 * Tests for file writability
 *
 * is_writable() returns TRUE on Windows servers when you really can't write to 
 * the file, based on the read-only attribute.  is_writable() is also unreliable
 * on Unix servers if safe_mode is on. 
 *
 * @access	private
 * @return	void
 */
function is_really_writable($file)
{	
	// If we're on a Unix server with safe_mode off we call is_writable
	if (DS == '/' AND @ini_get("safe_mode") == FALSE)
	{
		return is_writable($file);
	}

	// For windows servers and safe_mode "on" installations we'll actually
	// write a file then read it.  Bah...
	if (is_dir($file))
	{
		$file = rtrim($file, '/').'/'.md5(rand(1,100));

		if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		@chmod($file, DIR_WRITE_MODE);
		@unlink($file);
		return TRUE;
	}
	elseif (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
	{
		return FALSE;
	}

	fclose($fp);
	return TRUE;
}

// ------------------------------------------------------------------------

/**
* Class registry
*
* This function acts as a singleton plus factory.  If the requested class does not
* exist it is instantiated and set to a static variable.  If it has
* previously been instantiated the variable is returned.
*
* @access	public
* @param	string	the class name being requested
* @param	bool	optional flag that lets classes get loaded but not instantiated
* @return	object
*/
function &load_class($class, $instantiate = TRUE)
{
	static $objects = array();
	$fileExist = false;
	$name = strtolower($class);
	$name2 = classNameToFileName($class);

	// Does the class exist?  If so, we're done...
	if (isset($objects[$class]))
	{
		return $objects[$class];
	}

	// load a file from input param and with config library_folders (array or singale string)	
	$myFolders = config_item('library_folders');
	
	if (is_array($myFolders)) {
		foreach ($myFolders as $folder) {
			if (file_exists(WAPATH_BASE.DS.$folder.DS.$class.EXT))
			{
				require_once(WAPATH_BASE.DS.$folder.DS.$class.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$name.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.$name.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$name2.EXT)) {
				require_once(WAPATH_BASE.DS.$folder.DS.$name2.EXT);
				$fileExist = true;
				break;
			}
		}
	}elseif (($myFolders!==false)&&($myFolders!="")) {
		
		if (file_exists(WAPATH_BASE.DS.$myFolders.DS.$class.EXT))
		{
			require_once(WAPATH_BASE.DS.$myFolders.DS.$class.EXT);
			$fileExist = true;
		}elseif (file_exists(WAPATH_BASE.DS.$myFolders.DS.$name.EXT)){
			require_once(WAPATH_BASE.DS.$myFolders.DS.$name.EXT);
			$fileExist = true;
		}elseif (file_exists(WAPATH_BASE.DS.$myFolders.DS.$name2.EXT)) {
			require_once(WAPATH_BASE.DS.$myFolders.DS.$name2.EXT);
			$fileExist = true;
		}
	}

	if ($instantiate == FALSE)
	{
		$myTrue = TRUE;
		return $myTrue;
	}


	if (!$fileExist) 
		log_message('error', 'Class File Not Found --> '.$class.EXT);


	//convert first char to UPPERCASE
	$name = ucwords($class);
	$name2 = fileNameToClassName($class);

	if (class_exists($class)) {
		$objects[$class] =& instantiate_class(new $class());
		return $objects[$class];
	}elseif(class_exists($name)) {
		$objects[$class] =& instantiate_class(new $name());
		return $objects[$class];
	}elseif(class_exists($name2)) {
		$objects[$class] =& instantiate_class(new $name2());
		return $objects[$class];
	}else{
		$myFalse = FALSE;
		return $myFalse;
	}
}

/**
 * Instantiate Class
 *
 * Returns a new class object by reference, used by load_class() and the DB class.
 * Required to retain PHP 4 compatibility and also not make PHP 5.3 cry.
 *
 * Use: $obj =& instantiate_class(new Foo());
 * 
 * @access	public
 * @param	object
 * @return	object
 */
function &instantiate_class(&$class_object)
{
	return $class_object;
}

/**
* Loads the main config.php file
*
* @access	private
* @return	array
*/
function &get_config()
{
	static $main_conf;

	if ( ! isset($main_conf))
	{
		if ( ! file_exists(WAPATH_CONFIG.'config'.EXT))
		{
			exit('The configuration file config'.EXT.' does not exist.');
		}

		require(WAPATH_CONFIG.'config'.EXT);

		if ( ! isset($config) OR ! is_array($config))
		{
			exit('Your config file does not appear to be formatted correctly.');
		}

		$main_conf[0] =& $config;
	}
	return $main_conf[0];
}

/**
* Gets a config item
*
* @access	public
* @return	mixed
*/
function config_item($item)
{
	global $config_item;

	if ( ! isset($config_item[$item]))
	{
		$config =& get_config();

		if ( ! isset($config[$item]))
		{
			return false;
		}
		$config_item[$item] = $config[$item];
	}

	return $config_item[$item];
}


function load_other_config($file=''){
	global $config_item;

	$file = ($file == '') ? 'config' : str_replace(EXT, '', $file);
	
	if ( ! file_exists(WAPATH_CONFIG.$file.EXT))
	{
		show_error('The configuration file '.$file.EXT.' does not exist.');
		if ($fail_gracefully === true)
		{
			return false;
		}
	}

	include(WAPATH_CONFIG.$file.EXT);

	if ( ! isset($config) OR ! is_array($config))
	{
		if ($fail_gracefully === true)
		{
			return false;
		}
		show_error('Your '.$file.EXT.' file does not appear to contain a valid configuration array.');
	}
	
	$config_item = array_merge($config_item, $config);

	unset($config);
	log_message('debug', 'Config file loaded: '.WAPATH_CONFIG.$file.EXT);
	return true;
}

/**
* Error Handler
*
* This function lets us invoke the exception class and
* display errors using the standard error template located
* in application/errors/errors.php
* This function will send the error page directly to the
* browser and exit.
*
* @access	public
* @return	void
*/
function show_error($message, $status_code = 500)
{
	$error =& load_class('WaErrors');
	echo $error->show_error('An Error Was Encountered', $message, 'error_general', $status_code);
	exit;
}


/**
* 404 Page Handler
*
* This function is similar to the show_error() function above
* However, instead of the standard error template it displays
* 404 errors.
*
* @access	public
* @return	void
*/
function show_404($page = '')
{
	$error =& load_class('WaErrors');
	$error->show_404($page);
	exit;
}


/**
* Error Logging Interface
*
* We use this as a simple mechanism to access the logging
* class and send messages to be logged.
*
* @access	public
* @return	void
*/
function log_message($level = 'error', $message, $php_error = FALSE)
{
	static $LOG;
	
	$config =& get_config();
	if ($config['log_threshold'] == 0)
	{
		return;
	}

	$LOG =& load_class('WaLog');
	$LOG->write_log_file($level, $message, $php_error);
}


/**
 * Set HTTP Status Header
 *
 * @access	public
 * @param	int 	the status code
 * @param	string	
 * @return	void
 */
function set_status_header($code = 200, $text = '')
{
	$stati = array(
						200	=> 'OK',
						201	=> 'Created',
						202	=> 'Accepted',
						203	=> 'Non-Authoritative Information',
						204	=> 'No Content',
						205	=> 'Reset Content',
						206	=> 'Partial Content',

						300	=> 'Multiple Choices',
						301	=> 'Moved Permanently',
						302	=> 'Found',
						304	=> 'Not Modified',
						305	=> 'Use Proxy',
						307	=> 'Temporary Redirect',

						400	=> 'Bad Request',
						401	=> 'Unauthorized',
						403	=> 'Forbidden',
						404	=> 'Not Found',
						405	=> 'Method Not Allowed',
						406	=> 'Not Acceptable',
						407	=> 'Proxy Authentication Required',
						408	=> 'Request Timeout',
						409	=> 'Conflict',
						410	=> 'Gone',
						411	=> 'Length Required',
						412	=> 'Precondition Failed',
						413	=> 'Request Entity Too Large',
						414	=> 'Request-URI Too Long',
						415	=> 'Unsupported Media Type',
						416	=> 'Requested Range Not Satisfiable',
						417	=> 'Expectation Failed',

						500	=> 'Internal Server Error',
						501	=> 'Not Implemented',
						502	=> 'Bad Gateway',
						503	=> 'Service Unavailable',
						504	=> 'Gateway Timeout',
						505	=> 'HTTP Version Not Supported'
					);

	if ($code == '' OR ! is_numeric($code))
	{
		show_error('Status codes must be numeric', 500);
	}

	if (isset($stati[$code]) AND $text == '')
	{				
		$text = $stati[$code];
	}
	
	if ($text == '')
	{
		show_error('No status text available.  Please check your status code number or supply your own message text.', 500);
	}
	
	$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

	if (substr(php_sapi_name(), 0, 3) == 'cgi')
	{
		header("Status: {$code} {$text}", TRUE);
	}
	elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0')
	{
		header($server_protocol." {$code} {$text}", TRUE, $code);
	}
	else
	{
		header("HTTP/1.1 {$code} {$text}", TRUE, $code);
	}
}


/**
* Exception Handler
*
* This is the custom exception handler that is declaired at the top
* of Codeigniter.php.  The main reason we use this is permit
* PHP errors to be logged in our own log files since we may
* not have access to server logs. Since this function
* effectively intercepts PHP errors, however, we also need
* to display errors based on the current error_reporting level.
* We do that with the use of a PHP error template.
*
* @access	private
* @return	void
*/
function _exception_handler($severity, $message, $filepath, $line)
{	
	 // We don't bother with "strict" notices since they will fill up
	 // the log file with information that isn't normally very
	 // helpful.  For example, if you are running PHP 5 and you
	 // use version 4 style class functions (without prefixes
	 // like "public", "private", etc.) you'll get notices telling
	 // you that these have been deprecated.
	
	if ($severity == E_STRICT)
	{
		return;
	}

	$error =& load_class('WaErrors');

	// Should we display the error?
	// We'll get the current error_reporting level and add its bits
	// with the severity bits to find out.
	
	if (($severity & error_reporting()) == $severity)
	{
		$error->show_php_error($severity, $message, $filepath, $line);
	}
	
	// Should we log the error?  No?  We're done...
	$config =& get_config();
	if ($config['log_threshold'] == 0)
	{
		return;
	}

	$error->log_exception($severity, $message, $filepath, $line);
}




function &load_controller($class){
	static $controllers = array();
	//$class = ucfirst($class);
	$fileExist = false;
	$name = strtolower($class);
	$name2 = classNameToFileName($class);


	if (isset($controllers[$class]))
	{
		return $controllers[$class];
	}

    $myControllerFolders = config_item('controler_folders');
	if (is_array($myControllerFolders)) {	//cinfig is array
		foreach ($myControllerFolders as $folder) {
			if (file_exists(WAPATH_BASE.DS.$folder.DS.$class.EXT))
			{
				require_once(WAPATH_BASE.DS.$folder.DS.$class.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$class.DS.'controller'.DS.$class.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.$class.DS.'controller'.DS.$class.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$name.DS.'controller'.DS.$class.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.$name.DS.'controller'.DS.$class.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$name.EXT))
			{
				require_once(WAPATH_BASE.DS.$folder.DS.$name.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$class.DS.'controller'.DS.$name.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.$class.DS.'controller'.DS.$name.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$name.DS.'controller'.DS.$name.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.$name.DS.'controller'.DS.$name.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$name2.EXT))
			{
				require_once(WAPATH_BASE.DS.$folder.DS.$name2.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$class.DS.'controller'.DS.$name2.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.$class.DS.'controller'.DS.$name2.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$name.DS.'controller'.DS.$name2.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.$name.DS.'controller'.DS.$name2.EXT);
				$fileExist = true;
				break;
			}
		}
	}elseif ($myControllerFolders!="") {	//config is string
		if (file_exists(WAPATH_BASE.DS.$myControllerFolders.DS.$class.EXT))
		{
			require_once(WAPATH_BASE.DS.$myControllerFolders.DS.$class.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myControllerFolders.DS.$class.DS.'controller'.DS.$class.EXT)){
			require_once(WAPATH_BASE.DS.$myControllerFolders.DS.$class.DS.'controller'.DS.$class.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myControllerFolders.DS.$name.DS.'controller'.DS.$class.EXT)){
			require_once(WAPATH_BASE.DS.$myControllerFolders.DS.$name.DS.'controller'.DS.$class.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myControllerFolders.DS.$name.EXT))
		{
			require_once(WAPATH_BASE.DS.$myControllerFolders.DS.$name.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myControllerFolders.DS.$class.DS.'controller'.DS.$name.EXT)){
			require_once(WAPATH_BASE.DS.$myControllerFolders.DS.$class.DS.'controller'.DS.$name.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myControllerFolders.DS.$name.DS.'controller'.DS.$name.EXT)){
			require_once(WAPATH_BASE.DS.$myControllerFolders.DS.$name.DS.'controller'.DS.$name.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myControllerFolders.DS.$name2.EXT))
		{
			require_once(WAPATH_BASE.DS.$myControllerFolders.DS.$name2.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myControllerFolders.DS.$class.DS.'controller'.DS.$name2.EXT)){
			require_once(WAPATH_BASE.DS.$myControllerFolders.DS.$class.DS.'controller'.DS.$name2.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myControllerFolders.DS.$name.DS.'controller'.DS.$name2.EXT)){
			require_once(WAPATH_BASE.DS.$myControllerFolders.DS.$name.DS.'controller'.DS.$name2.EXT);
			$fileExist = true;
			break;
		}

	}

	if (!$fileExist) 
		log_message('error', 'Controller File Not Found --> '.$class.EXT);


	//convert first char to UPPERCASE
	$name = ucfirst($class);
	$name2 = fileNameToClassName($class);

	if (class_exists($class)) {
		$controllers[$class] =& instantiate_class(new $class());
	}elseif(class_exists($name)) {
		$controllers[$class] =& instantiate_class(new $name());
	}elseif(class_exists($name2)) {
		$controllers[$class] =& instantiate_class(new $name2());
	}

	return $controllers[$class]; //can return ref of null
	
}



function &load_view($class, $controller_name){
	static $views = array();
	//$class = ucfirst($class);
	$fileExist = false;
	$name = strtolower($class);
	$name2 = classNameToFileName($class);


	if (isset($views[$class]))
	{
		return $views[$class];
	}

    $myViewFolders = config_item('view_folders');
	if (is_array($myViewFolders)) {
		foreach ($myViewFolders as $folder) {
			if (file_exists(WAPATH_BASE.DS.$folder.DS.$class.EXT))
			{
				require_once(WAPATH_BASE.DS.$folder.DS.$class.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$controller_name.DS.'view'.DS.$class.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.$controller_name.DS.'view'.DS.$class.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.strtolower($controller_name).DS.'view'.DS.$class.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.strtolower($controller_name).DS.'view'.DS.$class.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$name.EXT))
			{
				require_once(WAPATH_BASE.DS.$folder.DS.$name.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$controller_name.DS.'view'.DS.$name.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.$controller_name.DS.'view'.DS.$name.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.strtolower($controller_name).DS.'view'.DS.$name.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.strtolower($controller_name).DS.'view'.DS.$name.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$name2.EXT))
			{
				require_once(WAPATH_BASE.DS.$folder.DS.$name2.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$controller_name.DS.'view'.DS.$name2.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.$controller_name.DS.'view'.DS.$name2.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.strtolower($controller_name).DS.'view'.DS.$name2.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.strtolower($controller_name).DS.'view'.DS.$name2.EXT);
				$fileExist = true;
				break;
			}
		}
	}elseif ($myViewFolders!="") {
		if (file_exists(WAPATH_BASE.DS.$myViewFolders.DS.$class.EXT))
		{
			require_once(WAPATH_BASE.DS.$myViewFolders.DS.$class.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myViewFolders.DS.$controller_name.DS.'view'.DS.$class.EXT)){
			require_once(WAPATH_BASE.DS.$myViewFolders.DS.$controller_name.DS.'view'.DS.$class.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myViewFolders.DS.strtolower($controller_name).DS.'view'.DS.$class.EXT)){
			require_once(WAPATH_BASE.DS.$myViewFolders.DS.strtolower($controller_name).DS.'view'.DS.$class.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myViewFolders.DS.$name.EXT))
		{
			require_once(WAPATH_BASE.DS.$myViewFolders.DS.$name.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myViewFolders.DS.$controller_name.DS.'view'.DS.$name.EXT)){
			require_once(WAPATH_BASE.DS.$myViewFolders.DS.$controller_name.DS.'view'.DS.$name.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myViewFolders.DS.strtolower($controller_name).DS.'view'.DS.$name.EXT)){
			require_once(WAPATH_BASE.DS.$myViewFolders.DS.strtolower($controller_name).DS.'view'.DS.$name.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myViewFolders.DS.$name2.EXT))
		{
			require_once(WAPATH_BASE.DS.$myViewFolders.DS.$name2.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myViewFolders.DS.$controller_name.DS.'view'.DS.$name2.EXT)){
			require_once(WAPATH_BASE.DS.$myViewFolders.DS.$controller_name.DS.'view'.DS.$name2.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myViewFolders.DS.strtolower($controller_name).DS.'view'.DS.$name2.EXT)){
			require_once(WAPATH_BASE.DS.$myViewFolders.DS.strtolower($controller_name).DS.'view'.DS.$name2.EXT);
			$fileExist = true;
			break;
		}

	}

	if (!$fileExist) 
		log_message('error', 'View File Not Found --> '.$class.EXT);

	//convert first char to UPPERCASE
	$name = ucfirst($class);
	$name2 = fileNameToClassName($class);

	if (class_exists($class)) {
		$views[$class] =& instantiate_class(new $class());
	}elseif(class_exists($name)) {
		$views[$class] =& instantiate_class(new $name());
	}elseif(class_exists($name2)) {
		$views[$class] =& instantiate_class(new $name2());
	}
	
	if (method_exists($views[$class],'setControllerName')) {
		$views[$class]->setControllerName($controller_name);
	}
	
	return $views[$class];	//can return ref of null
}





function &load_mode($class, $controller_name){
	static $modes = array();
	//$class = ucfirst($class);
	$fileExist = false;
	$name = strtolower($class);
	$name2 = classNameToFileName($class);

	if (isset($modes[$class]))
	{
		return $modes[$class];
	}

    $myModeFolders = config_item('mode_folders');
	if (is_array($myModeFolders)) {
		foreach ($myModeFolders as $folder) {
			if (file_exists(WAPATH_BASE.DS.$folder.DS.$class.EXT))
			{
				require_once(WAPATH_BASE.DS.$folder.DS.$class.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$controller_name.DS.'mode'.DS.$class.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.$controller_name.DS.'mode'.DS.$class.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.strtolower($controller_name).DS.'mode'.DS.$class.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.strtolower($controller_name).DS.'mode'.DS.$class.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$name.EXT))
			{
				require_once(WAPATH_BASE.DS.$folder.DS.$name.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$controller_name.DS.'mode'.DS.$name.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.$controller_name.DS.'mode'.DS.$name.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.strtolower($controller_name).DS.'mode'.DS.$name.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.strtolower($controller_name).DS.'mode'.DS.$name.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$name2.EXT))
			{
				require_once(WAPATH_BASE.DS.$folder.DS.$name2.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$controller_name.DS.'mode'.DS.$name2.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.$controller_name.DS.'mode'.DS.$name2.EXT);
				$fileExist = true;
				break;
			}elseif (file_exists(WAPATH_BASE.DS.$folder.DS.strtolower($controller_name).DS.'mode'.DS.$name2.EXT)){
				require_once(WAPATH_BASE.DS.$folder.DS.strtolower($controller_name).DS.'mode'.DS.$name2.EXT);
				$fileExist = true;
				break;
			}
		}
	}elseif ($myModeFolders!="") {
		if (file_exists(WAPATH_BASE.DS.$myModeFolders.DS.$class.EXT))
		{
			require_once(WAPATH_BASE.DS.$myModeFolders.DS.$class.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myModeFolders.DS.$controller_name.DS.'mode'.DS.$class.EXT)){
			require_once(WAPATH_BASE.DS.$myModeFolders.DS.$controller_name.DS.'mode'.DS.$class.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myModeFolders.DS.strtolower($controller_name).DS.'mode'.DS.$class.EXT)){
			require_once(WAPATH_BASE.DS.$myModeFolders.DS.strtolower($controller_name).DS.'mode'.DS.$class.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myModeFolders.DS.$name.EXT))
		{
			require_once(WAPATH_BASE.DS.$myModeFolders.DS.$name.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myModeFolders.DS.$controller_name.DS.'mode'.DS.$name.EXT)){
			require_once(WAPATH_BASE.DS.$myModeFolders.DS.$controller_name.DS.'mode'.DS.$name.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myModeFolders.DS.strtolower($controller_name).DS.'mode'.DS.$name.EXT)){
			require_once(WAPATH_BASE.DS.$myModeFolders.DS.strtolower($controller_name).DS.'mode'.DS.$name.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myModeFolders.DS.$name2.EXT))
		{
			require_once(WAPATH_BASE.DS.$myModeFolders.DS.$name2.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myModeFolders.DS.$controller_name.DS.'mode'.DS.$name2.EXT)){
			require_once(WAPATH_BASE.DS.$myModeFolders.DS.$controller_name.DS.'mode'.DS.$name2.EXT);
			$fileExist = true;
			break;
		}elseif (file_exists(WAPATH_BASE.DS.$myModeFolders.DS.strtoloer($controller_name).DS.'mode'.DS.$name2.EXT)){
			require_once(WAPATH_BASE.DS.$myModeFolders.DS.strtolower($controller_name).DS.'mode'.DS.$name2.EXT);
			$fileExist = true;
			break;
		}

	}

	if (!$fileExist) 
		log_message('error', 'Mode File Not Found --> '.$class.EXT);


	//convert first char to UPPERCASE
	$name = ucfirst($class);
	$name2 = fileNameToClassName($class);

	if (class_exists($class)) {
		$modes[$class] =& instantiate_class(new $class());
	}elseif(class_exists($name)) {
		$modes[$class] =& instantiate_class(new $name());
	}elseif(class_exists($name2)) {
		$modes[$class] =& instantiate_class(new $name2());
	}

	if (method_exists($modes[$class],'setControllerName')) {
		$modes[$class]->setControllerName($controller_name);
	}
	return $modes[$class];
}



function pa_call_user_func_array($c, $a, $p) {
    if (isset($c) && $a!="" && method_exists($c, $a)) { 
        switch(count($p)) {
            case 0: 
                $c->{$a}();     //call function by this template Benchmarking ~ 120% call by native
                break;
            case 1: 
                $c->{$a}($p[0]); //call function by this template Benchmarking ~ 120% call by native
                break;
            case 2: 
                $c->{$a}($p[0], $p[1]); //call function by this template Benchmarking ~ 120% call by native
                break;
            case 3: 
                $c->{$a}($p[0], $p[1], $p[2]); //call function by this template Benchmarking ~ 120% call by native
                break;
            case 4: 
                $c->{$a}($p[0], $p[1], $p[2], $p[3]); //call function by this template Benchmarking ~ 120% call by native
                break;
            case 5: 
                $c->{$a}($p[0], $p[1], $p[2], $p[3], $p[4]); //call function by this template Benchmarking ~ 120% call by native
                break;
            default: 
                call_user_func_array(array($c, $a), $p);  //call function by this template Benchmarking ~ 200% -> 220% call by native
                break;
        }
    }else {
		show_404();
	}
} 



/**
 * Returns given $lower_case_and_underscored_word as a camelCased word.
 *
 * @param string $lower_case_and_underscored_word Word to camelize
 * @return string Camelized word. likeThis.
 * @access public
 * @static
 */
function fileNameToClassName($lowerCaseAndUnderscoredWord) {
	$replace = str_replace(" ", "", ucwords(str_replace("_", " ", $lowerCaseAndUnderscoredWord)));
	return $replace;
}

/**
 * Returns an underscore-syntaxed ($like_this_dear_reader) version of the $camel_cased_word.
 *
 * @param string $camel_cased_word Camel-cased word to be "underscorized"
 * @return string Underscore-syntaxed version of the $camel_cased_word
 * @access public
 * @static
 */
    function classNameToFileName($camelCasedWord) {
            $replace = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
            return $replace;
    }

	
	
	
	
	
/**
 * Reads/writes temporary data to cache files or session.
 *
 * @param  string $path	File path within /tmp to save the file.
 * @param  mixed  $data	The data to save to the temporary file.
 * @param  mixed  $expires A valid strtotime string when the data expires.
 * @param  string $target  The target of the cached data; either 'cache' or 'public'.
 * @return mixed  The contents of the temporary file.
 * @deprecated Please use Cache::write() instead
 */
	function cache($path, $data = null, $expires = '+1 day', $target = 'cache') {
		
		$myCacheFlag = config_item('enable_cache');
	
		if (isset($myCacheFlag) && false===$myCacheFlag) {
			return null;
		}
		$now = time();

		if (!is_numeric($expires)) {
			$expires = strtotime($expires, $now);
		}

		switch (strtolower($target)) {
			case 'cache':
				$filename = config_item('cache_path').$path;
			break;
			case 'public':
				$filename = config_item('base_url') . $path;
			break;
			case 'tmp':
				$filename = config_item('temp_path') . $path;
			break;
		}
		$timediff = $expires - $now;
		$filetime = false;

		if (file_exists($filename)) {
			$filetime = @filemtime($filename);
		}

		if ($data === null) {
			if (file_exists($filename) && $filetime !== false) {
				if ($filetime + $timediff < $now) {
					@unlink($filename);
				} else {
					$data = @file_get_contents($filename);
				}
			}
		} elseif (is_writable(dirname($filename))) {
			@file_put_contents($filename, $data);
		}
		return $data;
	}

/**
 * Used to delete files in the cache directories, or clear contents of cache directories
 *
 * @param mixed $params As String name to be searched for deletion, if name is a directory all files in
 *   directory will be deleted. If array, names to be searched for deletion. If clearCache() without params,
 *   all files in app/tmp/cache/views will be deleted
 * @param string $type Directory in tmp/cache defaults to view directory
 * @param string $ext The file extension you are deleting
 * @return true if files found and deleted false otherwise
 */
	function clearCache($params = null, $type = 'views', $ext = '.php') {
	
		$myCachePath = config_item('cache_path');
	
		if (is_string($params) || $params === null) {
			$params = preg_replace('/\/\//', '/', $params);
			$cache = $myCachePath . $type . DS . $params;

			if (is_file($cache . $ext)) {
				@unlink($cache . $ext);
				return true;
			} elseif (is_dir($cache)) {
				$files = glob($cache . '*');

				if ($files === false) {
					return false;
				}

				foreach ($files as $file) {
					if (is_file($file) && strrpos($file, DS . 'empty') !== strlen($file) - 6) {
						@unlink($file);
					}
				}
				return true;
			} else {
				$cache = array(
					$myCachePath . $type . DS . '*' . $params . $ext,
					$myCachePath . $type . DS . '*' . $params . '_*' . $ext
				);
				$files = array();
				while ($search = array_shift($cache)) {
					$results = glob($search);
					if ($results !== false) {
						$files = array_merge($files, $results);
					}
				}
				if (empty($files)) {
					return false;
				}
				foreach ($files as $file) {
					if (is_file($file) && strrpos($file, DS . 'empty') !== strlen($file) - 6) {
						@unlink($file);
					}
				}
				return true;
			}
		} elseif (is_array($params)) {
			foreach ($params as $file) {
				clearCache($file, $type, $ext);
			}
			return true;
		}
		return false;
	}





	if(function_exists('lcfirst') === false) {
    function lcfirst($str) {
        $str[0] = strtolower($str[0]);
        return $str;
    }
}


/**
 * load all function in helper file
 * @param type $helper_name 
 */
function load_helper($helper_name){
    $name = strtolower($helper_name);
    $myFolders = config_item('helper_folders');
    if (is_array($helper_folders)) {
        foreach (helper_folders as $folder) {
            if (file_exists(WAPATH_BASE.DS.$folder.DS.$name.EXT))
            {
                require_once(WAPATH_BASE.DS.$folder.DS.$name.EXT);
                break;
            }elseif (file_exists(WAPATH_BASE.DS.$folder.DS.$name."_helper".EXT))
            {
                require_once(WAPATH_BASE.DS.$folder.DS.$name."_helper".EXT);
                break;
            }
        }
    }
    
}



/**
 * Trim Slashes
 *
 * Removes any leading/traling slashes from a string:
 *
 * /this/that/theother/
 *
 * becomes:
 *
 * this/that/theother
 *
 * @access	public
 * @param	string
 * @return	string
 */	
if ( ! function_exists('trim_slashes'))
{
	function trim_slashes($str)
	{
		return trim($str, '/');
	} 
}
	
// ------------------------------------------------------------------------

/**
 * Strip Slashes
 *
 * Removes slashes contained in a string or in an array
 *
 * @access	public
 * @param	mixed	string or array
 * @return	mixed	string or array
 */	
if ( ! function_exists('strip_slashes'))
{
	function strip_slashes($str)
	{
		if (is_array($str))
		{	
			foreach ($str as $key => $val)
			{
				$str[$key] = strip_slashes($val);
			}
		}
		else
		{
			$str = stripslashes($str);
		}
	
		return $str;
	}
}

// ------------------------------------------------------------------------

/**
 * Strip Quotes
 *
 * Removes single and double quotes from a string
 *
 * @access	public
 * @param	string
 * @return	string
 */	
if ( ! function_exists('strip_quotes'))
{
	function strip_quotes($str)
	{
		return str_replace(array('"', "'"), '', $str);
	}
}

// ------------------------------------------------------------------------

/**
 * Quotes to Entities
 *
 * Converts single and double quotes to entities
 *
 * @access	public
 * @param	string
 * @return	string
 */	
if ( ! function_exists('quotes_to_entities'))
{
	function quotes_to_entities($str)
	{	
		return str_replace(array("\'","\"","'",'"'), array("&#39;","&quot;","&#39;","&quot;"), $str);
	}
}

// ------------------------------------------------------------------------
/**
 * Reduce Double Slashes
 *
 * Converts double slashes in a string to a single slash,
 * except those found in http://
 *
 * http://www.some-site.com//index.php
 *
 * becomes:
 *
 * http://www.some-site.com/index.php
 *
 * @access	public
 * @param	string
 * @return	string
 */	
if ( ! function_exists('reduce_double_slashes'))
{
	function reduce_double_slashes($str)
	{
		return preg_replace("#([^:])//+#", "\\1/", $str);
	}
}
	
// ------------------------------------------------------------------------

/**
 * Reduce Multiples
 *
 * Reduces multiple instances of a particular character.  Example:
 *
 * Fred, Bill,, Joe, Jimmy
 *
 * becomes:
 *
 * Fred, Bill, Joe, Jimmy
 *
 * @access	public
 * @param	string
 * @param	string	the character you wish to reduce
 * @param	bool	TRUE/FALSE - whether to trim the character from the beginning/end
 * @return	string
 */	
if ( ! function_exists('reduce_multiples'))
{
	function reduce_multiples($str, $character = ',', $trim = FALSE)
	{
		$str = preg_replace('#'.preg_quote($character, '#').'{2,}#', $character, $str);

		if ($trim === TRUE)
		{
			$str = trim($str, $character);
		}

		return $str;
	}
}
	
// ------------------------------------------------------------------------

/**
 * Create a Random String
 *
 * Useful for generating passwords or hashes.
 *
 * @access	public
 * @param	string 	type of random string.  Options: alunum, numeric, nozero, unique
 * @param	integer	number of characters
 * @return	string
 */
if ( ! function_exists('random_string'))
{	
	function random_string($type = 'alnum', $len = 8)
	{					
		switch($type)
		{
			case 'alnum'	:
			case 'numeric'	:
			case 'nozero'	:
		
					switch ($type)
					{
						case 'alnum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
							break;
						case 'numeric'	:	$pool = '0123456789';
							break;
						case 'nozero'	:	$pool = '123456789';
							break;
					}

					$str = '';
					for ($i=0; $i < $len; $i++)
					{
						$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
					}
					return $str;
			  break;
			case 'unique' : return md5(uniqid(mt_rand()));
			  break;
		}
	}
}

// ------------------------------------------------------------------------

/**
 * Alternator
 *
 * Allows strings to be alternated.  See docs...
 *
 * @access	public
 * @param	string (as many parameters as needed)
 * @return	string
 */	
if ( ! function_exists('alternator'))
{
	function alternator()
	{
		static $i;	

		if (func_num_args() == 0)
		{
			$i = 0;
			return '';
		}
		$args = func_get_args();
		return $args[($i++ % count($args))];
	}
}

// ------------------------------------------------------------------------

/**
 * Repeater function
 *
 * @access	public
 * @param	string
 * @param	integer	number of repeats
 * @return	string
 */	
if ( ! function_exists('repeater'))
{
	function repeater($data, $num = 1)
	{
		return (($num > 0) ? str_repeat($data, $num) : '');
	} 
}



/**
 * Force Download
 *
 * Generates headers that force a download to happen
 *
 * @access	public
 * @param	string	filename
 * @param	mixed	the data to be downloaded
 * @return	void
 */
if ( ! function_exists('force_download'))
{
	function force_download($filename = '', $data = '')
	{
		if ($filename == '' OR $data == '')
		{
			return FALSE;
		}

		// Try to determine if the filename includes a file extension.
		// We need it in order to set the MIME type
		if (FALSE === strpos($filename, '.'))
		{
			return FALSE;
		}

		// Grab the file extension
		$x = explode('.', $filename);
		$extension = end($x);

		// Load the mime types
		if (is_file(WAPATH_CONFIG.'mimes.php'))
		{
			include(WAPATH_CONFIG.'mimes.php');
		}
		

		// Set a default mime if we can't find it
		if ( ! isset($mimes[$extension]))
		{
			$mime = 'application/octet-stream';
		}
		else
		{
			$mime = (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
		}

		// Generate the server headers
		if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE)
		{
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".strlen($data));
		}
		else
		{
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
			header("Content-Length: ".strlen($data));
		}

		exit($data);
	}
}


function get_extension($filename)
{
    $x = explode('.', $filename);
    return end($x);
}

	
/* End of file common.php */
/* Location: ./system/utils/common.php */
