<?php

class AuthView extends WaView {
    
    var $ajax = false;
       
    /**
     * 
     */
    function __construct() {
        parent::__construct();
        //$this->setTemplateDir(dirname(__FILE__).DS."html");
    }

    
    
    public function show_login($varsIn = array()){
        $vars['main_content'] = $this->getPart(dirname(__FILE__).DS."html/login.html", $varsIn);
        $this->showPage($vars);
    }
    
    
    public function show_loged($varsIn){
        $vars['main_content'] = $this->getPart(dirname(__FILE__).DS."html/loged.html", $varsIn);
        $this->showPage($vars);
    }
    
    
    public function show_register($varsIn){
        $vars['main_content'] = $this->getPart(dirname(__FILE__).DS."html/register.html", $varsIn);
        $this->showPage($vars);
    }
    
	public function show_reg_success($varsIn){
        $vars['main_content'] = $this->getPart(dirname(__FILE__).DS."html/reg_success.html", $varsIn);
        $this->showPage($vars);
    }
    
    public function testView($b) {
        echo "he he he ".$b;
    }

}