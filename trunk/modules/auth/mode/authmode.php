<?php
defined( '_WAEXEC' ) or die( 'Restricted access' );

class Authmode{

        /*
         * @type: user dataobject
         */
	private $users;
        
        /*
         * @type: groups dataobject
         */
        private $groups;
        
        /*
         * @type: profiles dataobject
         */
        private $profiles;

    function __construct()
    {
        
        $this->users = load_data_object('Users', 'auth');
        $this->groups = load_data_object("Groups", 'auth');
        $this->profiles = load_data_object("Profiles", 'auth');
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

		return $this->users->get_user_by_id($user_id, $activated);
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
		return $this->users->get_user_by_login($login);

	}

	/**
	 * Get user record by username
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_username($username)
	{
		return $this->users->get_user_by_username($username);

	}

	/**
	 * Get user record by email
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_email($email)
	{
		return $this->users->get_user_by_email($email);
	}

	/**
	 * Check if username available for registering
	 *
	 * @param	string
	 * @return	bool
	 */
	function is_username_available($username)
	{
		return $this->users->is_username_available($username);
	}

	/**
	 * Check if email available for registering
	 *
	 * @param	string
	 * @return	bool
	 */
	function is_email_available($email)
	{
		return $this->users->is_email_available($email);
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
		return $this->users->create_user($data, $activated = TRUE);

	}

	
	public function create_table(){
		$this->users->createTable();
                $this->groups->createTable();
                $this->profiles->createTable();
                
                $groupsxy = load_data_object("Groupsxy", 'auth'); //load_class("Groupsxy");
                if ($groupsxy!=null) $groupsxy->createTable();
                
                $groupusers = load_data_object("Groupusers", 'auth'); //load_class("Groupusers");
                if ($groupusers!=null) $groupusers->createTable();
	}
	

}

?>
