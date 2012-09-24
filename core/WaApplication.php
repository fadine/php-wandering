<?php

// no direct access
defined('_WAEXEC') or die('Restricted access');
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

class WaApplication {

    var $controller = null;
    var $default_controller = 'Content';
    var $default_method = 'index';
    var $default_type = 'ajax';

    function __construct() {
        $myUri = load_class('WaUri');
        $myUri->_fetch_url(config_item('controller_variable_name'), config_item('function_variable_name'), config_item('prefix_input_parameter_name'));
        
        
        $myAutoLoad = config_item('auto_load_class');
        if (isset($myAutoLoad) && is_array($myAutoLoad)){
            foreach($myAutoLoad as $myClass){
                load_class($myClass, false);
            }
        }
        

        $controllerClass = $myUri->getControllerName();
        $controllerMethod = $myUri->getMethodName();
        $paramInputs = $myUri->getInputParams();

        $myContr = load_controller($controllerClass);
        if (isset($myContr) && isset($controllerMethod)) {
            pa_call_user_func_array($myContr, $controllerMethod, $paramInputs);
        } else {
            log_message('error', 'Controller Object And Method Invalid --> ' . $controllerClass . '->' . $controllerMethod);
            echo 'Controller Error-> ' . $controllerClass . '->' . $controllerMethod;
        }
    }

}

?>
