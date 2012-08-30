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
class DATABASE_CONFIG {
	var $default = array(
		'driver' => 'mysql',
		'persistent' => false,
		'host' => 'localhost',
		'port' => '',
		'login' => 'root',
		'password' => '',
		'database' => 'dumme',
		'schema' => '',
		'prefix' => '',
		'encoding' => ''
	);
	var $test = array(
		'driver' => 'mysql',
		'persistent' => false,
		'host' => 'localhost',
		'port' => '',
		'login' => 'nowayfor_books',
		'password' => '123456798',
		'database' => 'nowayfor_books',
		'schema' => '',
		'prefix' => '',
		'encoding' => ''
	);
}
/* End of file database.php */
/* Location: ./system/application/config/database.php */
