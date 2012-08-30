<?php
/*
|---------------------------------------------------------------
| PHP ERROR REPORTING LEVEL
|---------------------------------------------------------------
|
| By default SYSTEM runs with error reporting set to ALL.  For security
| reasons you are encouraged to change this when your site goes live.
| For more info visit:  http://www.php.net/error_reporting
|
*/
	error_reporting(E_ALL);
	

// Set flag that this is a parent file
define( '_WAEXEC', 1 );


/*
|---------------------------------------------------------------
| SYSTEM FOLDER NAME
|---------------------------------------------------------------
|
| This variable must contain the name of your "system" folder.
| Include the path if the folder is not in the same  directory
| as this file.
|
| NO TRAILING SLASH!
|
*/
$system_folder = "system";



define('WAPATH_BASE', dirname(__FILE__) );
define( 'DS', DIRECTORY_SEPARATOR );
define('BASEPATH', $system_folder.DS);
define( 'WAPATH_SYSTEM',	 	WAPATH_BASE.DS.$system_folder.DS );
define('EXT', '.php');
$startTime = microtime(true);

require_once(WAPATH_SYSTEM .DS.'entryPoint'.EXT);

require_once(WAPATH_SYSTEM .DS.'PaApplication'.EXT);
//$app = new PaApplication();

$myArr = array(
				'me'=>'Huy',
				'son'=>'Nguye',
				'wife'=>'Lich'
			);
			
$data = serialize($myArr);
echo $data;

$new = unserialize($data);

var_export($new);

cache('me.php',$data);

$data1 = null;
$data1 = cache('me.php');

echo $data1;
