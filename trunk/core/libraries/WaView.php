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
class WaView {

	protected $_objTemplate;
	
	protected $_controllerName = '';
        
        protected $agent = null;
        
        
        var $ajax = false;

    /**
     * Constructor method
     *
     */
    public function __construct() {
		$myTemp = "Original";
		if (config_item('template_engine')!==false) $myTemp = config_item('template_engine');
		$this->initTemplate($myTemp);
                
                $this->agent = load_class("UserAgent");
                
                
		log_message('debug', "View Class Initialized");
    }

	/**
	* 
	**/
	function initTemplate($myTemp){
		$class = $myTemp . "Template";
		$this->_objTemplate = load_class($class);
		
        if ($this->_objTemplate===false) {
            echo "Template Engine Layer Class Not Found.";
			log_message('error', "Template Engine Layer Class Not Found.");
            die;
        }
		
		$myTempDir = "templates";
		if (config_item('template_dir')!==false) $myTempDir = config_item('template_dir').DS;
		$this->_objTemplate->setTemplateDir($myTempDir);
	}
	
	public function setControllerName($controllerName){
		$this->_controllerName = $controllerName;
	}
	
    /**
     * Set template dir
     *
     * @param string $dir
     */
    function setTemplateDir($dir) {
        $this->_objTemplate->setTemplateDir($dir);
    }

    /**
     * get template dir
     *
     * @return string
     */
    public function getTemplateDir() {
        return $this->_objTemplate->getTemplateDir();
    }

   
    /**
     * call method assign() of object Smarty
     *
     * @param string $key
     * @param string $value
     */
    public function assign($key, $value) {
        $this->_objTemplate->assign($key, $value);
    }

    /**
     * display part of page
     *
     * @param string $partName
     * @param array $vars
     * @param boolean $clearFlag
     */
    public function viewPart($partName, & $vars = null, $clearFlag = 0) {
        $this->_objTemplate->viewPart($partName, $this->_controllerName, $vars, $clearFlag);
    }

    /**
     * return part of page as HTML string
     *
     * @param string $partName
     * @param array $vars
     * @param boolean $clearFlag
     */
    public function getPart($partName, & $vars = null, $clearFlag = 0) {
        return $this->_objTemplate->getPart($partName, $this->_controllerName, $vars, $clearFlag);
    }


	
    /**
     *
     * @param type $vars 
     * @return print result of a instant of request
     */
    public function showPage($vars = array()){
        
        if (!$this->ajax){
            $myPage = "index";
            if ($this->agent->is_mobile()) $myPage = "mobile";
            $this->viewPart($myPage , $vars);
        }else {
            echo json_encode($vars);
        }
    }
    
    
    
    public function buildForms($moduleName, $dataObjects, $valueArr = array()){
        if ($moduleName == null || $moduleName == "" || $dataObjects == null ||!is_array($dataObjects)) return false;
        $content = "<form action = '".  config_item('base_url').config_item('index_page')."/".$moduleName."/do_auto_form' method='post' accept-charset='utf-8' enctype='multipart/form-data' id = 'auto_".$moduleName."' name == 'auto_".$moduleName."'>\n";
        
        foreach ($dataObjects as $mCount => $dataObject) {
            
            $content .= "<input type = 'hidden' name = 'dataobject[]' value = '".$dataObject->getClassName()."' />";
            
            $itemViewDef = $dataObject->get_edit_block();
            if (is_array($itemViewDef)){
                foreach ($itemViewDef as $fieldName => $def){
                    $eleName = "WaElement".ucfirst($this->getElementName($def));
                    
                    $eleObject = load_class($eleName);
                    $content .= "<div class = 'common_form_row' >". $eleObject->render($def, (isset($valueArr[$mCount][$fieldName])?$valueArr[$mCount][$fieldName]:""))."</div>";
                }
            }
        }
        
        $content .= "</form>\n";
        return $content;
    }
    
    
    private function getElementName($def){
        $eleName = "text";
        
        if (isset($def['type'])){
            switch ($def['type']) {
                case "string":
                    $eleName = "text";
                    break;
                case "text":
                    $eleName = "text";
                    break;
                case "float":
                    $eleName = "text";
                    break;
                case "datetime":
                    $eleName = "date";
                    break;
                case "date":
                    $eleName = "date";
                    break;
                case "datetime":
                    $eleName = "date";
                    break;
                case "timestamp":
                    $eleName = "date";
                    break;
                case "boolean":
                    $eleName = "radio";
                    break;
                case "integer":
                    if (isset($def['sources']) || isset($def['source_table']) ) 
                        $eleName = "select";
                    else
                        $eleName = "number";
                    
                    break;
            }
        }
        
        
        return $eleName;
    }
    
    
    

 
}
