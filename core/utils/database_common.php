<?php  
defined( '_WAEXEC' ) or die( 'Restricted access' );
/**
* Wandering PHP Framework
*
* PHP 5
*
* @package Wandering
* @author Nowayforback<nowayforback@gmail.com>
* @copyright Copyright (c) 2012, Nowayforback, (http://nowayforback.com) 
* @license http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
* @link http://nowayforback.com
* @since Version 1.0
* @filesource
*/
function set_join($tableName, $relationKey = '', $resetFlag = true){
    global $joinArr;
    if ($resetFlag) $joinArr = array();
    $joinArr['main_table'] = $tableName;
    if (trim($relationKey)!='') $joinArr['main_relation'] = $relationKey;
}

function left_join_left($joinTable, $joinKey, $mainKey = '', $mainTable = ""){
    global $joinArr;

    if ( trim($joinTable)=="" || trim($joinKey)=="" || ( trim($mainTable)=='' &&  $joinArr['main_table'] == "" ) ||( trim($mainKey)=='' && $joinArr['main_relation']=="" )  ){
        log_message('Error syntax in left join statement.');
    }

    $joinItem = array();
    $joinItem['type'] = 'LEFT JOIN';
    $joinItem['join_table'] = $joinTable;
    $joinItem['on_left'] = $joinTable.".".$joinKey;
    $joinItem['on_right'] = ( trim($mainTable)!=''?$mainTable:$joinArr['main_table'] ).".".( trim($mainKey)!=''?$mainKey:$joinArr['main_relation'] );
    $joinArr['join_items'][] = $joinItem;
}


function right_join($joinTable, $joinKey, $mainKey = '', $mainTable = ""){
    global $joinArr;

    if ( trim($joinTable)=="" || trim($joinKey)=="" || ( trim($mainTable)=='' &&  $joinArr['main_table'] == "" ) ||( trim($mainKey)=='' && $joinArr['main_relation']=="" )  ){
        log_message('Error syntax in right join statement.');
    }

    $joinItem = array();
    $joinItem['type'] = 'RIGHT JOIN';
    $joinItem['join_table'] = $joinTable;
    $joinItem['on_left'] = $joinTable.".".$joinKey;
    $joinItem['on_right'] = ( trim($mainTable)!=''?$mainTable:$joinArr['main_table'] ).".".( trim($mainKey)!=''?$mainKey:$joinArr['main_relation'] );
    $joinArr['join_items'][] = $joinItem;
}



function inner_join($joinTable, $joinKey, $mainKey = '', $mainTable = ""){
    global $joinArr;

    if ( trim($joinTable)=="" || trim($joinKey)=="" || ( trim($mainTable)=='' &&  $joinArr['main_table'] == "" ) ||( trim($mainKey)=='' && $joinArr['main_relation']=="" )  ){
        log_message('Error syntax in inner join statement.');
    }

    $joinItem = array();
    $joinItem['type'] = 'INNER JOIN';
    $joinItem['join_table'] = $joinTable;
    $joinItem['on_left'] = $joinTable.".".$joinKey;
    $joinItem['on_right'] = ( trim($mainTable)!=''?$mainTable:$joinArr['main_table'] ).".".( trim($mainKey)!=''?$mainKey:$joinArr['main_relation'] );
    $joinArr['join_items'][] = $joinItem;
}


function get_join(){
    return $joinArr;
}

function sqlFetch($sql){
    $sql = trim($sql);
    //$mySelect

}


/* End of file common.php */
/* Location: ./system/utils/common.php */
