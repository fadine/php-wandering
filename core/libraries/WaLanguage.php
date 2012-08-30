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
class WaLanguage {

    public static $global_lang_label;
    
    var $module_lang_label;
    //var $global_lang_label;
    
    private $module_name;
    
    var $session;
    
    var $default_lang;
    var $userLanguage;

    /**
     * Constructor method
     *
     */
    public function __construct($module_name) {

        //TODO
        
        $this->module_name = $module_name;
        
        $this->default_lang = config_item('language');
        
        $this->session = load_class("WaSession");
        $myUserCurrentData = $this->session->all_userdata();
        
        if (isset($myUserCurrentData['user_language'])) {
            $this->userLanguage = $myUserCurrentData['user_language'];
        }else{
            $this->userLanguage = $default_lang;
        }
        
        if (config_item('modul_folders') && file_exists(WAPATH_BASE.DS.config_item('modul_folders').DS.strtolower($module_name).DS."languages".DS.$this->userLanguage.EXT) ){
            require_once(WAPATH_BASE.DS.config_item('modul_folders').DS.strtolower($module_name).DS."languages".DS.$this->userLanguage.EXT);
            if (isset($lang) && is_array($lang)) $this->module_lang_label = $lang;
        }
        
        log_message('debug', "View Class Initialized");
    }
    
    
    
    public function get_label($str_key){
        if (isset($this->module_lang_label[$str_key])) {
            return $this->module_lang_label[$str_key];
        }
            
        if (isset(WaLanguage::$global_lang_label)) {
            if (isset(WaLanguage::$global_lang_label[$str_key])) {
                return WaLanguage::$global_lang_label[$str_key];
            }
        }elseif (file_exists(config_item('global_lang_dir').DS.$this->userLanguage.EXT) ){
            unset($lang);
            require_once(config_item('global_lang_dir').DS.$this->userLanguage.EXT);
            if (isset($lang) && is_array($lang)) WaLanguage::$global_lang_label = $lang;
            if (isset(WaLanguage::$global_lang_label[$str_key])) {
                return WaLanguage::$global_lang_label[$str_key];
            }
        }
        
        if (file_exists(config_item('cache_lang_dir').DS.$this->userLanguage.EXT) ){
            require_once(config_item('cache_lang_dir').DS.$this->userLanguage.EXT);
            if (isset($cache_lang[$str_key])) {
                return $cache_lang[$str_key];
            }
        }
        
        
        return $str_key;
    }
        
    
    
    function update_label_to_module($label_arr){
        load_helper('file_helper');
        $have_new = false;
        if (!isset($this->module_lang_label)) return false;
        if (!is_array($label_arr)) return false;
        foreach ($label_arr as $key => $label) {
            if (!isset($this->module_lang_label[$key])) {
                $this->module_lang_label[$key] = $label;
                $have_new = true;
            }
        }
        
        if (config_item('modul_folders') && file_exists(WAPATH_BASE.DS.config_item('modul_folders').DS.strtolower($module_name).DS."languages".DS.$userLanguage.EXT) && $have_new) {
            $pre_text = "<?php \n defined( '_WAEXEC' ) or die( 'Restricted access' );\n".'$lang = ';
            write_file(WAPATH_BASE.DS.config_item('modul_folders').DS.strtolower($module_name).DS."languages".DS.$userLanguage.EXT, $pre_text . var_export($this->module_lang_label) . ";");
        }
    }
    

 
}
