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
class SmartyTemplate implements WaTemplateInterface {

	/**
     *
     * @param <String> template directory
     */
    private $template_dir = "";
	
	
    /**
     * Object Smarty
     *
     * @var Smarty
     */
    protected $_objSmarty = null;
	    
    /**
     * Constructor method
     *
     * @param FdocController $objCtrl
     */
    public function __construct(){

		load_other_config('smarty');
		
        $this->_objSmarty = load_class('Smarty');

		$compile_check = config_item('smarty_compile_check');
        $this->_objSmarty->compile_check = $compile_check;


        $fdocCacheDir = WAPATH_BASE.DS.config_item('template_cache');
        if (!is_dir($fdocCacheDir)) {
                PaFileSystem::mkdirs($fdocCacheDir);
        }
        $this->_objSmarty->cache_dir    = $fdocCacheDir . DS;

        $mCacheDir = WAPATH_BASE.DS.config_item('template_compile');
        if (!is_dir($mCacheDir)) {
                PaFileSystem::mkdirs($mCacheDir);
        }
        
        $this->_objSmarty->compile_dir  = $mCacheDir . DS;
        
        $this->_objSmarty->config_dir   = WAPATH_BASE.DS.config_item('config') . DS;

		$this->_objSmarty->debugging = config_item('smarty_debug');
        
		log_message('debug', "Smarty Template Class Initialized");

    }
    
    /**
     * Set template dir
     *
     * @param string $dir
     */
    public function setTemplateDir($dir) {
            if (!$dir) {
                    throw new Exception("Template dir must be valid!");
            }
            $this->_objSmarty->template_dir = $dir;
			$this->template_dir = $dir;
    }


    /**
     * get template dir
     *
     * @return string
     */
    public function getTemplateDir() {
            return $this->_objSmarty->template_dir;
    }



    /**
     * call method assign() of object Smarty
     *
     * @param string $key
     * @param string $value
     */
    public function assign($key, $value) {
            $this->_objSmarty->assign($key, $value);
    }

    /**
     * display part of page
     *
     * @param string $partName
     * @param array $vars
     * @param boolean $clearFlag
     */
    public function viewPart($partName, $moduleName = '', & $vars = null, $clearFlag = 0) {
            if ($clearFlag)
                    $this->_objSmarty->clear_all_assign();

            if (!isset($vars))
                    $this->_objSmarty->display($partName . ".html");

            elseif (is_array($vars)) {
                    foreach ($vars as $key => $value) {
                            $this->_objSmarty->assign($key, $value);
                    }

                    $this->_objSmarty->display($partName . ".html");
            }

            else {
                    throw new FdocException("[viewpart] \$vars must be an array");
            }
    }

    /**
     * return part of page as HTML string
     *
     * @param string $partName
     * @param array $vars
     * @param boolean $clearFlag
     */
    public function getPart($partName, $moduleName = '', & $vars = null, $clearFlag = 0) {

            if ($clearFlag) {
                    $this->_objSmarty->clear_all_assign();
            }

            try {
                    if (is_array($vars)) {
                            foreach ($vars as $key => $value) {
                                    $this->_objSmarty->assign($key, $value);
                            }
                    }

                    return $this->_objSmarty->fetch($partName . ".html");
            } catch (FdocException $e) {
                    throw new FdocException($e->getMessage());
            }
    }


    /**
     * @author: HuyNQ<huynq@vnext.vn>
     * return orinal Smarty Object, only service for Backward Compatibility
     */
    public function & getSmartyObject(){
        return $this->_objSmarty;
    }


}
?>