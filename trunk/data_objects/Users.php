<?php
defined( '_WAEXEC' ) or die( 'Restricted access' );

define('TABLE_USERS',"dum_users");
define('TABLE_GROUPS',"dum_groups");
define('TABLE_PROFILES',"dum_profiles");

class Users extends DataObject{

	

    function __construct() {
        parent::__construct();
    }

	
	/**
	 * Get user record by Id
	 *
	 * @param	int
	 * @param	bool
	 * @return	assoc array about user
	 */
	function get_user_by_id($user_id, $activated)
	{

		$conditions = array(
						array('field'=>'user_id', 'math'=>'=', 'value'=>$user_id), 
						array('keyword' => 'AND','field'=>'user_delete_flag', 'math'=>'=', 'value'=>0), 
						array('keyword' => 'AND','field'=>'user_activated', 'math'=>'=', 'value'=>($activated ? 1 : 0)));

		$myReturn = $this->myDb->simpleSelect(TABLE_USERS, "*", $conditions);
		if (isset($myReturn[0][TABLE_USERS])) return $myReturn[0][TABLE_USERS];
		return NULL;
	}

///////////////////////////////////

	/**
	 * Get user record by login (username or email)
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_login($login)
	{
		$conditions = array(
                                    array('field'=>'user_name', 'math'=>'=', 'value'=>strtolower($login)), 
                                    array('keyword' => 'OR','field'=>'user_email', 'math'=>'=', 'value'=>strtolower($login)));

		$myReturn = $this->myDb->simpleSelect(TABLE_USERS, "*", $conditions);
		if (isset($myReturn[0][TABLE_USERS])) return $myReturn[0][TABLE_USERS];
		return NULL;

	}

	/**
	 * Get user record by username
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_username($username)
	{
		$conditions = array(
						array('field'=>'user_name', 'math'=>'=', 'value'=>strtolower($username)), 
						array('keyword' => 'AND','field'=>'user_delete_flag', 'math'=>'=', 'value'=>0));

		$myReturn = $this->myDb->simpleSelect(TABLE_USERS, "*", $conditions);
		if (isset($myReturn[0][TABLE_USERS])) return $myReturn[0][TABLE_USERS];
		return NULL;

	}

	/**
	 * Get user record by email
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_email($email)
	{
		$conditions = array(
						array('field'=>'user_email', 'math'=>'=', 'value'=>strtolower($email)), 
						array('keyword' => 'AND','field'=>'user_delete_flag', 'math'=>'=', 'value'=>0));

		$myReturn = $this->myDb->simpleSelect(TABLE_USERS, "*", $conditions);
		if (isset($myReturn[0][TABLE_USERS])) return $myReturn[0][TABLE_USERS];
		return NULL;
	}

	/**
	 * Check if username available for registering
	 *
	 * @param	string
	 * @return	bool
	 */
	function is_username_available($username)
	{
		$conditions = array(array('field'=>'user_name', 'math'=>'=', 'value'=>strtolower($username)));
		$myReturn = $this->myDb->simpleSelect(TABLE_USERS, "1", $conditions);
		return (isset($myReturn[0]));
	}

	/**
	 * Check if email available for registering
	 *
	 * @param	string
	 * @return	bool
	 */
	function is_email_available($email)
	{
		$conditions = array(array('field'=>'user_email', 'math'=>'=', 'value'=>strtolower($email)));
		$myReturn = $this->myDb->simpleSelect(TABLE_USERS, "1", $conditions);
		return (isset($myReturn[0]));
	}


////////////////////////////

	/**
	 * Create new user record
	 *
	 * @param	array
	 * @param	bool
	 * @return	array
	 */
	function create_user($data, $activated = TRUE)
	{
		$data['user_date_entered'] = date('Y-m-d H:i:s');
		$data['user_activated'] = $activated ? 1 : 0;

		$myReturn = $this->myDb->insert(TABLE_USERS, $data);

		if ($myReturn){
			$user_id = $this->myDb->lastInsertId(TABLE_USERS);
			if ($activated)	$this->create_profile($user_id);
			return array('user_id' => $user_id);
		}
		return false;

	}

	/**
	 * Activate user if activation key is valid.
	 * Can be called for not activated users only.
	 *
	 * @param	int
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function activate_user($user_id)
	{

		$conditions = array(array('field'=>'user_id', 'math'=>'=', 'value'=>strtolower($user_id)),
                                    array('keyword' => 'AND', 'field'=>'user_delete_flag', 'math'=>'=', 'value'=>0));
		$myReturn = $this->myDb->simpleSelect(TABLE_USERS, "1", $conditions);
		if (!isset($myReturn[0])) return false;
                
                $data['user_activated'] = 1;
                $myReturn = $this->myDb->update(TABLE_USERS, $data, $conditions);
                return $myReturn;
	}
        
        
        
        /**
	 * Delete user record
	 *
	 * @param	int
	 * @return	bool
	 */
	function delete_user($user_id)
	{
            $conditions = array(array('field'=>'user_id', 'math'=>'=', 'value'=>strtolower($user_id)),
                                    array('keyword' => 'AND', 'field'=>'user_delete_flag', 'math'=>'=', 'value'=>0));
            $myReturn = $this->myDb->simpleSelect(TABLE_USERS, "1", $conditions);
            if (!isset($myReturn[0])) return false;

            
            $conditions = array(array('field'=>'user_id', 'math'=>'=', 'value'=>strtolower($user_id)));
            $data['user_delete_flag'] = 1;
            $myReturn = $this->myDb->update(TABLE_USERS, $data, $conditions);
            if ($myReturn==true) $this->delete_profile($user_id);
            return $myReturn;
	}
        
        
        
        /**
	 * Create an empty profile for a new user
	 *
	 * @param	int
	 * @return	bool
	 */
	private function create_profile($user_id)
	{
            $conditions = array(array('field'=>'user_id', 'math'=>'=', 'value'=>strtolower($user_id)),
                                    array('keyword' => 'AND', 'field'=>'user_delete_flag', 'math'=>'=', 'value'=>0));
            $myReturn = $this->myDb->simpleSelect(TABLE_USERS, "1", $conditions);
            if (!isset($myReturn[0])) return false;
            
            
            $conditions = array(array('field'=>'pro_user_id', 'math'=>'=', 'value'=>strtolower($user_id)),
                                    array('keyword' => 'AND', 'field'=>'pro_delete_flag', 'math'=>'=', 'value'=>0));
            $myReturn = $this->myDb->simpleSelect(TABLE_PROFILES, "1", $conditions);
            if (isset($myReturn[0])) return false;
            
            $data['pro_user_id'] = $user_id;
            $myReturn = $this->myDb->insert(TABLE_PROFILES, $data);
            if ($myReturn){
                    $profile_id = $this->myDb->lastInsertId(TABLE_PROFILES);
                    return array('profile_id' => $profile_id);
            }
            return false;
	}

	/**
	 * Delete user profile
	 *
	 * @param	int
	 * @return	void
	 */
	private function delete_profile($user_id)
	{
            $conditions = array(array('field'=>'pro_user_id', 'math'=>'=', 'value'=>strtolower($user_id)),
                                    array('keyword' => 'AND', 'field'=>'pro_delete_flag', 'math'=>'=', 'value'=>0));
            $myReturn = $this->myDb->simpleSelect(TABLE_PROFILES, "1", $conditions);
            if (!isset($myReturn[0])) return false;
            
            $data['pro_delete_flag'] = 1;
            $myReturn = $thisi->myDb->update(TABLE_PROFILES, $data, $conditions);
            return $myReturn;
		
	}
        
        
        



}

class Users_DataDef {
    var $table = "dum_users";
    var $column_def = array(
    'user_id' => array('type'=>'integer', 'key'=>'primary', 'length'=>'11', 'default'=>'0', 'null'=>false),
  	'user_date_entered' => array('type'=>'datetime'),
  	'user_last_visit' => array('type'=>'timestamp'),
  	'user_name' => array('type'=>'string', 'length'=>'50', 'null'=>false, 'charset'=>'utf8'),
  	'user_screen' => array('type'=>'string', 'length'=>'50', 'charset'=>'utf8'),
  	'user_password' => array('type'=>'string', 'length'=>'255', 'charset'=>'utf8', 'null'=>false),
  	'user_email' => array('type'=>'string', 'length'=>'120', 'null'=>false, 'charset'=>'utf8'),
  	'user_salt' => array('type'=>'string', 'length'=>'50', 'charset'=>'utf8'),
	'user_login' => array('type'=>'boolean', 'default'=>'0'),
	'user_activated' => array('type'=>'boolean', 'default'=>'0', 'null'=>false),
	'user_delete_flag' => array('type'=>'boolean', 'default'=>'0', 'is_delete_flag' => true),
	'user_param' => array('type'=>'text'),
        
    'indexes'=>array('PRIMARY'=>array('column'=>'user_id')),
    'tableParameters'=>array('charset'=>'utf8', 'collate'=>'utf8_unicode_ci', 'engine'=>'InnoDB')
    );
}

?>
