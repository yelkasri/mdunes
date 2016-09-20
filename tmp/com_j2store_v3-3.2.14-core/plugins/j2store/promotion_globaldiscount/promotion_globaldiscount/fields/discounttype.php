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
class JFormFieldDiscounttype extends JFormFieldList
{
	protected $type = 'discounttype';
	function getInput(){
		return J2Html::select()->clearState()
					->type('genericlist')
					->name($this->name)
					->value($this->value)
					->setPlaceHolders(array('0'=>JText::_('J2STORE_SHIPM_FLAT_RATE_PER_ORDER'),'3' =>JText::_('J2STORE_SHIPM_FLAT_RATE_PER_ITEM')))
					->getHtml();
			}
}

