<?php

/*-----------------------------------------------------------------

# com_j2store - J2Store

# ------------------------------------------------------------------------

# author    Sasi varna kumar - Weblogicx India http://www.weblogicxindia.com

# copyright Copyright (C) 2014 - 19 Weblogicxindia.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://j2store.org

# Technical Support:  Forum - http://j2store.org/forum/index.html

--------------------------------------------------------------------------*/





// No direct access to this file

defined('_JEXEC') or die;
JHTML::_('behavior.modal');

// import the list field type

jimport('joomla.form.helper');

require_once(JPATH_ADMINISTRATOR .'/components/com_j2store/library/popup.php');



class JFormFieldDiscounts extends JFormField

{

	/**

	 * The field type.

	 *
	 * @var		string

	 */

	protected $type = 'Discounts';



function getInput(){

		//get libraries

		$html ="";

		$id    = JFactory::getApplication()->input->getInt('extension_id', '0');

		F0FModel::addIncludePath(JPATH_SITE.'/plugins/j2store/promotion_globaldiscount/promotion_globaldiscount/models');

		$model = F0FModel::getTmpInstance('Promotions','J2StoreModel');

		$rates = $model->getList();

		$html.='<div class="table-container">';

		$html.='<table class="table-content table table-bordered">';
		$html.='<thead>';

		$html.='<tr>';

		$html .='<th colspan="3">';

        $html.='<span style="float: right;">';

        $html.= J2StorePopup::popup( "index.php?option=com_j2store&view=promotion&task=view&id={$id}&shippingTask=setrates&tmpl=component",JText::_('J2STORE_SET_RATES'),array('class'=>'btn btn-success') );

        $html.=' </th></span></tr>';

		$html.='<th>';

		$html.=JText::_('J2STORE_GEOZONE');

		$html .='</th><th>';

		$html.=JText::_('J2STORE_SHIPPING_COST');

		$html .='</th><th>';

		$html.=JText::_('J2STORE_HANDLING_FEE');

		$html .='</th></thead>';

		$html .='<tbody>';



		foreach($rates as $rate)

		{

			$geozone = F0FTable::getAnInstance('Geozone','J2StoreTable');

			$geozone->load($rate->geozone_id);

			$html.='<tr>';

			$html.='<td>';

			$html.= $geozone->geozone_name;

			$html .='</td>';

			$html.='<td>'.$rate->shipping_rate_price.'</td>';

			$html.='<td>'.$rate->shipping_rate_handling.'</td>';

			$html.='</tr>';

		}

		$html .='</tbody>';

		$html.='</table></div>';

		return	$html;

	}

}

