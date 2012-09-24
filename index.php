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


$startTime = microtime(true);

define( 'EXT', '.php' );

require_once(dirname(__FILE__) . '/entryPoint'.EXT);
if (defined('WAPATH_SYSTEM')) {
    require_once(WAPATH_SYSTEM.DS.'libraries/WaController'.EXT);
    require_once(WAPATH_SYSTEM.DS.'libraries/WaView'.EXT);
    require_once(WAPATH_SYSTEM .DS.'WaApplication'.EXT);
    $app = new WaApplication();
}

