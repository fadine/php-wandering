<?php
defined( '_WAEXEC' ) or die( 'Restricted access' );

class Groups extends DataObject{

	

    function __construct() {
        parent::__construct();
    }


}

class Groups_DataDef {
    var $table = "dum_groups";
    var $column_def = array(
    'group_id' => array('type'=>'integer', 'key'=>'primary', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'ID'),
    'group_level' => array('type'=>'integer', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'Level'),
    'group_name' => array('type'=>'string', 'length'=>'100', 'null'=>false, 'charset'=>'utf8', 'label'=>'Name'),
    'group_description' => array('type'=>'text', 'null'=>true, 'charset'=>'utf8', 'label'=>'Description'),
        
    'indexes'=>array('PRIMARY'=>array('column'=>'group_id')),
    'tableParameters'=>array('charset'=>'utf8', 'collate'=>'utf8_unicode_ci', 'engine'=>'InnoDB')
    );
	
	
}

?>
