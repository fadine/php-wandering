<?php
// Check to ensure this file is within the rest of the framework
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


class WaElement
{
	/**
	* element name
	*
	* This has to be set in the final
	* renderer classes.
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = null;

	/**
	* reference to the object that instantiated the element
	*
	* @access	protected
	* @var		object
	*/
	var	$_parent = null;

	/**
	 * Constructor
	 *
	 * @access protected
	 */
	function __construct($parent = null) {
		$this->_parent = $parent;
	}

	/**
	* get the element name
	*
	* @access	public
	* @return	string	type of the parameter
	*/
	function getName() {
		return $this->_name;
	}

	function render($arrElement, $value, $mode = "edit")	//mode = edit or view, set the page is view or edit
	{
		if ($mode == "edit"){
			$this->fetchElement($arrElement, $value);
		}elseif ($mode == "view"){
			$this->fetchViewElement($arrElement, $value);
		}

		return $result;
	}



	function fetchElement($arrElement, $value) {
		return;
	}

	function fetchViewElement($arrElement, $value) {
		return;
	}
}
