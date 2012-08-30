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

class WaElementDetailTable extends WaElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'DetailTable';

        /**
         *
         * @param <type> $arrElement
         * @param <type> $value ia array of array of value
         * @return <type>
         */
	function fetchElement($arrElement, $value)
	{
		global $mod_strings;
                $returnStr = "";
                $myColArr = array();
                $mapArr = array();
                $mapValueArr = array();
                $overlayFormat = array();

                $class = ( isset($arrElement['class']) ? "class='".$arrElement['class']."' " : "class='common_detail_table'" );
                $returnStr .= "<table id = '".$arrElement['name']."' ".$class." >";
                $myColInfo = &$arrElement['list_fields'];
                if (is_array($myColInfo)){
                    $header = "<tr class = 'head'>";

                    $myCount = 1;

                    foreach ($myColInfo as $fieldName => $fieldinfo){
                        $label = $mod_strings[(isset($fieldinfo['label'])?$fieldinfo['label']:"_NO_LABEL")];
                        $id = $fieldinfo['map_name'];
                        $mapArr[$fieldName] = $id;
                        $class = ( isset($fieldinfo['class']) ? "class='".$fieldinfo['class']."'" : "class='common_detail_col'" );
                        if ((isset($fieldinfo['in_table']))&&($fieldinfo['in_table']==true)) {
                            $returnStr .= "<col ".$class." />";
                            $header .= "<td>".$label."</td>";
                            $myColArr[$fieldName] = $id;
                        }

                        $formatRow = array();
                        $formatRow['id'] = $myCount;
                        $formatRow['fname'] = $fieldinfo['map_name'];
                        $formatRow['flabel'] = $label;
                        $formatRow['ftype'] = $fieldinfo['type'];
                        $formatRow['fsource'] = ($fieldinfo['type']=='enum'?("enum_".$fieldName):($fieldinfo['type']=='select'?("select_".$fieldName):""));
                        $formatRow['fdisplay'] = ($fieldinfo['in_table']==true?true:false);
                        $formatRow['fl_class'] = "label_o";
                        $formatRow['fi_class'] = "input_o";


                        $overlayFormat[] = $formatRow;
                        $myCount++;

                    }
                    $returnStr .= "<col class = 'w20px'/>";
                    $header .= "</tr>";
                    $returnStr .= $header;
                }

                if ((is_array($value))&&(count($value))){
                    $myIndex = 0;
                    foreach ($value as $row) {
                        $rowArr = array();
                        $returnStr .= "<tr class = 'detail_row'>";
                        foreach ($mapArr as $fieldName => $mapName){
                            $rowArr[$mapName] = ((isset($row[$fieldName]))?$row[$fieldName]:"");
                            if(isset($myColArr[$fieldName])) {
                                $returnStr .= "<td>";
                                $returnStr .= ((isset($row[$fieldName]))?$row[$fieldName]:"");
                                $returnStr .= "</td>";
                            }

                        }
                        $returnStr .= "<td><img src = '' onclick = 'myMaintainOverlay(\"".$arrElement['name']."_format\",\"".$arrElement['name']."\",".$myIndex.");' /></td>";


                        $returnStr .= "</tr>";
                        $mapValueArr[] = $rowArr;
                        $myIndex ++;
                    }
                }


                $returnStr .= "</table>";

                $returnStr .= "<script> var ".$arrElement['name']."_format = ".json_encode($mapValueArr).";\n
                    var myDataValues[".$arrElement['name']."] = ".  json_encode($mapValueArr)."; </script>";

                return $returnStr;
	}





	function fetchViewElement($arrElement, $value)
	{
		global $mod_strings;
                $returnStr = "";
                $myColArr = array();
                $mapArr = array();
                $mapValueArr = array();

                $class = ( isset($arrElement['class']) ? "class='".$arrElement['class']."' " : "class='common_detail_table'" );
                $returnStr .= "<table id = '".$arrElement['name']."' ".$class." >";
                $myColInfo = &$arrElement['list_fields'];
                if (is_array($myColInfo)){
                    $header = "<tr class = 'head'>";
                    $myCount = 1;
                    foreach ($myColInfo as $fieldName => $fieldinfo){
                        $label = $mod_strings[(isset($fieldinfo['label'])?$fieldinfo['label']:"_NO_LABEL")];
                        $id = $fieldinfo['map_name'];
                        $mapArr[$fieldName] = $id;
                        $class = ( isset($fieldinfo['class']) ? "class='".$fieldinfo['class']."'" : "class='common_detail_col'" );
                        if ((isset($fieldinfo['in_table']))&&($fieldinfo['in_table']==true)) {
                            $returnStr .= "<col ".$class." />";
                            $header .= "<td>".$label."</td>";
                            $myColArr[$fieldName] = $id;
                        }

                        $formatRow = array();
                        $formatRow['id'] = $myCount;
                        $formatRow['fname'] = $fieldinfo['map_name'];
                        $formatRow['flabel'] = $label;
                        $formatRow['ftype'] = $fieldinfo['type'];
                        $formatRow['fsource'] = ($fieldinfo['type']=='enum'?("enum_".$fieldName):($fieldinfo['type']=='select'?("select_".$fieldName):""));
                        $formatRow['fdisplay'] = ($fieldinfo['in_table']==true?true:false);
                        $formatRow['fl_class'] = "label_o";
                        $formatRow['fi_class'] = "input_o";


                        $overlayFormat[] = $formatRow;
                        $myCount++;

                    }
                    $header .= "</tr>";
                    $returnStr .= $header;
                }

                if ((is_array($value))&&(count($value))){
                    foreach ($value as $row) {
                        $rowArr = array();
                        $returnStr .= "<tr class = 'detail_row'>";
                        foreach ($mapArr as $fieldName => $mapName){
                            $rowArr[$mapName] = ((isset($row[$fieldName]))?$row[$fieldName]:"");
                            if(isset($myColArr[$fieldName])) {
                                $returnStr .= "<td>";
                                $returnStr .= ((isset($row[$fieldName]))?$row[$fieldName]:"");
                                $returnStr .= "</td>";
                            }

                        }

                        $returnStr .= "</tr>";
                        $mapValueArr[] = $rowArr;
                    }
                }


                $returnStr .= "</table>";

                $returnStr .= "<script> var ".$arrElement['name']."_format = ".json_encode($mapValueArr).";\n
                    var myDataValues[".$arrElement['name']."] = ".  json_encode($mapValueArr)."; </script>";

                return $returnStr;
	}
}
