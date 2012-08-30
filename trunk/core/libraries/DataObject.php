<?php
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
class DataObject {
    
    protected $myClassName = "DataObject";
    protected $myDataDefClass = null;
    protected $dbManager;
    protected $myDb;
	
	protected $_request;


    function __construct(){
        $this->myClassName = get_class($this);
        $myDataDefClassName = $this->myClassName."_DataDef";
		if (class_exists($myDataDefClassName)) $this->myDataDefClass = new $myDataDefClassName();
        $this->dbManager = load_class("DatabaseManager");
        $this->myDb = $this->dbManager->getDataSource("default");
		
		$this->_request = load_class("WaRequest");
    }
    
    
    public function getClassName(){
        return $this->myClassName;
    }
    
    
    public function createTable(){
        if (isset($this->myDataDefClass)){
            $myTableName = $this->myDataDefClass->table;
            $myTableDef = $this->myDataDefClass->column_def;
            if ($myTableDef==null || $myTableName==null) return null;
            $createSql = $this->myDb->createSchema($myTableDef, $myTableName);
            return $this->myDb->execute($createSql);
        }else {
            log_message('error', "Data Define ".$this->myClassName." not set.");
            return null;
        }
        
    }

	
	public function insert_data($data, $table_name = null){
		if (is_array($data) && $table_name == null && isset($this->myDataDefClass)) {
			$myReturn = $this->myDb->insert($this->myDataDefClass->table, $data);
			if ($myReturn){
				$my_id = $this->myDb->lastInsertId($this->myDataDefClass->table);
				return $my_id;
			}else return false;
		}elseif(is_array($data) && $table_name != null){
			$myReturn = $this->myDb->insert($table_name, $data);
			if ($myReturn){
				$my_id = $this->myDb->lastInsertId($table_name);
				return $my_id;
			}else return false;
		}
	}



	public function update_data($data, $key_name = null, $table_name = null){
		if ($table_name == null && $this->myDataDefClass->table!=null) $table_name = $this->myDataDefClass->table;
		if ($key_name==null){
			$key_name = $this->get_key_name();
		}
		if ($data == null || $key_name == null || $key_name == false || $table_name == null) return false;

		$condition = array();
		if (!is_array($key_name) && isset($data[$key_name])) {
 			$condition[] = array('field'=>$key_name, 'math'=>'=', 'value'=>$data[$key_name]);
			unset($data[$key_name]);
		}elseif(is_array($key_name)) {
			foreach($key_name as $val_con){
				if (isset($data[$val_con])) {
					$condition[] = array('field'=>$val_con, 'math'=>'=', 'value'=>$data[$val_con]);
					unset($data[$val_con]);
				}
			}
		}
		
		if (!isset($condition[0])) return false;

		return $this->myDb->update($table_name, $data, $condition);
	}

    

	public function delete_logic($key_value, $key_name = null, $table_name = null){
		if ($table_name == null && $this->myDataDefClass->table!=null) $table_name = $this->myDataDefClass->table;
		if ($key_name==null){
			$key_name = $this->get_key_name();
		}
		
		$delete_flag = $this->get_delete_flag_name();

		if ($key_value == null || $key_value == "" || $key_name == null || $key_name == false || $table_name == null || $delete_flag == false) return false;

		$data = array($delete_flag=>1);

		$condition = array();
		if (!is_array($key_name) && isset($data[$key_name])) {
 			$condition[] = array('field'=>$key_name, 'math'=>'=', 'value'=>$key_value);
		}elseif(is_array($key_name) && is_array($key_value)) {
			foreach($key_name as $val_con){
				if (isset($data[$val_con]) && isset($key_value[$val_con])) {
					$condition[] = array('field'=>$val_con, 'math'=>'=', 'value'=>$key_value[$val_con]);
				}
			}
		}
		
		if (count($condition)) return false;

		return $this->myDb->update($table_name, $data, $condition);
	}
	
        
        
        
        /**
         *
         * @param type $rawData
         * @return type mixed
         * get data array of bean assoc if only one reacord, array of assoc if multi records tranfer by request
         */
        public function getObjectDataFromRawData($rawData){
            if (!isset($this->myDataDefClass->column_def)) return false;
            $dataArr = array();
            
            $countRecord = 1;
            
            foreach ($this->myDataDefClass->column_def as $keyName => $def){
                $paramName = $keyName;
                
                if (isset($def['map_name'])) $paramName = $def['map_name'];
                
                if (isset($rawData[$paramName]) && !is_array($rawData[$paramName])) {
                    $dataArr[$keyName] = $rawData[$paramName];
                }elseif (isset($rawData[$paramName]) && is_array($rawData[$paramName])) {
                    $myCount = count($rawData[$paramName]);
                    $countRecord = ($countRecord>=$myCount)?$countRecord:$myCount;
                    if ($myCount==1) {
                        $dataArr[$keyName] = current($rawData[$paramName]);
                        
                    }elseif ($myCount>1){
                        foreach($rawData[$paramName] as $val){
                            $dataArr[$keyName][] = $val;
                        }
                    }
                }
            }
            
            if ($countRecord==1) {
                return $dataArr;
            }else {
                $returnArr = array();
                for ($i = 0; $i < $countRecord; $i++){
                    $itemArr = array();
                    foreach ($dataArr as $key2 => $val2){
                        $itemArr[$key2] = (isset($val2[$i])?$val2[$i]:"");
                    }
                    
                    $returnArr[$i] = $itemArr;
                }
                return $returnArr;
            }
            
            return false;
        }
        
        

        /**
         *
         * @param type $data
         * @param type $checked
         * @return type 
         * commit (include insert, update)
         */
	public function commit($data, $checked = false){
		$table_name = $this->myDataDefClass->table;
		$key_name = $this->get_key_name();
		$delete_flag = $this->get_delete_flag_name();
		if ($data == null || $key_name == null || $key_name == false || $table_name == null || $table_name == "") return false;

		if ($checked==false) {
                    //unset cac bien ko trong bang
                    $myDef = $this->myDataDefClass->column_def;
                    $myDiff = array_diff_key($data, $myDef);
                    if (is_array($myDiff)) 
                        foreach ($myDiff as $key=>$val){
                            unset($data[$key]);
                        }
		}

		if (!isset($data[$key_name]) || $data[$key_name] === "" || $data[$key_name] ===false) {
                    //insert
                    return $this->insert_data($data, $table_name);
		}else {
                    //update, delete_logic
                    return $this->update_data($data, $key_name, $table_name);
		}

		return false;
	}
        
        
        public function commitMulti($data, $checked = false){
            if (!is_array($data)) return false;
            if (!is_array(current($data))) return $this->commit($data, $checked);
            
            $returnCheck = true;
            foreach ($data as $row){
                $returnCheck &= $this->commit($row, $checked);
            }
            return $returnCheck;
        }


	public function get_key_name(){
		if ($this->myDataDefClass==null || $this->myDataDefClass->column_def == null) return false;
		
		foreach ($this->myDataDefClass->column_def as $key => $def){		
			if ($key=="tableKeyName") return  $def;
			if ($key=="indexes" || $key=="tableParameters" || $key=="deleteKey") continue;
			if (is_array($def)){
				foreach ($def as $key_def=>$val_def){
					if (($key_def == "key" && $val_def == "primary")||($key_def == "type" && $val_def == "primary_key")) return $key;
				}
			}
		}

		return false;

	}


	public function get_delete_flag_name(){
		if ($this->myDataDefClass==null || $this->myDataDefClass->column_def == null) return false;
		
		foreach ($this->myDataDefClass->column_def as $key => $def){		
			if ($key=="deleteKey") return  $def;
			if ($key=="indexes" || $key=="tableParameters" || $key=="tableKeyName") continue;
			if (is_array($def)){
				foreach ($def as $key_def=>$val_def){
					if ($key_def == "is_delete_flag" && $val_def == true) return $key;
				}
			}
		}

		return false;

	}
        
        
        function get_entry_by_key($key_value)
	{
            $table_name = $this->myDataDefClass->table;
            $key_name = $this->get_key_name();
            $delete_flag = $this->get_delete_flag_name();
            if ($key_value == null || $key_value == "" || $key_name == null || $key_name == false || $table_name == null || $table_name == "") return false;

            $conditions = array(array('field'=>$key_name, 'math'=>'=', 'value'=>$key_value));
            if ($delete_flag!=null && $delete_flag!="" && $delete_flag!=false)
                $conditions[] = array('keyword' => 'AND','field'=>$delete_flag, 'math'=>'=', 'value'=>0);

            $myReturn = $this->myDb->simpleSelect($table_name, "*", $conditions);
            if (isset($myReturn[0])) return $myReturn[0];
            return NULL;
	}

        
        public function get_block($conditions = null, $orders = null, $page = null, $num_perpage = 20){
            $table_name = $this->myDataDefClass->table;
            $key_name = $this->get_key_name();
            $delete_flag = $this->get_delete_flag_name();
            if ($key_name == null || $key_name == false || $table_name == null || $table_name == "") return false;
            
            if ($delete_flag!=null && $delete_flag!="" && $delete_flag!=false)
                $conditions[] = array('keyword' => 'AND','field'=>$delete_flag, 'math'=>'=', 'value'=>0);
            
            $offset = false;
            $limit = false;
            if ($page!=null && is_int($page)) {
                $offset = ($page - 1)*$num_perpage;
                $limit = $num_perpage;
            }else {
                
            }
            
            $myReturn = $this->myDb->simpleSelect($table_name, "*", $conditions, $orders, null, $limit, $offset);
            if (isset($myReturn[0])) return $myReturn;
            return null;
        }
        
		
		
		/**
		* create view block defined
		*
		**/
		public function get_edit_block(){
                    if ($this->myDataDefClass==null || $this->myDataDefClass->column_def == null) return false;
                    $myEditViewBlock = array();

                    foreach ($this->myDataDefClass->column_def as $key => $def){
                        if ($key != "indexes" && $key != "tableParameters" && $key != "deleteKey") {
                            $myBlockItem = array();
                            $myBlockItem['map_name'] = isset($def['map_name'])?$def['map_name']:$key;
                            $myBlockItem['label'] = isset($def['label'])?$def['label']:"No label";
                            if (isset($def['description'])) $myBlockItem['description'] = $def['description'];
                            if (isset($def['size'])) $myBlockItem['size'] = $def['size'];
                            if (isset($def['class'])) $myBlockItem['class'] = $def['class'];

                            //for select by database
                            if (isset($def['source_table'])) $myBlockItem['source_table'] = $def['source_table'];
                            if (isset($def['source_conditions'])) $myBlockItem['source_conditions'] = $def['source_conditions'];
                            if (isset($def['source_code'])) $myBlockItem['source_code'] = $def['source_code'];
                            if (isset($def['source_label'])) $myBlockItem['source_label'] = $def['source_label'];

                            //for select by enum
                            if (isset($def['sources'])) $myBlockItem['sources'] = $def['sources'];
                            
                            $myEditViewBlock[$key] = $myBlockItem;
                        }
                    }
                    
                    return $myEditViewBlock;
		}
		
		
		
/////////////////////

	public function getParams() {
        return $this->_request->getParams();
    }

    /*
     * @author: Nowayorback<nowayforback@gmail.com>
     * @version: 1.0
     * @params:
     * @return: array of old conditions from request, format like
     *      $conditions = array();
     *      $conditions['doc_id'] = '123';
     */
    public function buildOldMasterCondotionFromRequest() {
        $conditions = array();
        $myParams = $this->getParams();

        $masterSearchArr = $this->myDataDefClass->search_defined;
        if (isset($masterSearchArr) && count($masterSearchArr)) {
            foreach($masterSearchArr as $k=>$v){
                if ((isset($myParams[$k]))&&(trim($myParams[$k])!="")) $conditions[$k] = $myParams[$k];
            }
        }
        return $conditions;
    }
	
    /*
     * @author: Nowayorback<nowayforback@gmail.com>
     * @version: 1.0
     * @params:
     * @return: array of conditions from request, format like
     *      $conditions = array();
     *      $conditions[] = array('','doc_id','=', '123');
     */
    public function buildCondotionFromRequest($searchDef) {
        $conditions = (isset($searchDef['search_default']))?$searchDef['search_default']:array();
        $myParams = $this->getParams();
        $masterSearchArr = $searchDef['search_defined'];
        if (isset($masterSearchArr) && count($masterSearchArr)) {
            foreach($masterSearchArr as $k=>$v){
                $myItem = $this->buildItemCondition($k, $v);
                if (is_array($myItem) && (count($myItem))) $conditions[] = $myItem;

            }
        }
        return $conditions;

    }
	
	

    /*
     * @author: Nowayorback<nowayforback@gmail.com>
     * @params: $k is key_name of array item; $v is value of array item
     *
     * @return: a array, item of condition
     */

    private function buildItemCondition($k, $v){
        $myParams = $this->getParams();
        $ItemCondition = array();
        if (((!isset($v['source_table'])) || ($v['source_table']=="")) && ($v['type']=='text') && (!is_array($v['field_name'])) ) {
            if ((isset($myParams[$k]))&&(trim($myParams[$k])!="")) $ItemCondition = array($v['linked'],$v['field_name'],"LIKE","%".$myParams[$k]."%");
        }
        if (((!isset($v['source_table'])) || ($v['source_table']=="")) &&  ($v['type']=='number') && (!is_array($v['field_name'])) ) {
            if ((isset($myParams[$k]))&&(trim($myParams[$k])!="")) $ItemCondition = array($v['linked'],$v['field_name'],'=',$myParams[$k]);
        }
        if (((!isset($v['source_table'])) || ($v['source_table']=="")) &&  ($v['type']=='date') && (!is_array($v['field_name'])) )  {
            if ((isset($myParams[$k."_begin"]))&&(!isset($myParams[$k."_end"]))&&(trim($myParams[$k."_begin"])!="")&&(trim($myParams[$k."_end"])!=""))
                $ItemCondition = array($v['linked'],$v['field_name'],"BETWEEN "," (".$myParams[$k."_begin"]." AND ".$myParams[$k."_end"].") ");
        }
        if ((isset($v['source_table'])) && ($v['source_table']!="") && (!is_array($v['field_name'])) ) {
            if ((isset($myParams[$k]))&&(trim($myParams[$k])!="")) {
                $myStr = "";
                if ($v['type']=='text')
                    $myStr = $v['field_name'] ." IN (SELECT ".$v['field_link']." FROM ".$v['source_table']." WHERE ".$v['field_search']." LIKE '%".$myParams[$k]."%' ) ";

                if ($v['type']=='number')
                    $myStr = $v['field_name'] ."IN (SELECT ".$v['field_link']." FROM ".$v['source_table']." WHERE ".$v['field_search']." = '".$myParams[$k]."' ) ";

                $ItemCondition = array($v['linked'],$myStr);
            }
        }

        if ((is_array($v['field_name'])) && (isset($v['group_in'])) && (isset($myParams[$k])) && (trim($myParams[$k])!="") ) {
            $myStr = "";
            if ($v['type']=='text'){
                $myStr = '(';
                $interval = 0;
                foreach ($v['field_name'] as $field_name_item){
                    $myStr .= " ".$field_name_item." LIKE '%".$myParams[$k]."%' ".$v['group_in'];
                    $interval++;
                }
                if ($interval) $myStr =  substr($myStr, 0, (strlen($myStr) - strlen($v['group_in']) - 1));
                $myStr .= ')';
            }

            if ($v['type']=='number'){
                $myStr = '(';
                $interval = 0;
                foreach ($v['field_name'] as $field_name_item){
                    $myStr .= " ".$field_name_item." = '".$myParams[$k]."' ".$v['group_in'];
                    $interval++;
                }
                if ($interval) $myStr =  substr($myStr, 0, (strlen($myStr) - strlen($v['group_in']) - 1));
                $myStr .= ')';
            }

                $ItemCondition = array($v['linked'],$myStr);

        }

        return $ItemCondition;

    }

	
    /*
     * @author: Nowayorback<nowayforback@gmail.com>
     * @version: 1.0
     * @params:
     * @return: array of order from request, format like
     *      $resultOrderArr = array('doc_id'=>'ASC','doc_bill_number'=>'DESC');
     * @other: only get varial name 'master_order_by' from $_REQUEST, all fields split by ','
     */
    public function buildOrderArrFromRequest() {
        $resultOrderArr = array();
		$prefix = $this->myDataDefClass->table."_";

        if (isset($this->myDataDefClass->order_default) && count($this->myDataDefClass->order_default)) $resultOrderArr = $this->myDataDefClass->order_default;

        $myParams = $this->getParams();

        $directOrderArr = array();
        if (isset($myParams[$prefix.'order_direct'])){
            $myOrderDirectString = $myParams[$prefix.'order_direct'];
            $directOrderArr = explode(",",$myOrderDirectString);
            if ((!is_array($directOrderArr)) ||(!count($directOrderArr))) $directOrderArr[0] = "ASC";
        }

        if (isset($myParams[$prefix.'order_by'])){
                $myOrderString = $myParams[$prefix.'order_by'];
                $myOrderArr = explode(",",$myOrderString);
                foreach ($myOrderArr as $k=> $v) {
                    $myDirect = (isset($directOrderArr[$k])?$directOrderArr[$k]:"ASC");

                    $myIndexs = array_keys($this->myDataDefClass->column_def,$v);
                    if ((is_array($myIndexs))&&(isset($myIndexs[0]))) $resultOrderArr[$myIndexs[0]] = $myDirect;
                }
        }
        return $resultOrderArr;
    }

	
	/*
     * @author: Nowayorback<nowayforback@gmail.com>
     * @version: 1.0
     * @params:
     * @return: array of order from request, format like
     *      $resultOrderArr = array('doc_id','doc_bill_number');
     * @other: only get varial name 'page_number' from $_REQUEST
     */
    public function buildPageNumFromRequest() {
        $resultPage = 1;
        $myParams = $this->getParams();
        if (isset($myParams['page_number'])) $resultPage = $myParams['page_number'];
        return $resultPage;
    }
	
	
    /*
     * @author: Nowayorback<nowayforback@gmail.com>
     * @version: 1.0
     * @params:
     * @return: array of order from request, format like
     *      $resultOrderArr = array('doc_id','doc_bill_number');
     * @other: only get varial name 'page_number' from $_REQUEST
     */
   public function getPage() {
        $results = false;
        $myCondition = $this->buildCondotionFromRequest();
        $myOrderBy = $this->buildOrderArrFromRequest();
        $myPageNumber = $this->buildPageNumFromRequest();
        $results = $this->getItemsInPage($myCondition, $myOrderBy, $myPageNumber);
        return $results;
    }
	
	
    public function getItemsInPage($conditons, $orders, $page = 0, $items_on_page = 30) //_entryExists($primary)
    {
        $results = false;
        $res_begin = 1;
        if (!$page) {
            $results = $this->myDb->simpleRawSelect($this->myDataDefClass->table, "*", $conditons, $orders);
        } else {
            $res_begin = ($page - 1)*$items_on_page ;
            $res_end = $res_begin + $items_on_page -1;
            $results = $this->myDb->simpleRawSelect($this->myDataDefClass->table, "*", $conditons, $orders, null, $items_on_page, $res_begin);
        }

        return $results;

    }


////////////////////////////////

    protected function buildJoinsFromColumnArr($columArr) {
        if (!is_array($columArr)) return false;
        $returnArr = array();
        foreach ($columArr as $key=>$def){
           if (!isset($this->myDataDefClass->column_def[$key])) return false;

            $def = $this->myDataDefClass->column_def[$key];
            if (isset($def['source_table']) && ($def['source_code'])) {
                $returnArr[$def['source_table']] = array('type'=>'LEFT', 'main_key'=>$key, 'join_key'=>$def['source_code']);
            }
        }

        return $returnArr;
    }


    protected function buildFieldNamesFromColumnArr($columArr) {
        if (!is_array($columArr)) return false;
        $returnStr = "";
        foreach ($columArr as $key=>$def){
           if (!isset($this->myDataDefClass->column_def[$key])) return false;

           $returnStr .= $this->myDataDefClass->table. "." . $key.", ";

            $def = $this->myDataDefClass->column_def[$key];
            if (isset($def['source_table']) && ($def['source_code']) &&isset($def['source_label'])) {
                $returnStr .= $def['source_table']. "." . $def['source_label']." as ".$key. "_label, ";
            }
        }
        if ($returnStr!="") $returnStr =  substr($returnStr, 0, (strlen($returnStr) - strlen($returnStr) - 2));

        return $returnStr;
    }



    public function getListPage($listDef, $searchDef = null) {

        if (!isset($listDef['def']) || !is_array($listDef['def'])) {
            log_message('List defined is invalid.');
            return false;
        }

        foreach ($listDef['def'] as $key=>$def){
            if (!isset($this->myDataDefClass->column_def[$key])) {
                log_message('List defined is invalid.');
                return false;
            }
        }

        $items_on_page = (isset($listDef['items_count']) && is_numeric($listDef['items_count']))?$listDef['items_count']:30;

        $fieldNames = $this->buildFieldNamesFromColumnArr($listDef);

        $joins = $this->buildJoinsFromColumnArr($listDef);

        $results = false;
        $myCondition = $this->buildCondotionFromRequest($searchDef);
        $myOrderBy = $this->buildOrderArrFromRequest();
        $myPageNumber = $this->buildPageNumFromRequest();
        $results = $this->getItemsListPage($fieldNames, $myCondition, $myOrderBy, $joins, $myPageNumber, $items_on_page);
        return $results;
    }


    public function getItemsListPage($fields, $conditons, $orders, $joins, $page = 0, $items_on_page = 30) //_entryExists($primary)
    {
        $results = false;
        $res_begin = 1;
        if (!$page) {
            $results = $this->myDb->complexRawSelect($this->myDataDefClass->table, $fields, $joins, $conditons, $orders);
        } else {
            $res_begin = ($page - 1)*$items_on_page ;
            $res_end = $res_begin + $items_on_page -1;
            $results = $this->myDb->complexRawSelect($this->myDataDefClass->table, $fields, $joins, $conditons, $orders, null, $items_on_page, $res_begin);
        }

        return $results;

    }


}

?>
