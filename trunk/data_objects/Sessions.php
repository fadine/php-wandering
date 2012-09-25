<?php
defined( '_WAEXEC' ) or die( 'Restricted access' );


class Sessions extends DataObject{

	

    function __construct() {
        parent::__construct();
    }

	
}

class Sessions_DataDef {
    var $table = "dum_sessions";
    var $column_def = array(
    'session_id' => array('type'=>'string', 'key'=>'primary', 'length'=>'40', 'default'=>'', 'null'=>false, 'label'=>'Id'),
	'session_ip_address' => array('type'=>'string', 'length'=>'16', 'null'=>false, 'charset'=>'utf8', 'label'=>'Ip'),
	'session_user_agent' => array('type'=>'string', 'length'=>'50', 'null'=>true, 'charset'=>'utf8', 'label'=>'User Agent'),
	'session_last_activity' => array('type'=>'integer', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'Last Activity'),
	'session_user_data' => array('type'=>'text', 'label'=>'User Data'),
	    
    'indexes'=>array('PRIMARY'=>array('column'=>'session_id')),
    'tableParameters'=>array('charset'=>'utf8', 'collate'=>'utf8_unicode_ci', 'engine'=>'InnoDB')
    );
	
	var $bean_map = null; //setup map of name all fields of table on view, null is name of input same name of data column name (key)
	
}

?>
