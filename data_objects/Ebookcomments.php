<?php
defined( '_WAEXEC' ) or die( 'Restricted access' );

class Ebookcomments extends DataObject{

	

    function __construct() {
        parent::__construct();
    }


}

class Ebookcomments_DataDef {
    var $table = "dum_ebookcomments";
    var $column_def = array(
    'ebookcm_id' => array('type'=>'integer', 'key'=>'primary', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'ID'),
    'ebookcm_user_id' => array('type'=>'integer', 'length'=>'11', 'default'=>'0', 'null'=>false, 'label'=>'Userl'),
    'ebookcm_comment' => array('type'=>'text', 'null'=>true, 'charset'=>'utf8', 'label'=>'Comment'),
    'ebookcm_rank' => array('type'=>'integer', 'length'=>'4', 'default'=>'0', 'null'=>false, 'label'=>'Rank'),

    'ebookcm_delete_flag' => array('type'=>'boolean', 'default'=>'0', 'is_delete_flag' => true, 'label'=>'Delete'),
        
    'indexes'=>array('PRIMARY'=>array('column'=>'ebookcm_id')),
    'tableParameters'=>array('charset'=>'utf8', 'collate'=>'utf8_unicode_ci', 'engine'=>'InnoDB')
    );
	
	
}

?>
