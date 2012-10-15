<?php

class AuthView extends WaView {
    
    var $ajax = false;
       
    var $lang = null;
    
    
    /**
     * 
     */
    function __construct() {
        parent::__construct();
        load_class("WaLanguage", false);
        $this->lang = new WaLanguage('auth');
        
        //$this->setTemplateDir(dirname(__FILE__).DS."html");
    }

    
    
    public function show_login($varsIn = array()){
        if ($varsIn==null) $varsIn = array();
        $varsIn['lang'] = $this->lang;
        $vars['main_content'] = $this->getPart(dirname(__FILE__).DS."html/login.html", $varsIn);
        $this->showPage($vars);
    }
    
    
    public function show_loged($varsIn){
        $vars['main_content'] = $this->getPart(dirname(__FILE__).DS."html/loged.html", $varsIn);
        $vars['lang'] = $this->lang;
        $this->showPage($vars);
    }
    
    
    public function show_register($varsIn){
        if ($varsIn==null) $varsIn = array();
        $varsIn['lang'] = $this->lang;
        $vars['main_content'] = $this->getPart(dirname(__FILE__).DS."html/register.html", $varsIn);
        
        $this->showPage($vars);
    }
    
	public function show_reg_success($varsIn){
        if ($varsIn==null) $varsIn = array();
        $varsIn['lang'] = $this->lang;
        $vars['main_content'] = $this->getPart(dirname(__FILE__).DS."html/reg_success.html", $varsIn);
        $this->showPage($vars);
    }
    
    public function testView($b) {
        echo "he he he ".$b;
    }

}