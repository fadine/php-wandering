<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
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
 * Renders a calendar element
 *
 * @author 		Louis Landry
 * @package 	Pachay.Framework
 * @subpackage	Parameter
 * @since		1.5
 */
class WaElementCalendar extends WaElement
{
	/**
	* Element name
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Calendar';

	function fetchElement($name, $value, &$node, $control_name)
	{
		JHTML::_('behavior.calendar'); //load the calendar behavior

		$format	= ( $node->attributes('format') ? $node->attributes('format') : '%Y-%m-%d' );
		$class	= $node->attributes('class') ? $node->attributes('class') : 'inputbox';

		$id   = $control_name.$name;
		$name = $control_name.'['.$name.']';

		return JHTML::_('calendar', $value, $name, $id, $format, array('class' => $class));
	}
}
