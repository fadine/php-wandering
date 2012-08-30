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

require_once ( WAPATH_SYSTEM.'libraries'.DS.'WaElement.php' );

/**
 * Renders a text element
 *
 * @author 		QuangHuy <chaovietnam@yahoo.com>
 * @package 	Pachay.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class WaElementCheck extends WaElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Check';

	function fetchElement($arrElement, $value)
	{
		global $mod_strings;
		
		$label = $mod_strings[(isset($arrElement['label'])?$arrElement['label']:"_NO_LABEL")]; //$xmlElement->selectNodes('label',1)->getText();
		$descr = (isset($arrElement['description'])?$arrElement['description']:""); //$xmlElement->selectNodes('description',1)->getText();
		$id = $arrElement['map_name']; //$xmlElement->selectNodes('description',1)->getText();
		$name = $id; //$xmlElement->selectNodes('name',1)->getText();
		$size = ( isset($arrElement['size']) ? 'size="'.$arrElement['size'].'"' : '' );
		$class = ( isset($arrElement['class']) ? $arrElement['class'] : 'common_input common_checkbox' );
		$label_class = ( isset($arrElement['class']) ? 'class="lbl_'.$arrElement['class'].'"' : 'class="lbl_common_input lbl_common_checkbox"' );
		$wrap_class = ( isset($arrElement['class']) ? 'class="wrap_'.$arrElement['class'].'"' : 'class="wrap_common_input wrap_common_checkbox"' );

                $mySource = $this->getDataSource($arrElement);
                $myOptions = "";
                if ((is_array($mySource))&&($arrElement['type']=="check_enum")){
                    foreach ($mySource as $k=>$v){
                        $myOptions .= "<label for= '".$id."' class = 'lbl_".$class."' ><input class = '".$class."' type = 'checkbox' id = '".$id."' name = '".$name."' value = '".$k."' ".(($k==$value)?"checked":"")." />".$v."</label>";
                    }
                }elseif ((is_array($mySource))&&($arrElement['type']=="check_select")){
                    foreach ($mySource as $k=>$v){
                        $myOptions .= "<label for= '".$id."' class = 'lbl_".$class."' ><input class = '".$class."' type = 'checkbox' id = '".$id."' name = '".$name."' value = '".$v['source_code']."' ".(($v['source_code']==$value)?"checked":"")." />".$v['source_label']."</label>";
                    }
                }
		$returnStr = '<div id = "lbl_'.$id.'" '.$label_class.'>'.$label.': </div><div id = "wrap_'.$id.'" '.$wrap_class.' >'.$myOptions.'</div>  <div class="clear"></div> ';
                
                return $returnStr;
	}

	

	function fetchViewElement($arrElement, $value)
	{
		global $mod_strings;
		
		$label = $mod_strings[(isset($arrElement['label'])?$arrElement['label']:"_NO_LABEL")]; //$xmlElement->selectNodes('label',1)->getText();
		$descr = (isset($arrElement['description'])?$arrElement['description']:""); //$xmlElement->selectNodes('description',1)->getText();
		$id = $arrElement['map_name']; //$xmlElement->selectNodes('description',1)->getText();
		$name = $id; //$xmlElement->selectNodes('name',1)->getText();
		$size = ( isset($arrElement['size']) ? 'size="'.$arrElement['size'].'"' : '' );
		$class = ( isset($arrElement['class']) ? 'class="'.$arrElement['class'].' mode_view"' : 'class="common_input common_checkbox mode_view"' );
		$label_class = ( isset($arrElement['class']) ? 'class="lbl_'.$arrElement['class'].' mode_view"' : 'class="lbl_common_input lbl_common_checkbox mode_view"' );
		$wrap_class = ( isset($arrElement['class']) ? 'class="wrap_'.$arrElement['class'].'"' : 'class="wrap_common_input wrap_common_checkbox"' );

                $mySource = $this->getDataSource($arrElement);
                $myLabel = $value;
                if ((is_array($mySource))&&($arrElement['type']=="check_enum")){
                    $myLabel = $mySource[$value];
                }elseif ((is_array($mySource))&&($arrElement['type']=="check_select")){
                    foreach ($mySource as $k=>$v){
                         if ($v['source_code']==$value) $myLabel = $v['source_label'];
                    }
                }

		return '<div id = "lbl_'.$id.'" '.$label_class.'>'.$label.': </div><div id = "wrap_'.$id.'" '.$wrap_class.' ><span id="'.$id.'" '.$class.' >'.$myLabel.'</span> </div>  <div class="clear"></div> ';
	}


        private function getDataSource($arrElement) {
            $returnArr = array();
            $myDb = DBManagerFactory::getInstance();

            if ((isset($arrElement['type']))&&($arrElement['type']=="check_select")) {
                $myConditions = "";
                if ((isset($arrElement['source_conditions']))&&(is_array($arrElement['source_conditions']))) {
                    $myConditions = $myDb->paserCondition($arrElement['source_conditions']);
                }
                $sql = "SELECT ".$arrElement['source_code'] . " AS source_code, ".$arrElement['source_label']." AS source_label FROM ".$arrElement['source_table'] . " ".$myConditions;
                $results = $myDb->query($query);
    		while($row = $myDb->fetchByAssoc($results))
    		{
    			$returnArr[] = $row;
    		}
            } elseif ((isset($arrElement['type']))&&($arrElement['type']=="check_enum")) {
                $returnArr = $arrElement['source'];
            }

            
            return $returnArr;
        }
}
