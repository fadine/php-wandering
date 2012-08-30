<?php
// no direct access
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
| CORE FOLDER NAME
|---------------------------------------------------------------
|
| This variable must contain the name of your "core" folder.
|
*/
$core_folder = "core";


/*
|---------------------------------------------------------------
| CONFIG FOLDER NAME
|---------------------------------------------------------------
|
| This variable must contain the name of your "config" folder.
|
*/
$config_folder = "config";


define('WAPATH_BASE', dirname(__FILE__) );
define( 'DS', DIRECTORY_SEPARATOR );
define('BASEPATH', $core_folder.DS);
define( 'WAPATH_SYSTEM',	 	WAPATH_BASE.DS.$core_folder.DS );
define( 'WAPATH_CONFIG',	 	WAPATH_BASE.DS.$config_folder.DS );
define('EXT', '.php');



require_once ( WAPATH_SYSTEM .DS.'defines'.EXT );

//load function libs
require(WAPATH_SYSTEM.DS.'utils/common'.EXT);





?>
