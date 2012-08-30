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
class WaController {

	protected $_get_args = array();
	protected $_post_args = array();
	protected $_args = array();
	
	protected $security;
        
        
        //protected $do_auto_form_redirect = "/";
	
	//private static $instance = false;
	
	/**
	 * Constructor
	 *
	 * Calls the initialize() function
	 */
	 /*
	final private function __construct()
	{	
		$this->init();
		self::$instance =& $this;
		log_message('debug', "Controller Class Initialized");
	}
	*/
	
	public function __construct()
	{	
		log_message('debug', "Controller Class Initialized");
		
		$this->security = load_class('WaSecurity');

		$myUri = load_class('WaUri');
		$this->_args = array_merge($myUri->getInputAssocParams(), $_GET, $_POST);
		$this->_get_args = array_merge($myUri->getInputAssocParams(), $_GET);
		$this->_post_args = $_POST;

	}
	
	
	
	
	
	public function getVar($_varName = ""){
            if ($_varName==="")  return $this->_get_args;
            return (isset($this->_get_args[$_varName]))?$this->_get_args[$_varName]:null;
	}
	

	public function postVar($_varName = ""){
            if ($_varName==="")  return $this->_post_args;
            return (isset($this->_post_args[$_varName]))?$this->_post_args[$_varName]:null;
	}


	public function requestVar($_varName = ""){
            if ($_varName==="")  return $this->_args;
            return (isset($this->_args[$_varName]))?$this->_args[$_varName]:null;
	}




	/**
	 * Redirect to another URL
	 *
	 * @access	public
	 * @param	string	$url	The URL to redirect to
	 */
	function redirect( $url)
	{
		/*
		 * If the headers have been sent, then we cannot send an additional location header
		 * so we will output a javascript redirect statement.
		 */
		if (headers_sent()) {
			echo "<script>document.location.href='$url';</script>\n";
		} else {
			//@ob_end_clean(); // clear output buffer
			session_write_close();
			header( 'HTTP/1.1 301 Moved Permanently' );
			header( "Location: ". $url );
		}
		exit();
	}
	
	
	public function &getView($class){
		$myView = null;
		$myControllerName = lcfirst(get_class($this));
		$myView = load_view($class, $myControllerName);
		return $myView;
	}

        
        
        
        public function do_auto_form() {
            $myDataObjectNames = $this->requestVar('dataobject');
            if (!isset($myDataObjectNames) || !is_array($myDataObjectNames)) return false;
            
            foreach ($myDataObjectNames as $objectName) {
                $myDataObject = load_class($objectName);
                if ($myDataObject!=null && $myDataObject!==false) {
                    $myBeanData = $myDataObject->getObjectDataFromRawData($myDataObjectNames);
                    if ($myBeanData){
                        $result = $myDataObject->commitMulti($myBeanData, true);
                        
                        //echo, redirect //TODO
                        
                    }
                }
            }
        }
        
        
        
        
        
        
        /**
	 * get file name after uploaded
	 *
	 * @param string $name
	 * @return string
	 */
	public function getFile($name) {
		return $_FILES[$name]['name'];
	}

	/**
	 * get list file after uploaded
	 *
	 * @return array
	 */
	public function & getFiles() {
		return $_FILES;
	}

	/**
	 * get tempfile after uploaded
	 *
	 * @param string $name
	 * @return string
	 */
	public function getTempFile($name) {
		return $_FILES[$name]['tmp_name'];
	}

	/**
	 * get filesize after uploaded
	 *
	 * @param string $name
	 * @return integer
	 */
	public function getFileSize($name) {
		return $_FILES[$name]['size'];
	}

	/**
	 * get file type after uploaded
	 *
	 * @param string $name
	 * @return string
	 */
	public function getFileType($name) {
		return $_FILES[$name]['type'];
	}

	/**
	 * Copy file after uploaded to $dir
	 *
	 * @param string $name
	 * @param string $dir
	 * @return boolean, TRUE if copy success
	 */
	public function copyFile($name, $dir) {
		if (substr($dir, -1) != '/') {
			$dir .= '/';
		}
                
                @mkdir($dir, 0777, true);
                @unlink($dir.$name);
                
		return @copy($_FILES[$name]['tmp_name'], $dir.$_FILES[$name]['name']);
	}
        
        

}
// END WaController class
