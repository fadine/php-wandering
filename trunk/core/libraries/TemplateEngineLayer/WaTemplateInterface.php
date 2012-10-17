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
interface WaTemplateInterface {
    
    public function setTemplateDir($dir);
    public function getTemplateDir();
    public function assign($key, $value);
    public function viewPart($partName, $moduleName = '', & $vars = null, $clearFlag = 0);
    public function getPart($partName, $moduleName = '', & $vars = null, $clearFlag = 0);
    
}
?>