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
require_once(dirname(__FILE__).DS. "WaTemplateInterface.php");
class OriginalTemplate implements WaTemplateInterface {

    
    /**
     * Variables of the template file
     *
     * @var array
     */
    private $_myVariables = array();

    /**
     *
     * @param <String> template directory
     */
    private $template_dir = "";


     /**
     * Constructor method
     *
     */
	public function __construct()
	{	
		log_message('debug', "Original Template Class Initialized");
	}
	

    /**
     * @author: HuyNQ<huynq@vnext.vn>
     * @param <String> $dir folder of template
     */
    public function setTemplateDir($dir){
        if (!$dir) {
                throw new Exception("Template dir must be valid!");
        }
        $this->template_dir = $dir;
    }


    /**
     * @author: HuyNQ<huynq@vnext.vn>
     * @return <type>
     */
    public function getTemplateDir(){
        return $this->template_dir;
    }


    /**
     * @author: HuyNQ<huynq@vnext.vn>
     * @param <type> $key name of variable
     * @param <type> $value value of variable
     */
    public function assign($key, $value){
        if (!preg_match("/^(\w+)(\/\w+)*$/", $key)) {
                throw new FdocException("Key name accept with \"word\" character");
        }
        $this->_myVariables[$key] =& $value;
    }


    /**
     * @author: HuyNQ<huynq@vnext.vn>
     * @param <type> $partName name of template file
     * @param <type> $vars array of variabales to set for template
     * @param <type> $clearFlag 
     */
    public function viewPart($partName, $moduleName = '', & $vars = null, $clearFlag = 0){

        if ($clearFlag) unset( $this->_myVariables);
        
        $filePath = $partName;
        if(!file_exists($filePath)){
            $filePath = $this->template_dir.$partName.".php";
            if(!file_exists($filePath)){
                $filePath = $this->template_dir.$partName.".html";
            }
        }
		
		if(!file_exists($filePath) && $moduleName!=''){
			$filePath = $this->template_dir.$moduleName.DS.$partName;
			if(!file_exists($filePath)){
				$filePath = $this->template_dir.$moduleName.DS.$partName.".php";
				if(!file_exists($filePath)){
					$filePath = $this->template_dir.$moduleName.DS.$partName.".html";
				}
			}
		}


        if(file_exists($filePath)){
            $myVariables = array();
            if ((is_array($this->_myVariables))&&(is_array($vars))) {
                $myVariables = array_merge($this->_myVariables,$vars);
            }elseif (is_array($this->_myVariables)){
                $myVariables = $this->_myVariables;
            }elseif (is_array($vars)){
                $myVariables = $vars;
            }

            if ((isset($myVariables))&&(is_array($myVariables))) {
                foreach ($myVariables as $key => $value){
                    $$key = $value;
                }
            }

            include  $filePath;
        }else {
            show_error( "Template Not Found.");
            die;
        }

    }


    /**
     * @author: HuyNQ<huynq@vnext.vn>
     * @param <type> $partName name of template file
     * @param <type> $vars array of variabales to set for template
     * @param <type> $clearFlag
     * @return <String> content of templates part after set variables
     */
    public function getPart($partName, $moduleName = '', & $vars = null, $clearFlag = 0){
        $myContent = "Template Not Found.";
        if ($clearFlag) unset( $this->_myVariables);

        $filePath = $partName;
        if(!file_exists($filePath)){
            $filePath = $this->template_dir.$partName.".php";
            if(!file_exists($filePath)){
                $filePath = $this->template_dir.$partName.".html";
            }
        }
		
		if(!file_exists($filePath) && $moduleName!=''){
			$filePath = $this->template_dir.$moduleName.DS.$partName;
			if(!file_exists($filePath)){
				$filePath = $this->template_dir.$moduleName.DS.$partName.".php";
				if(!file_exists($filePath)){
					$filePath = $this->template_dir.$moduleName.DS.$partName.".html";
				}
			}
		}

        if(file_exists($filePath)){
            $myVariables = array();
            if ((is_array($this->_myVariables))&&(is_array($vars))) {
                $myVariables = array_merge($this->_myVariables,$vars);
            }elseif (is_array($this->_myVariables)){
                $myVariables = $this->_myVariables;
            }elseif (is_array($vars)){
                $myVariables = $vars;
            }

            if ((isset($myVariables))&&(is_array($myVariables))) {
                foreach ($myVariables as $key => $value){
                    $$key = $value;
                }
            }

            ob_start();
            include  $filePath;
            $myContent = ob_get_contents();
            ob_end_clean();
        }


        return $myContent;
    }

}
?>