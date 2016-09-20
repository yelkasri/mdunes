<?php
/************************************************************
 * @version			Ticketmaster 3.0.1
 * @package			com_ticketmaster
 * @copyright		Copyright © 2009 - All rights reserved.
 * @license			GNU/GPL
 * @author			Robert Dam
 * @author mail		info@rd-media.org
 * @website			http://www.rd-media.org
 *************************************************************/
 
 	function _getAmount($eid, $paymentprocessor=0, $backend=0) {
			
			## Connect database now.
			$db = JFactory::getDBO();			
			
			if($backend == 0){ 
				
				$total = getItems($eid);
	
				if ($total == 0){
					$transcost = 0;
					return $transcost;
				}
				
			}

			## getting the configuration of ticketmaster.
			$sql = 'SELECT * FROM #__ticketmaster_config WHERE configid = 1';
			$db->setQuery($sql);
			$config = $db->loadObject();		

			if($backend == 0){
				
				$sql='SELECT a.userid, SUM(t.ticketprice) AS orderprice, a.coupon
					  FROM #__ticketmaster_tickets AS t, #__ticketmaster_orders AS a
					  WHERE a.ordercode = '.$eid.'
					  AND a.ticketid = t.ticketid
					  AND (a.paid = 0 OR a.paid = 3)
					  GROUP BY a.ordercode';   
	
				$db->setQuery($sql);
				$result = $db->loadObject();
				
			}else{
				
				$sql='SELECT a.userid, SUM(t.ticketprice) AS orderprice, a.coupon
					  FROM #__ticketmaster_tickets AS t, #__ticketmaster_orders AS a
					  WHERE a.ordercode = '.$eid.'
					  AND a.ticketid = t.ticketid
					  AND a.paid = 1
					  GROUP BY a.ordercode';
				
				$db->setQuery($sql);
				$result = $db->loadObject();
				
			}

			
			##########################################################
			##### THIS WILL NLY BE CALLED WHEN A PAYMENT RETURNS #####
			##### IT IS BEING USED TO CHECK IF THERE IS A COUPON #####
			
			if ($paymentprocessor == 1) {
				
				$sql='SELECT a.userid, a.coupon
					  FROM #__ticketmaster_orders AS a
					  WHERE a.ordercode = '.(int)$eid.'';				
			
				$db->setQuery($sql);
				$list = $db->loadObjectList();			
			
				for ($i = 0, $n = count($list); $i < $n; $i++ ){
					
					$row = $list[$i];

					if($row->coupon != ''){
						
						## We need to fill this temporary :)
						$session = JFactory::getSession();
						## Gettig the orderid if there is one.
						$couponcode = $session->set('coupon', $row->coupon); 			
					}
				
				}
			
			}
			
			##### END: THIS WILL NLY BE CALLED WHEN A PAYMENT RETURNS #####
			##### PLEASE DO NOT CHANGE THIS CODE AS IT MAY DAMAGE!!   #####
			###############################################################
			
			if ($result->orderprice == 0) {
				
				$amount = 0;
			
			}else{	
				
				## Get the order price!
				$orderprice = $result->orderprice;
			
				$session = JFactory::getSession();
				## Gettig the orderid if there is one.
				$couponcode = $session->get('coupon');            
				
				if ($couponcode != ''){
					
					$sql = 'SELECT * FROM #__ticketmaster_coupons 
							WHERE coupon_code = "'.$couponcode.'"';		
										  
					$db->setQuery($sql);
					$coupon = $db->loadObject();			
					
					if ($coupon->coupon_type == 1){
						
						## Discount in %
						$discount = ($orderprice/100)*$coupon->coupon_discount;
					
					}else{
						
						## Discount in amounts :)
						$discount = $coupon->coupon_discount;
											
					}
					
					$orderprice = $orderprice-$discount;
					
				}
						
				## Now let's do the counting of the price again.
				if ($config->variable_transcosts != 1) {
					## When no variable transaction costs are here.
					$transcost = $config->transactioncosts;
				}else{
					## Total order amount for ordercode (eid) --> variable cost is on.
					$transcost = (($orderprice/100)*$config->transcosts)+$config->transactioncosts;
				}	
				
				if($orderprice != 0){
					## Amount is not 0.00 so transactin costs are needed.
					$amount = $orderprice+$transcost;
				}else{
					## Amount  is 0.00 no transaction costs needed.
					$amount = $orderprice;
				}
				
			}
					
		return $amount;
			
	}

 	function _getDiscount($eid, $backend=0) {
			
			## Connect database now.
			$db = JFactory::getDBO();
			## Set discount to null to avoid errors.
			$discount = 0;			
			
			if($backend == 0){
				
				$total = getItems($eid);
				
				if ($total == 0){
					$transcost = 0;
					return $transcost;
				}
				
			}
			
			## getting the configuration of ticketmaster.
			$sql = 'SELECT * FROM #__ticketmaster_config WHERE configid = 1';
			$db->setQuery($sql);
			$config = $db->loadObject();		
			
			if($backend == 0){
				
				$sql='SELECT a.userid, SUM(t.ticketprice) AS orderprice 
					  FROM #__ticketmaster_tickets AS t, #__ticketmaster_orders AS a
					  WHERE a.ordercode = '.(int)$eid.'
					  AND a.ticketid = t.ticketid
					  AND a.paid = 0  
					  GROUP BY a.ordercode'; 	
				
				$db->setQuery($sql);
				$result = $db->loadObject();
				
			}else{
				
				$sql='SELECT a.userid, SUM(t.ticketprice) AS orderprice
					  FROM #__ticketmaster_tickets AS t, #__ticketmaster_orders AS a
					  WHERE a.ordercode = '.(int)$eid.'
					  AND a.ticketid = t.ticketid
					  AND a.paid = 1
					  GROUP BY a.ordercode';
				
				$db->setQuery($sql);
				$result = $db->loadObject();
								
			}	

			
			if ($result->orderprice == 0) {
				
				$discount = 0;
			
			}else{	
				
				## Get the order price!
				$orderprice = $result->orderprice;
			
				$session = JFactory::getSession();
				## Gettig the orderid if there is one.
				$couponcode = $session->get('coupon');            
				
				if ($couponcode != ''){
					
					$sql = 'SELECT * FROM #__ticketmaster_coupons 
							WHERE coupon_code = "'.$couponcode.'"';
												  
					$db->setQuery($sql);
					$coupon = $db->loadObject();			
					
					if ($coupon->coupon_type == 1){
						
						## Discount in %
						$discount		= ($orderprice/100)*$coupon->coupon_discount;
					
					}else{
						
						## Discount in amounts :)
						$discount 	= $coupon->coupon_discount;
											
					}
					
				}
				
			}
					
		return $discount;
			
	}

	function getItems($ordercode){
			
			$db = JFactory::getDBO();
						
			## Making the query to check the orderprice.
			$sql='SELECT COUNT(orderid) AS total 
				  FROM #__ticketmaster_orders
				  WHERE ordercode = '.$ordercode.'
				  AND paid != 1'; 	

			$db->setQuery($sql);
			$result = $db->loadObject();

			
			return $result->total;			
			
	}

 	function _getFees($ordercode) {
			
			## Connect database now.
			$db = JFactory::getDBO();
			
			$total = getItems($ordercode);
			
			if ($total == 0){
				$transcost = 0;
				return $transcost;
			}
			
			## getting the configuration of ticketmaster.
			$sql = 'SELECT * FROM #__ticketmaster_config WHERE configid = 1';
			$db->setQuery($sql);
			$config = $db->loadObject();		
			
			## Making the query to check the orderprice.
			$sql='SELECT a.userid, SUM(t.ticketprice) AS orderprice 
				  FROM #__ticketmaster_tickets AS t, #__ticketmaster_orders AS a
				  WHERE a.ordercode = '.(int)$ordercode.'
				  AND a.ticketid = t.ticketid
				  AND a.paid = 0  
				  GROUP BY a.ordercode'; 	
			
			$db->setQuery($sql);
			$result = $db->loadObject();	
			
			if ($result->orderprice == 0) {
			
				$transcost = 0;
			
			}else{
				
				## Get the order price!
				$orderprice = $result->orderprice;
			
				$session = JFactory::getSession();
				## Gettig the orderid if there is one.
				$couponcode = $session->get('coupon');            
				
				if ($couponcode != ''){
					
					$sql = 'SELECT * FROM #__ticketmaster_coupons WHERE coupon_code = "'.$couponcode.'"';					  
					$db->setQuery($sql);
					$coupon = $db->loadObject();			
					
					if ($coupon->coupon_type == 1){
						
						## Discount in %
						$discount = ($orderprice/100)*$coupon->coupon_discount;
					
					}else{
						
						## Discount in amounts :)
						$discount = $result->orderprice-$coupon->coupon_discount;
											
					}
					
					$orderprice = $orderprice-$discount;
					
				}				
			    
				## Check the order price again
				## If the total orderprice is 0.00 then no transaction costs have to charged.
				if($orderprice == 0){
					$transcost = 0;
					return $transcost;
				}
			
				## Now let's do the counting of the price again.
				if ($config->variable_transcosts != 1) {		
					## When no variable transaction costs are here.
					$transcost = $config->transactioncosts;
				}else{
					## Total order amount for ordercode (eid) --> variable cost is on.
					$transcost = (($orderprice/100)*$config->transcosts)+$config->transactioncosts;
				}
			
			
		return $transcost;
			
	}	
}
?>