<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 */

defined('_JEXEC') or die;
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

if (!defined('F0F_INCLUDED'))
{
	include_once JPATH_LIBRARIES . '/f0f/include.php';
}
require_once(JPATH_ADMINISTRATOR.'/components/com_j2store/library/plugins/promotion.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_j2store/helpers/toolbar.php');

class plgJ2StorePromotion_globaldiscount extends J2StorePromotionPlugin
{
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 *                         forcing it to be unique
	 */
    var $_element   = 'promotion_globaldiscount';

    /**
     * Overriding
     *
     * @param $options
     * @return unknown_type
     */
    function onJ2StoreGetPromotionView( $row )
    {
    	if (!$this->_isMe($row))
    	{
    		return null;
    	}

    	$html = $this->viewList();

    	return $html;
    }

    /**
     * Validates the data submitted based on the suffix provided
     * A controller for this plugin, you could say
     *
     * @param $task
     * @return html
     */
    function viewList()
    {

    	$app = JFactory::getApplication();
    	$html = "";
    	JToolBarHelper::title(JText::_('J2STORE_SHIPM_SHIPPING_METHODS').'-'.JText::_('plg_j2store_'.$this->_element),'j2store-logo');

  		/*JToolbarHelper::custom('newMethod','new','new','JTOOLBAR_NEW', false, false, 'promotionTask');
    	JToolbarHelper::custom('delete', 'delete', 'delete', 'JTOOLBAR_DELETE', false, false, 'promotionTask');
    	*/
    	JToolBarHelper::cancel( 'cancel', 'JTOOLBAR_CLOSE' );

    	$vars = new JObject();

    	$vars->state = $this->_getState();


    	$this->includeCustomModel('PromotionMethods');
    	//$this->includeCustomTables();
    	$this->includeCustomTables('PromotionMethod');


    	$model  = F0FModel::getTmpInstance('PromotionMethods', 'J2StoreModel');

    	$list = $model->getList();


    	$vars->list = $list;

    	$id = $app->input->getInt('id', '0');
    	$form = array();
    	$form['action'] = "index.php?option=com_j2store&view=promotion&task=view&id={$id}";
    	$vars->form = $form;
    	$vars->sid = $id;

    	$html = $this->_getLayout('default', $vars);

    	return $html;
    }


    function onJ2StoreCalculateOrderDiscountTotals(&$order){
    	$order->order_discount = $this->getPromotionTotals($order,$this->_element);
    }


    public function getPromotionTotals($order,$element) {
    	$cart_helper = J2store::cart();
    	$params = J2Store::config();
    	$items = $order->getItems();
    	$session = JFactory::getSession();
    	$promotiontotals = 0;

    	$promotion = new JObject();
    	$promotion->products = $this->params->get('products');
    	$promotion->discount_value = $this->params->get('discount_value');
    	$promotion->discount_type = $this->params->get('discount_type');
    	$promotion->subtotal_minimum = $this->params->get('subtotal_minimum');
    	$promotion->subtotal_maximum= $this->params->get('subtotal_maximum');
    	$promotion->user_groups= $this->params->get('users');

   		$var = 'orderitem_finalprice_without_tax';
    		if(!$params->get('config_discount_before_tax', 1)) {
    			//discount applied after tax
    			$var = 'orderitem_finalprice_with_tax';
    		}


    		$status = true;

    		$user = JFactory::getUser();
    		//get the currenct user Groups
    		$current_user_groups = $user->getAuthorisedGroups();

    		$allowed_promotion_user_groups = $promotion->user_groups;

    		$current_user_group_id =implode(',' ,$user->groups);

			if($allowed_promotion_user_groups && !empty( $allowed_promotion_user_groups)){
				if(!in_array($current_user_group_id , $allowed_promotion_user_groups )){
					$status =  false;
				}
			}

    		if (!$promotion->products) {
	    			$sub_total = 0;
	    			foreach ($items as $item) {
	    				$sub_total += $item->$var;
	    			}

	    		} else {
	    			$sub_total = 0;
	    			foreach ($items as $item) {
	    				if (in_array($item->product_id, $promotion->products)) {
	    					$sub_total += $item->$var;
	    				}
	    			}
	    		}


	    		if( $status && $promotion->subtotal_minimum){
	    			if($promotion->subtotal_minimum > $sub_total){
	    				$status =  false;
	    			}
	    		}

	     		if( $status && $promotion->subtotal_maximum ){
	    		 	if($sub_total > $promotion->subtotal_maximum){
	    				$status =  false;
	    			}
	    		}
    		if ($status && $promotion->discount_type == 'F') {
    			$promotion_value = min($promotion->discount_value, $sub_total);
    		}


    		//maximum value restriction. If set then we need to check
    		if ($status && $promotion->discount_type == 'P' && !empty($promotion->subtotal_maxmimum) && (float) $promotion->subtotal_maxmimum > 0) {
    			//calculate the actual discount
    			$actual_discount = $this->getTotalPromotionDiscount($promotion,$items);
    			//is the actual discount greater than the max value
    			if($actual_discount > 0 && $actual_discount > (float) $promotion->subtotal_maxmimum) {
    				$promotion_value = (float) $promotion->subtotal_maxmimum;
    				$promotion->discount_type = 'F';
    			}
    		}
    		if($status ){
	    		$product_array2 = array();
	    		foreach ($items as &$item) {
	    			$discount = 0;

	    			if (!$promotion->products) {
	    				$status = true;
	    			} else {
	    				if (in_array($item->product_id, $promotion->products)) {
	    					$status = true;
	    				} else {
	    					$status = false;
	    				}
	    			}

	    			if ($status) {

	    				if ($promotion->discount_type == 'F') {
	    					$discount = $promotion->discount_value * ($item->$var / $sub_total);
	    				} elseif ($promotion->discount_type == 'P') {
	    					$discount = $item->$var / 100 * $promotion->discount_value;
	    				}

	    				if ($item->orderitem_taxprofile_id ) {
	    					$taxModel = F0FModel::getTmpInstance('TaxProfiles', 'J2StoreModel');
	    					$tax_rates = $taxModel->getTaxwithRates($item->$var - ($item->$var - $discount), $item->orderitem_taxprofile_id, 0);
	    					$item_discount_taxtotal = 0;
	    					foreach ($tax_rates->taxes as $taxrate_id=>$tax_rate) {
	    						$order->_taxrates[$taxrate_id]['total'] -= $tax_rate['amount'];
	    						$item_discount_taxtotal -= $tax_rate['amount'];
	    					}
	    				}
	    			}

	    			$item->orderitem_discount = $discount;
	    			$item->orderitem_discount_tax = $item_discount_taxtotal;
	    			$promotiontotals += $discount;
	    		}
    		}

    		if($session->has('shipping_values', 'j2store')) {
    			$shipping = $session->get('shipping_values', array(), 'j2store');
    			$shipping_cost = $shipping['shipping_price'] + $shipping['shipping_extra'] + $shipping['shipping_tax'];
    			$promotiontotals += $shipping_cost;
    		}
    		$promotionValue = new JObject();
    		$promotionValue->value = $promotion->discount_value;
    		$promotionValue->type = $promotion->discount_type;
    		$promotionValue->amount = $promotiontotals;
    		$this->_orderpromotions[] = $promotionValue;

    	return $promotiontotals;
    }


    public function getTotalPromotionDiscount($promotion, $items) {

    	$app = JFactory::getApplication();
    	$params = J2Store::config();
    	$session = JFactory::getSession();
    	$cart_helper = J2Store::cart();
    	$discount_total = 0;
    	$sub_total = 0;
		$var = 'orderitem_finalprice_without_tax';
    	if(!$params->get('config_discount_before_tax', 1)) {
    			//discount applied after tax
    			$var = 'orderitem_finalprice_without_tax';
    		}

    		$user = JFactory::getUser();
    		//get the currenct user Groups
    		$current_user_groups = $user->getAuthorisedGroups();

    		$allowed_promotion_user_groups = $promotion->user_groups;

    		$current_user_group_id =implode(',' ,$user->groups);
    		if($allowed_promotion_user_groups && !empty( $allowed_promotion_user_groups)){
    			if(!in_array($current_user_group_id , $allowed_promotion_user_groups )){
    				$status = false;
    			}
    		}

    		if (!$promotion->products) {
    			$sub_total = 0;

    			foreach ($items as $item) {
    				$sub_total += $item->$var;
    			}

    		} else {
    			$sub_total = 0;
    			foreach ($items as $item) {
    				if (in_array($item->product_id, $promotion->products)) {
    					$sub_total += $item->$var;
    				}
    			}
    		}

    		if( $status && $promotion->subtotal_minimum){
    			if($sub_total < $promotion->subtotal_minimum){
    				$status =  false;
    			}
    		}

    		if( $status && $promotion->subtotal_maximum){
    			if($sub_total > $promotion->subtotal_maximum){
    				$status =  false;
    			}
    		}


    		if ($discount_type == 'F') {
    			$promotion_value = min($promotion->discount_value, $sub_total);
    		}

    		$product_array2 = array();

			if($status ){
	    		foreach ($items as $item){
	    			$discount = 0;
	    			if (!$products) {
	    				$status = true;
	    			} else {
	    				if (in_array($item->product_id, $promotion->products)) {
	    					$status = true;
	    				} else {
	    					$status = false;
	    				}
	    			}

	    			if ($status) {
	    				if ($discount_type == 'F') {
	    					$discount = $promotion->discount_value * ($item->$var / $sub_total);
	    				} elseif ($discount_type == 'P') {
	    					$discount = $item->$var / 100 * $promotion->discount_value;
	    				}
	    			}
	    			$discount_total += $discount;
	    		}
			}

    		if($session->has('shipping_values', 'j2store')) {
    			$shipping = $session->get('shipping_values', array(), 'j2store');
    			$shipping_cost = $shipping['shipping_price']+$shipping['shipping_extra']+$shipping['shipping_tax'];
    			$discount_total += $shipping_cost;
    		}
    	return $discount_total;
    }


    protected function getPromotion($order){
    	$status = true;
    	$items = $order->getItems();

    	$app = JFactory::getApplication();
		$params = J2Store::config();
		$session = JFactory::getSession();
		$cart_helper = J2Store::cart();
		$discount_total = 0;
    	//now validate
   		//get the user groups
			$user = JFactory::getUser();

			//get the currenct user Groups
			$current_user_groups = $user->getAuthorisedGroups();

			//and also get the promotion users groups
			//incase user groups are applied

			$allowed_promotion_user_groups = $this->params->get('users');

			//$this->_orderpromotions[] =

			$min_subtotal = $this->params->get('subtotal_minimum');
			$max_subtotal = $this->params->get('subtotal_maximum');

			if($min_subtotal){
				if($order->order_subtotal > $min_subtotal){
					$status =  true;
				}
			}

			if($max_subtotal){
				if($order->order_subtotal < $max_subtotal){
					$status =  true;
				}
			}


	   		/* if($session->has('promotion', 'j2store')) { */

			$var = 'orderitem_finalprice_without_tax';

			if(!$params->get('config_discount_before_tax', 1)) {
				//discount applied after tax
				$var = 'orderitem_finalprice_without_tax';
			}

			if (!$this->params->get('products')) {
				$sub_total = 0;

				foreach ($items as $item) {
					$sub_total += $item->$var;
				}

			} else {
				$sub_total = 0;
				foreach ($items as $item) {
					if (in_array($item->product_id,$this->prams->get('products'))) {
						$sub_total += $item->$var;
					}
				}
			}

			$promotion_value =  $this->params->get('discount_value');

			if ($this->params->get('discount_type') == 'F') {
				$promotion_value = min($promotion_value, $sub_total);
			}

			$product_array2 = array();
			foreach ($items as $item) {
				$discount = 0;

				if (!$this->params->get('products')) {
					$status = true;
				} else {
					if (in_array($item->product_id,$this->params->get('products') )) {
						$status = true;
					} else {
						$status = false;
					}
				}

				if ($status) {
					if ($this->params->get('discount_type')  == 'F') {
						$discount = $promotion_value * ($item->$var / $sub_total);
					} elseif ($this->params->get('discount_type')  == 'P') {
						$discount = $item->$var / 100 * $promotion_value ;
					}

				}

				$discount_total += $discount;
		}
		return $discount_total;
    }



}

