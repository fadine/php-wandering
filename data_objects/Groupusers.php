<?php
defined( '_WAEXEC' ) or die( 'Restricted access' );

class Groupusers extends DataObject{

	

    function __construct() {
        parent::__construct();
    }


}

class Groupusers_DataDef {
    var $table = "dum_group_users";
    var $column_def = array(
    'group_users_id' => array('type'=>'integer', 'key'=>'primary', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'ID'),
	'group_users_group_id' => array('type'=>'integer', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'Group ID'),
	'group_users_user_id' => array('type'=>'integer', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'User ID'),
        
    'indexes'=>array('PRIMARY'=>array('column'=>'group_users_id')),
    'tableParameters'=>array('charset'=>'utf8', 'collate'=>'utf8_unicode_ci', 'engine'=>'InnoDB')
    );
	
	var $bean_map = null; //setup map of name all fields of table on view, null is name of input same name of data column name (key)

}

?>
