<?php
defined( '_WAEXEC' ) or die( 'Restricted access' );

class Groupsxy extends DataObject{

	

    function __construct() {
        parent::__construct();
    }


}

class Groupsxy_DataDef {
    var $table = "dum_groups_xy";
    var $column_def = array(
    'groups_xy_id' => array('type'=>'integer', 'key'=>'primary', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'ID'),
	'groups_xy_parent_id' => array('type'=>'integer', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'Parent'),
	'groups_xy_child' => array('type'=>'integer', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'Child'),
        
    'indexes'=>array('PRIMARY'=>array('column'=>'groups_xy_id')),
    'tableParameters'=>array('charset'=>'utf8', 'collate'=>'utf8_unicode_ci', 'engine'=>'InnoDB')
    );
	
	var $bean_map = null; //setup map of name all fields of table on view, null is name of input same name of data column name (key)

}

?>
