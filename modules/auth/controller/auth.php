<?php

class Auth extends WaController
{
	
        var $myView;
		
        var $session;
        
        var $ajax = false;
	
	// Constructor function
	public function __construct()
	{
            parent::__construct();
            $this->myView = $this->getView('AuthView');

            $this->session = load_class("WaSession");
            
            $this->ajax = is_bool($this->requestVar('ajax'))?$this->requestVar('ajax'):false;
            $this->myView->ajax = $this->ajax;
	}
	
        
	public function show_login(){
		$myUserCurrentData = $this->session->all_userdata();
		if (isset($myUserCurrentData['logged_in']) && $myUserCurrentData['logged_in']==TRUE && isset($myUserCurrentData['username'])) {
                    $vars['login_name'] = $myUserCurrentData['username'];
                    $this->myView->show_loged($vars);
                }else {
                    $this->myView->show_login();
                }
	}
        
        
	public function login(){
			
		$myUserCurrentData = $this->session->all_userdata();
		
                if (isset($myUserCurrentData['logged_in']) && $myUserCurrentData['logged_in']==TRUE && isset($myUserCurrentData['username'])) {
                    $vars['login_name'] = $myUserCurrentData['username'];
                    $this->myView->show_loged($vars);
                }else {
	
                    $userName = $this->requestVar('login_name');
                    $userPassword = $this->requestVar('password');

                    $myModel = load_mode('authmode', 'auth');

                    //$myModel->create_table();
                    $mDataSessions = load_class("Sessions");
                    $mDataSessions->createTable();

                    $data = $myModel->get_user_by_login($userName);
                    if ($data['user_password']==md5($userPassword.config_item('encryption_key'))) {
                            //var_dump($data);
                        $newdata = array(
                           'username'  => $userName,
                           'logged_in' => TRUE                    
                        );

                        $this->session->set_userdata($newdata);
                        $vars['login_name'] = $userName;
                        $this->myView->show_loged($vars);

                    }else{
                        $vars['login_error'] = "Username or password be incorrect.";
                        $this->myView->show_login($vars);
                    }
                }
	}
        
        
        public function logout(){
            $this->session->sess_destroy();
            $vars['login_info'] = "Logout is success.";
            $this->myView->show_login($vars);
        }
        
        
        public function show_register(){
            $myUserCurrentData = $this->session->all_userdata();
		
            if (isset($myUserCurrentData['logged_in']) && $myUserCurrentData['logged_in']==TRUE && isset($myUserCurrentData['username'])) {
                $vars['login_name'] = $myUserCurrentData['username'];
                $this->myView->show_loged($vars);
            }else {
                $this->myView->show_register();
            }
        }
        
        public function register(){
            $myUserCurrentData = $this->session->all_userdata();
		
            if (isset($myUserCurrentData['logged_in']) && $myUserCurrentData['logged_in']==TRUE && isset($myUserCurrentData['username'])) {
                $vars['login_name'] = $myUserCurrentData['username'];
                $this->myView->show_loged($vars);
            }else {
                $userName = $this->requestVar('login_name');
                $userPassword = $this->requestVar('password');
                $userEmail = $this->requestVar('email');
                
				
                $myModel = load_mode('authmode', 'auth');
                $data = $myModel->get_user_by_login($userName);
                if (isset($data['user_name'])) {
                    $vars['register_error'] = "Username in use.";
                    $this->myView->show_register($vars);
                }else {
                    if (strlen($userName)<3) $errorArr .= "Username is too short.<br />";  
                    if (strlen($userPassword)<3) $errorArr .= "Password is too short.<br />";  
                    if (strlen($userEmail)<3) $errorArr .= "Email is too short.<br />";  
                    
                    if ($errorArr!="") {
                        $vars['register_error'] = $errorArr;
                        $this->myView->show_register($vars);
                    }else{
                        $newUser = array('user_name'=>$userName, 'user_password'=>md5($userPassword.config_item('encryption_key')), 'user_email'=>$userEmail);
                        $myModel->create_user($newUser, true);
                        
                        $newUserData = array(
                           'username'  => $userName,
                           'logged_in' => TRUE                    
                        );

                        $this->session->set_userdata($newUserData);
                        
                        $vars['login_name'] = $userName;
                        $this->myView->show_reg_success($vars);
                    }
                    
                }
            }
        }


}
