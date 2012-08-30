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
class WaUri {
	private $_baseHost = "http://example.com:81";
	private $_baseUrl = "http://example.com/home/";
	private $_baseScript = "http://example.com/home/index.php";
	
	private $_module = "";
	private $_method = "index";
	private $_inputVars = array();

	private $_inputAssocVars = array();
	
	private $config;

	public function WaUri($c_var_name = 'module', $f_var_name = 'action', $prefix_params = 'param_' ){
	
//		$this->config = & load_class('Config');
		$_protocol = $_SERVER['SERVER_PROTOCOL'];
		if (substr_count($_protocol,'HTTPS')) {
			$_protocol = "https://";
		}elseif(substr_count($_protocol,'HTTP')) {
			$_protocol = "http://";
		}
		
		$_serverName = $_SERVER['SERVER_NAME'];
		$_scriptName = $_SERVER['SCRIPT_NAME'];
		$_serverPort = $_SERVER['SERVER_PORT'];
		
		$this->_baseHost = ($_serverPort=='80')?($_protocol.$_serverName):($_protocol.$_serverName.':'.$_serverPort); //http://example.com:81
		
		$this->_baseScript = $this->_baseHost.$_scriptName; //http://example.com/home/index.php
		
		$this->_baseUrl = substr($this->_baseScript, 0, strripos($this->_baseScript, '/')+1); //http://example.com/home/
		//$this->_fetch_url($c_var_name, $f_var_name, $prefix_params);

	}
	
	
	public function _fetch_url($c_var_name = 'module', $f_var_name = 'action', $prefix_params = 'param_' ) {
		if (is_array($_GET)){
			$this->_module = isset($_GET[$c_var_name])?$_GET[$c_var_name]:"";
			$this->_method = isset($_GET[$f_var_name])?$_GET[$f_var_name]:"";
			$i = 0;
			while (isset($_GET[$prefix_params.$i])) {
				$this->_inputVars[] = urldecode($_GET[$prefix_params.$i]);
				$i++;
			}
			
			if ($this->_module!="") return TRUE;
		}
		
		$_fullRequest = $this->_baseHost . $_SERVER['REQUEST_URI'];
		$myPos = stripos($_fullRequest, $this->_baseScript);
		
		if ($myPos!==false){
			$myQueryStr = substr($_fullRequest, $myPos + strlen($this->_baseScript)+1);	//		?23/wewew
			$myGetVars = explode("/", $myQueryStr);
			if (isset($myGetVars[0])) $this->_module = $myGetVars[0];
			if (isset($myGetVars[1])) $this->_method = $myGetVars[1];
			$myVarsLength = count($myGetVars);
			for ($i = 2; $i<$myVarsLength; $i++) {
				//Lay bien kieu mang so tu duong dan
				$this->_inputVars[$i - 2] = urldecode($myGetVars[$i]);
				
				
				//Lay bien kieu assoc tu duong dan
				if(($i%2==0)&&($i+1<$myVarsLength)) {
					$this->_inputAssocVars[urldecode($myGetVars[$i])] = urldecode($myGetVars[$i+1]);
				}

			}
			if ($this->_module!="") return TRUE;
		}else{
			$myPos = stripos($_fullRequest, $this->_baseUrl);
			if ($myPos){
				$myQueryStr = substr($_fullRequest, $myPos + strlen($this->_baseUrl));
				$myGetVars = explode("/", $myQueryStr);
				if (isset($myGetVars[0])) $this->_module = $myGetVars[0];
				if (isset($myGetVars[1])) $this->_method = $myGetVars[1];
				$myVarsLength = count($myGetVars);
				for ($i = 2; $i<$myVarsLength; $i++) {
					//Lay bien kieu mang so tu duong dan
					$this->_inputVars[$i - 2] = urldecode($myGetVars[$i]);

					//Lay bien kieu assoc tu duong dan
					if(($i%2==0)&&($i+1<$myVarsLength)) {
						$this->_inputAssocVars[urldecode($myGetVars[$i])] = urldecode($myGetVars[$i+1]);
					}
				}
				if ($this->_module!="") {
					return TRUE;
				}else{
					return FALSE;
				}
			}else {
				return FALSE;
			}
			
		}
		
	}
	
	
	// private function getVarFromRawUrlStr($rawStr = "") {
		// $returnStr = "";
		
		// return $returnStr;
	// }

	
	public function getControllerName(){
		return $this->_module;
	}
	
	public function getMethodName(){
		return $this->_method;
	}
	
	
	public function getInputParams(){
		return $this->_inputVars;
	}

	public function getInputAssocParams(){
		return $this->_inputAssocVars;
	}

}



/*
array(30) {
  ["HTTP_HOST"]=>
  string(9) "localhost"
  ["HTTP_USER_AGENT"]=>
  string(106) "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.13) Gecko/20101206 Ubuntu/10.10 (maverick) Firefox/3.6.13"
  ["HTTP_ACCEPT"]=>
  string(63) "text/html,application/xhtml+xml,application/xml;q=0.9;q=0.8"
  ["HTTP_ACCEPT_LANGUAGE"]=>
  string(14) "en-us,en;q=0.5"
  ["HTTP_ACCEPT_ENCODING"]=>
  string(12) "gzip,deflate"
  ["HTTP_ACCEPT_CHARSET"]=>
  string(30) "ISO-8859-1,utf-8;q=0.7,*;q=0.7"
  ["HTTP_KEEP_ALIVE"]=>
  string(3) "115"
  ["HTTP_CONNECTION"]=>
  string(10) "keep-alive"
  ["HTTP_COOKIE"]=>
  string(560) "virtuemart=j3u7f34jf5lmnh1om2fqu1ek20; FDOC_session_name-1002685647-acc=LTTH1KmDz4Eo0dRbjyy%252BsNMtZ%252FDqPWwYbzLnVfxHDzgKZXmSiREG6PAXJqzUwFQ018Y%253D; FDOC-1002685647-SID=a282a85ae0827f4b584e5555e474e61341cb5928; bep_navigationmenu_bep_system=block; PHPSESSID=2h7s0qkv448iubfenn2lg8a705; switchmenu=1; FDOC-1335267697-SID=08160b2de4a1aa75ae8bafa357cd8701857b8c4c; FDOC-991331757-SID=168ef50d634509c4bc752864d6c57a06315f0dcc; 062d08c1ed771d7378ba9113d0c9cf0b=446639f8ffc7cef1216f165738ecbd05; 8422a715d968167dd8aa6511490de46a=932ade0a711ad238391e29d56b92586d"
  ["PATH"]=>
  string(28) "/usr/local/bin:/usr/bin:/bin"
  ["SERVER_SIGNATURE"]=>
  string(70) "<address>Apache/2.2.16 (Ubuntu) Server at localhost Port 80</address>
"
  ["SERVER_SOFTWARE"]=>
  string(22) "Apache/2.2.16 (Ubuntu)"
  ["SERVER_NAME"]=>
  string(9) "localhost"
  ["SERVER_ADDR"]=>
  string(9) "127.0.0.1"
  ["SERVER_PORT"]=>
  string(2) "80"
  ["REMOTE_ADDR"]=>
  string(9) "127.0.0.1"
  ["DOCUMENT_ROOT"]=>
  string(8) "/var/www"
  ["SERVER_ADMIN"]=>
  string(19) "webmaster@localhost"
  ["SCRIPT_FILENAME"]=>
  string(26) "/var/www/joomla/myinfo.php"
  ["REMOTE_PORT"]=>
  string(5) "49142"
  ["GATEWAY_INTERFACE"]=>
  string(7) "CGI/1.1"
  ["SERVER_PROTOCOL"]=>
  string(8) "HTTP/1.1"
  ["REQUEST_METHOD"]=>
  string(3) "GET"
  ["QUERY_STRING"]=>
  string(0) ""
  ["REQUEST_URI"]=>
  string(52) "/joomla/myinfo.php/mekiepdsifsd%20/dsf%20uibsdyflkud"
  ["SCRIPT_NAME"]=>
  string(18) "/joomla/myinfo.php"
  ["PATH_INFO"]=>
  string(30) "/mekiepdsifsd /dsf uibsdyflkud"
  ["PATH_TRANSLATED"]=>
  string(38) "/var/www/mekiepdsifsd /dsf uibsdyflkud"
  ["PHP_SELF"]=>
  string(48) "/joomla/myinfo.php/mekiepdsifsd /dsf uibsdyflkud"
  ["REQUEST_TIME"]=>
  int(1296027336)
}

*/








/*
array(28) {
  ["HTTP_HOST"]=>
  string(9) "localhost"
  ["HTTP_USER_AGENT"]=>
  string(106) "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.13) Gecko/20101206 Ubuntu/10.10 (maverick) Firefox/3.6.13"
  ["HTTP_ACCEPT"]=>
  string(63) "text/html,application/xhtml+xml,application/xml;q=0.9,*;q=0.8"
  ["HTTP_ACCEPT_LANGUAGE"]=>
  string(14) "en-us,en;q=0.5"
  ["HTTP_ACCEPT_ENCODING"]=>
  string(12) "gzip,deflate"
  ["HTTP_ACCEPT_CHARSET"]=>
  string(30) "ISO-8859-1,utf-8;q=0.7,*;q=0.7"
  ["HTTP_KEEP_ALIVE"]=>
  string(3) "115"
  ["HTTP_CONNECTION"]=>
  string(10) "keep-alive"
  ["HTTP_COOKIE"]=>
  string(521) "FDOC_session_name-1002685647-acc=LTTH1KmDz4Eo0dRbjyy%252BsNMtZ%252FDqPWwYbzLnVfxHDzgKZXmSiREG6PAXJqzUwFQ018Y%253D; FDOC-1002685647-SID=a282a85ae0827f4b584e5555e474e61341cb5928; bep_navigationmenu_bep_system=block; PHPSESSID=2h7s0qkv448iubfenn2lg8a705; switchmenu=1; FDOC-1335267697-SID=08160b2de4a1aa75ae8bafa357cd8701857b8c4c; FDOC-991331757-SID=168ef50d634509c4bc752864d6c57a06315f0dcc; 062d08c1ed771d7378ba9113d0c9cf0b=446639f8ffc7cef1216f165738ecbd05; 8422a715d968167dd8aa6511490de46a=932ade0a711ad238391e29d56b92586d"
  ["PATH"]=>
  string(28) "/usr/local/bin:/usr/bin:/bin"
  ["SERVER_SIGNATURE"]=>
  string(70) "<address>Apache/2.2.16 (Ubuntu) Server at localhost Port 80</address>
"
  ["SERVER_SOFTWARE"]=>
  string(22) "Apache/2.2.16 (Ubuntu)"
  ["SERVER_NAME"]=>
  string(9) "localhost"
  ["SERVER_ADDR"]=>
  string(9) "127.0.0.1"
  ["SERVER_PORT"]=>
  string(2) "80"
  ["REMOTE_ADDR"]=>
  string(9) "127.0.0.1"
  ["DOCUMENT_ROOT"]=>
  string(8) "/var/www"
  ["SERVER_ADMIN"]=>
  string(19) "webmaster@localhost"
  ["SCRIPT_FILENAME"]=>
  string(28) "/var/www/phpexcell/index.php"
  ["REMOTE_PORT"]=>
  string(5) "37221"
  ["GATEWAY_INTERFACE"]=>
  string(7) "CGI/1.1"
  ["SERVER_PROTOCOL"]=>
  string(8) "HTTP/1.1"
  ["REQUEST_METHOD"]=>
  string(3) "GET"
  ["QUERY_STRING"]=>
  string(0) ""
  ["REQUEST_URI"]=>
  string(11) "/phpexcell/"
  ["SCRIPT_NAME"]=>
  string(20) "/phpexcell/index.php"
  ["PHP_SELF"]=>
  string(20) "/phpexcell/index.php"
  ["REQUEST_TIME"]=>
  int(1296027536)
}

*/




/*
array(28) {
  ["HTTP_HOST"]=>
  string(9) "localhost"
  ["HTTP_USER_AGENT"]=>
  string(106) "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.13) Gecko/20101206 Ubuntu/10.10 (maverick) Firefox/3.6.13"
  ["HTTP_ACCEPT"]=>
  string(63) "text/html,application/xhtml+xml,application/xml;q=0.9,;q=0.8"
  ["HTTP_ACCEPT_LANGUAGE"]=>
  string(14) "en-us,en;q=0.5"
  ["HTTP_ACCEPT_ENCODING"]=>
  string(12) "gzip,deflate"
  ["HTTP_ACCEPT_CHARSET"]=>
  string(30) "ISO-8859-1,utf-8;q=0.7,*;q=0.7"
  ["HTTP_KEEP_ALIVE"]=>
  string(3) "115"
  ["HTTP_CONNECTION"]=>
  string(10) "keep-alive"
  ["HTTP_COOKIE"]=>
  string(521) "FDOC_session_name-1002685647-acc=LTTH1KmDz4Eo0dRbjyy%252BsNMtZ%252FDqPWwYbzLnVfxHDzgKZXmSiREG6PAXJqzUwFQ018Y%253D; FDOC-1002685647-SID=a282a85ae0827f4b584e5555e474e61341cb5928; bep_navigationmenu_bep_system=block; PHPSESSID=2h7s0qkv448iubfenn2lg8a705; switchmenu=1; FDOC-1335267697-SID=08160b2de4a1aa75ae8bafa357cd8701857b8c4c; FDOC-991331757-SID=168ef50d634509c4bc752864d6c57a06315f0dcc; 062d08c1ed771d7378ba9113d0c9cf0b=446639f8ffc7cef1216f165738ecbd05; 8422a715d968167dd8aa6511490de46a=932ade0a711ad238391e29d56b92586d"
  ["PATH"]=>
  string(28) "/usr/local/bin:/usr/bin:/bin"
  ["SERVER_SIGNATURE"]=>
  string(70) "<address>Apache/2.2.16 (Ubuntu) Server at localhost Port 80</address>
"
  ["SERVER_SOFTWARE"]=>
  string(22) "Apache/2.2.16 (Ubuntu)"
  ["SERVER_NAME"]=>
  string(9) "localhost"
  ["SERVER_ADDR"]=>
  string(9) "127.0.0.1"
  ["SERVER_PORT"]=>
  string(2) "80"
  ["REMOTE_ADDR"]=>
  string(9) "127.0.0.1"
  ["DOCUMENT_ROOT"]=>
  string(8) "/var/www"
  ["SERVER_ADMIN"]=>
  string(19) "webmaster@localhost"
  ["SCRIPT_FILENAME"]=>
  string(28) "/var/www/phpexcell/index.php"
  ["REMOTE_PORT"]=>
  string(5) "37223"
  ["GATEWAY_INTERFACE"]=>
  string(7) "CGI/1.1"
  ["SERVER_PROTOCOL"]=>
  string(8) "HTTP/1.1"
  ["REQUEST_METHOD"]=>
  string(3) "GET"
  ["QUERY_STRING"]=>
  string(14) "dighdf/efdsfsd"
  ["REQUEST_URI"]=>
  string(26) "/phpexcell/?dighdf/efdsfsd"
  ["SCRIPT_NAME"]=>
  string(20) "/phpexcell/index.php"
  ["PHP_SELF"]=>
  string(20) "/phpexcell/index.php"
  ["REQUEST_TIME"]=>
  int(1296027566)
}


*/
