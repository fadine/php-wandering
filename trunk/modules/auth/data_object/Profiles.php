<?php
defined( '_WAEXEC' ) or die( 'Restricted access' );

class Profiles extends DataObject{

	

    function __construct() {
        parent::__construct();
    }

}

class Profiles_DataDef {
    var $table = "dum_profiles";
    var $column_def = array(
    'pro_id' => array('type'=>'integer', 'key'=>'primary', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'ID'),
	'pro_user_id' => array('type'=>'integer', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'User ID'),
	'pro_hash' => array('type'=>'string', 'length'=>'32', 'null'=>true, 'charset'=>'utf8', 'label'=>'Hash'),
	'pro_system_generated_password' => array('type'=>'boolean', 'default'=>'0'),
  	'pwd_last_changed' => array('type'=>'datetime'),
	'pro_authenticate_id' => array('type'=>'string', 'length'=>'100', 'null'=>true, 'charset'=>'utf8', 'label'=>'Au ID'),
	'pro_first_name' => array('type'=>'string', 'length'=>'32', 'null'=>true, 'charset'=>'utf8', 'label'=>'First Name'),
	'pro_last_name' => array('type'=>'string', 'length'=>'32', 'null'=>true, 'charset'=>'utf8', 'label'=>'Last Name'),
	'pro_reports_to_id' => array('type'=>'integer', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'Report Id'),
	'pro_is_admin' => array('type'=>'boolean', 'default'=>'0', 'null'=>false, 'label'=>'Is Admin'),
	'pro_external_auth_only' => array('type'=>'boolean', 'default'=>'0', 'null'=>false, 'label'=>'External Auth'),
	'pro_receive_notifications' => array('type'=>'boolean', 'default'=>'1', 'null'=>false, 'label'=>'Receive Notification'),
	'pro_description' => array('type'=>'text', 'label'=>'Description'),
	'pro_date_modified' => array('type'=>'datetime', 'label'=>'Modified'),
	'pro_modified_by_id' => array('type'=>'integer', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'Modified ID'),
	'pro_created_by_id' => array('type'=>'integer', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'Created ID'),
	'pro_phone_home' => array('type'=>'string', 'length'=>'50', 'null'=>true, 'charset'=>'utf8', 'label'=>'First Name'),
	'pro_phone_mobile' => array('type'=>'string', 'length'=>'50', 'null'=>true, 'charset'=>'utf8', 'label'=>'Mobile'),
	'pro_phone_work' => array('type'=>'string', 'length'=>'50', 'null'=>true, 'charset'=>'utf8', 'label'=>'Work Num'),
	'pro_phone_other' => array('type'=>'string', 'length'=>'50', 'null'=>true, 'charset'=>'utf8', 'label'=>'Work Num2'),
	'pro_phone_fax' => array('type'=>'string', 'length'=>'50', 'null'=>true, 'charset'=>'utf8', 'label'=>'Fax'),
	'pro_status' => array('type'=>'string', 'length'=>'100', 'null'=>true, 'charset'=>'utf8', 'label'=>'Status'),
	'pro_address_street' => array('type'=>'string', 'length'=>'150', 'null'=>true, 'charset'=>'utf8', 'label'=>'Address Street'),
	'pro_address_city' => array('type'=>'string', 'length'=>'100', 'null'=>true, 'charset'=>'utf8', 'label'=>'City'),
	'pro_address_state' => array('type'=>'string', 'length'=>'100', 'null'=>true, 'charset'=>'utf8', 'label'=>'State'),
	'pro_address_country' => array('type'=>'string', 'length'=>'100', 'null'=>true, 'charset'=>'utf8', 'label'=>'Country'),
	'pro_address_postalcode' => array('type'=>'string', 'length'=>'20', 'null'=>true, 'charset'=>'utf8', 'label'=>'Postalcode'),
	'pro_delete_flag' => array('type'=>'boolean', 'default'=>'0', 'is_delete_flag' => true, 'label'=>'Deleted'),
	'pro_portal_only' => array('type'=>'boolean', 'default'=>'0', 'is_delete_flag' => true, 'label'=>'ForPortal'),
	'pro_employee_status' => array('type'=>'string', 'length'=>'100', 'null'=>true, 'charset'=>'utf8', 'label'=>'Emp Status'),
	'pro_messenger_id' => array('type'=>'string', 'length'=>'100', 'null'=>true, 'charset'=>'utf8', 'label'=>'Messenger ID (chaovietnam)'),
	'pro_messenger_type' => array('type'=>'string', 'length'=>'100', 'null'=>true, 'charset'=>'utf8', 'label'=>'Messenger Type'),
	'pro_is_group' => array('type'=>'boolean', 'default'=>'0', 'is_delete_flag' => true, 'label'=>'Is Group'),
	'pro_param' => array('type'=>'text', 'charset'=>'utf8', 'label'=>'First Name', 'label'=>'Parameters'),
        
    'indexes'=>array('PRIMARY'=>array('column'=>'pro_id')),
    'tableParameters'=>array('charset'=>'utf8', 'collate'=>'utf8_unicode_ci', 'engine'=>'InnoDB')
    );
	
	var $bean_map = null; //setup map of name all fields of table on view, null is name of input same name of data column name (key)

	
}

?>
