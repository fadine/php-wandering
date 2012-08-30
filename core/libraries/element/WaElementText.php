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

class WaElementText extends WaElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Text';

	function fetchElement($arrElement, $value)
	{
		global $mod_strings;
		
		$label = $mod_strings[(isset($arrElement['label'])?$arrElement['label']:"_NO_LABEL")]; //$xmlElement->selectNodes('label',1)->getText();
		$descr = (isset($arrElement['description'])?$arrElement['description']:""); //$xmlElement->selectNodes('description',1)->getText();
		$id = $arrElement['map_name']; //$xmlElement->selectNodes('description',1)->getText();
		$name = $id; //$xmlElement->selectNodes('name',1)->getText();
		$size = ( isset($arrElement['size']) ? 'size="'.$arrElement['size'].'"' : '' );
		$class = ( isset($arrElement['class']) ? 'class="'.$arrElement['class'].'"' : 'class="common_input common_textbox"' );
		$label_class = ( isset($arrElement['class']) ? 'class="lbl_'.$arrElement['class'].'"' : 'class="lbl_common_input lbl_common_textbox"' );
		$wrap_class = ( isset($arrElement['class']) ? 'class="wrap_'.$arrElement['class'].'"' : 'class="wrap_common_input wrap_common_textbox"' );
        /*
         * Required to avoid a cycle of encoding &
         * html_entity_decode was used in place of htmlspecialchars_decode because
         * htmlspecialchars_decode is not compatible with PHP 4
         */

		return '<div id = "lbl_'.$id.'" '.$label_class.'>'.$label.': </div><div id = "wrap_'.$id.'" '.$wrap_class.' ><input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" '.$class.' '.$size.' /></div>  <div class="clear"></div> ';
	}

	

	function fetchViewElement($arrElement, $value)
	{
		global $mod_strings;
		
		$label = $mod_strings[(isset($arrElement['label'])?$arrElement['label']:"_NO_LABEL")]; //$xmlElement->selectNodes('label',1)->getText();
		$descr = (isset($arrElement['description'])?$arrElement['description']:""); //$xmlElement->selectNodes('description',1)->getText();
		$id = $arrElement['map_name']; //$xmlElement->selectNodes('description',1)->getText();
		$name = $id; //$xmlElement->selectNodes('name',1)->getText();
		$size = ( isset($arrElement['size']) ? 'size="'.$arrElement['size'].'"' : '' );
		$class = ( isset($arrElement['class']) ? 'class="'.$arrElement['class'].' mode_view"' : 'class="common_input common_textbox mode_view"' );
		$label_class = ( isset($arrElement['class']) ? 'class="lbl_'.$arrElement['class'].' mode_view"' : 'class="lbl_common_input lbl_common_textbox mode_view"' );
		$wrap_class = ( isset($arrElement['class']) ? 'class="wrap_'.$arrElement['class'].'"' : 'class="wrap_common_input wrap_common_textbox"' );
        /*
         * Required to avoid a cycle of encoding &
         * html_entity_decode was used in place of htmlspecialchars_decode because
         * htmlspecialchars_decode is not compatible with PHP 4
         */

		return '<div id = "lbl_'.$id.'" '.$label_class.'>'.$label.': </div><div id = "wrap_'.$id.'" '.$wrap_class.' ><span id="'.$id.'" '.$class.' >'.$value.'</span> </div>  <div class="clear"></div> ';
	}
}
