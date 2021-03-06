<?php
/*------------------------------------------------------------------------
# mod_j2store_cart - J2 Store Cart
# ------------------------------------------------------------------------
# author    Sasi varna kumar - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2014 - 19 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://j2store.org
# Technical Support:  Forum - http://j2store.org/forum/index.html
-------------------------------------------------------------------------*/



// no direct access
defined('_JEXEC') or die('Restricted access');
require_once (JPATH_ADMINISTRATOR.'/components/com_j2store/helpers/j2store.php');
class modJ2StoreCartHelper {

	protected static $_data = null;
	protected static $_items = null;

	public static function getItems() {

		if (empty(self::$_data ))
		{
			$list = array();
			$total = 0;

			$j2params = J2Store::config();
			$order = F0FModel::getTmpInstance('Orders', 'J2StoreModel')->initOrder()->getOrder();

		//	$module = JModuleHelper::getModule('mod_j2store_cart');
			// Get params and output
		//	$params = new JRegistry($module->params);

			$params = ModJ2StoreCartHelper::getModuleParams();
			if($params->get('quantity_count',1) == 1){
				$items = $order->getItems();
				if(count($items)>0){
					foreach($items as $item){
						$total += $item->orderitem_quantity;
					}
				}
			} else {
					$total = $order->getItemCount();
			}

			if($total) {
				$list['total'] = $order->order_total;
				$list['product_count'] = $total;
				//$html = JText::sprintf('J2STORE_CART_TOTAL', $product_count, J2StorePrices::number($total));
			} else {
				$list['total'] = 0;
				$list['product_count'] = 0;
				//$html = JText::_('J2STORE_NO_ITEMS_IN_CART');
			}
			self::$_data = $list;
		}

		return self::$_data;
	}

	public static function getAdavcedItems(){

		if (empty(self::$_items))
		{
			$j2params = J2Store::config();
			$order = F0FModel::getTmpInstance('Orders', 'J2StoreModel')->initOrder()->getOrder();
			$module = JModuleHelper::getModule('mod_j2store_cart');
			// Get params and output
			$params = new JRegistry($module->params);
			$items = $order->getItems();
			self::$_items = $items;
		}
		return self::$_items;

	}

	public static function getModuleParams(){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)->select('params')->from('#__modules')->where('module='.$db->q('mod_j2store_cart'))->where('published = 1');
		//$query = 'SELECT params FROM #__modules WHERE module="mod_j2store_cart" and published=1';
		$db->setQuery($query);
		$result = $db->loadResult();
		//$module = JModuleHelper::getModule('mod_j2store_cart');
		// Get params and output
		return $params = new JRegistry($result);//$module->params
	}
}
