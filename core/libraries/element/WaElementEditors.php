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
 * Renders a editors element
 *
 * @author 		QuangHuy <chaovietnam@yahoo.com>
 * @package 	Pachay.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class WaElementEditors extends WaElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Editors';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$db		= & JFactory::getDBO();
		$user	= & JFactory::getUser();

		//TODO: change to acl_check method
		if(!($user->get('gid') >= 19) ) {
			return JText::_('No Access');
		}

		// compile list of the editors
		$query = 'SELECT element AS value, name AS text'
		. ' FROM #__plugins'
		. ' WHERE folder = "editors"'
		. ' AND published = 1'
		. ' ORDER BY ordering, name'
		;
		$db->setQuery( $query );
		$editors = $db->loadObjectList();

		array_unshift( $editors, JHTML::_('select.option',  '', '- '. JText::_( 'Select Editor' ) .' -' ) );

		return JHTML::_('select.genericlist',   $editors, ''. $control_name .'['. $name .']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name );
	}
}