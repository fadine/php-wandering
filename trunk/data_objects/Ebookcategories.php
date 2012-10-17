<?php
defined( '_WAEXEC' ) or die( 'Restricted access' );


class Ebookcategories extends DataObject{

	

    function __construct() {
        parent::__construct();
    }
 
}

class Ebookcategories_DataDef {
    var $table = "dum_ebookrcategories";
    var $column_def = array(
    'ebookcat_id' => array('type'=>'integer', 'key'=>'primary', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'ID'),
    'ebookcat_name' => array('type'=>'string', 'length'=>'100', 'null'=>false, 'charset'=>'utf8', 'label'=>'Name'),
    'ebookcat_description' => array('type'=>'text', 'null'=>true, 'charset'=>'utf8', 'label'=>'Description'),

    'ebookcat_language' => array('type'=>'string', 'length'=>'2', 'null'=>false, 'charset'=>'utf8', 'label'=>'Language'),
        
    'ebookcat_delete_flag' => array('type'=>'boolean', 'default'=>'0', 'is_delete_flag' => true, 'label'=>'Delete'),
    'ebookcat_params' => array('type'=>'text', 'label'=>'Params'),
	    
    'indexes'=>array('PRIMARY'=>array('column'=>'ebookcat_id')),
    'tableParameters'=>array('charset'=>'utf8', 'collate'=>'utf8_unicode_ci', 'engine'=>'InnoDB')
    );
	
}

?>
