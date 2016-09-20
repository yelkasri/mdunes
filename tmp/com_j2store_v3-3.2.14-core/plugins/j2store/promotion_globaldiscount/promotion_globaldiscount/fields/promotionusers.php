<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 */
// No direct access to this file
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

require_once(JPATH_ADMINISTRATOR .'/components/com_j2store/helpers/j2html.php');

class JFormFieldPromotionusers extends JFormFieldList
{

	protected $type = 'promotionusers';

	function getInput(){

			$groups =  JHtmlUser::groups();
           	$group_option[]  = JText::_('J2STORE_SELECT_OPTION');
   	    	foreach($groups as $group){
   	    		$group_option[$group->value] = JText::_($group->text);
			}
	return JHTML::_ ( 'select.genericlist', $group_option, $this->name,array('multiple'=>true), 'value', 'text',$this->value,'usergroups');
	}
}