<?php
## no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
## Import library dependencies
jimport('joomla.plugin.plugin');
 
class plgRDmediaAuthorized extends JPlugin
{
/**
 * Constructor
 *
 * For php4 compatability we must not use the __constructor as a constructor for
 * plugins because func_get_args ( void ) returns a copy of all passed arguments
 * NOT references.  This causes problems with cross-referencing necessary for the
 * observer design pattern.
 */
 function plgRDMediaAuthorized( $subject , $params ) {
 
    parent::__construct( $subject , $params );
	
	// Load user_profile plugin language
	$lang = JFactory::getLanguage();
	$lang->load('plg_rdmedia_authorized', JPATH_ADMINISTRATOR);
 
	## load plugin params info
 	$plugin = JPluginHelper::getPlugin('authorized', 'authorized');
	$this->x_login = $this->params->def( 'x_login', 'Invalid' );
	$this->x_tran_key = $this->params->def( 'x_tran_key', 'Invalid' );
	$this->sandbox_on = $this->params->def( 'sandbox_on', 1 );
	$this->success_tpl = $this->params->def( 'success_tpl', 1 );
	$this->failure_tpl = $this->params->def( 'failure_tpl', 1 );
	$this->dump_response = $this->params->def( 'dump_response', 0 );
	$this->dump_sent_fields = $this->params->def( 'dump_sent_fields', 0 );
	$this->infobox = $this->params->def( 'infobox', 'Please enter your information below to process the order:' );	
	
	
	
	## Including required paths to calculator.
	$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
	include_once( $path_include );

	## Getting the global DB session
	$session =& JFactory::getSession();
	## Gettig the orderid if there is one.
	$this->ordercode = $session->get('ordercode');
	
	## Getting the amounts for this order.
	$amount      = _getAmount($this->ordercode);
	$fees		 = _getFees($this->ordercode); 
	
	$this->return_amount = _getAmount($this->ordercode, 1);
	$this->return_amount = number_format($this->return_amount, 2, '.', '');
	
	## Prepare for Mollie.
	$this->amount = $amount;
	$this->fees = $fees;

	## Return URL after payment has been done.
	## In the page where the redirect goed a call will be done to Authorized.net (CURL required)
	$this->return_url  = JURI::root().'index.php?option=com_ticketmaster&view=transaction&payment_type=authorized'; 
	$this->error_url  = JURI::root().'index.php?option=com_ticketmaster&view=payment'; 

 }
 
/**
 * Plugin method with the same name as the event will be called automatically.
*/

	 function display()
	 {
		$app = &JFactory::getApplication();
		
		$lang = JFactory::getLanguage();
		$lang->load('plg_rdmedia_authorized', JPATH_ADMINISTRATOR);		
		
		## Making sure PayPal getting the right amount (23.00)
		$ordertotal = number_format($this->amount, 2, '.', '');			

		## Loading the CSS file for ideal plugin.
		$document = &JFactory::getDocument();
		$document->addStyleSheet( JURI::root(true).'/plugins/rdmedia/authorized/rdmedia_authorized/css/authorized.css' );	

		$year = date("Y"); 
		
		## Creating the drop down menu for years,
		$options[] = JHTML::_('select.option', '0', JText::_( 'PLG_TICKETMSTER_AUTHORIZED_YEAR' ));
		
		for ($i = $year, $n = $year+15; $i <= $n; $i++ ){
			 $options[] = JHTML::_('select.option', $i, $i);
		}												
		
		## Create <select name="x_exp_date_year" class="inputbox"></select> ##
		$lists['year'] = JHTML::_('select.genericlist', $options, 'x_exp_date_year', 'class="inputbox"', 'value', 'text', 0);	
		
		## Creating the drop down menu for years,
		$months[] = JHTML::_('select.option', '0', JText::_( 'PLG_TICKETMSTER_AUTHORIZED_MONTHS' ));
		$months[] = JHTML::_('select.option', '01', '01' );
		$months[] = JHTML::_('select.option', '02', '02' );
		$months[] = JHTML::_('select.option', '03', '03' );
		$months[] = JHTML::_('select.option', '04', '04' );
		$months[] = JHTML::_('select.option', '05', '05' );
		$months[] = JHTML::_('select.option', '06', '06' );
		$months[] = JHTML::_('select.option', '07', '07' );
		$months[] = JHTML::_('select.option', '08', '08' );
		$months[] = JHTML::_('select.option', '09', '09' );
		$months[] = JHTML::_('select.option', '10', '10' );
		$months[] = JHTML::_('select.option', '11', '11' );
		$months[] = JHTML::_('select.option', '12', '12' );							
		
		## Create <select name="x_exp_date_month" class="inputbox"></select> ##
		$lists['month'] = JHTML::_('select.genericlist', $months, 'x_exp_date_month', 'class="inputbox"', 'value', 'text', 0);			
		
		## Check if partner id is given.
		if($this->x_login == 'Invalid') {
			
			## if not, please stop the script.
			echo JText::_( 'PLG_TICKETMSTER_AUTHORIZED_NO_LOGIN' );
		
		}else{
			
			if ($ordertotal > '0.00') {
				
				$description = str_replace("%%OC%%", $this->ordercode, $this->infobox);
				
				## Form to show to client.
				echo '<form action = "index.php" method="POST" name="adminForm" id="adminForm">';	
				echo '<div id="plg_rdmedia_authorized">';
	
				//echo //'<div id="plg_rdmedia_authorized_cards">';
				//	echo// $description.'---'.$this->return_amount;			
				//echo //'</div>';	
				
				echo '<div id="plg_rdmedia_authorized_info" style="margin-top:5px;">';		
					
					echo '<div id="plg_rdmedia_authorized_info_label">';		
							echo JText::_( 'PLG_TICKETMSTER_AUTHORIZED_CARDHOLDER_FIRSTNAME' );
					echo '</div>';		
					
					echo '<div id="plg_rdmedia_authorized_info_label">';		
							echo JText::_( 'PLG_TICKETMSTER_AUTHORIZED_CARDHOLDER_LASTNAME' );
					echo '</div>';								
						
				echo '</div>';					
	
				echo '<div id="plg_rdmedia_authorized_info">';		
					
					echo '<div id="plg_rdmedia_authorized_info_label">';		
							echo    '<input type="text" name="x_first_name" value="" size="15" />';
					echo '</div>';	
					
					echo '<div id="plg_rdmedia_authorized_info_label">';		
							echo    '<input type="text" name="x_last_name" value="" size="15" />';
					echo '</div>';									
						
				echo '</div>';		
				
				echo '<div id="plg_rdmedia_authorized_info">';		
					
					echo '<div id="plg_rdmedia_authorized_info_label">';		
							echo JText::_( 'PLG_TICKETMSTER_AUTHORIZED_CARDNUMBER' );
					echo '</div>';	
					
					echo '<div id="plg_rdmedia_authorized_info_label">';		
							echo JText::_( 'PLG_TICKETMSTER_AUTHORIZED_CARD_EXPIRATION' );
					echo '</div>';	
					
					echo '<div id="plg_rdmedia_authorized_info_label">';		
							echo JText::_( 'PLG_TICKETMSTER_AUTHORIZED_CCV' );
					echo '</div>';														
						
				echo '</div>';				
				
				echo '<div id="plg_rdmedia_authorized_info">';		
					
					echo '<div id="plg_rdmedia_authorized_info_label">';		
							echo    '<input type="text" name="x_card_num" id="x_card_num" value="" size="15" />';
					echo '</div>';		
					
					echo '<div id="plg_rdmedia_authorized_info_label">';		
							echo $lists['month'];
							echo $lists['year'];
					echo '</div>';	
					
					echo '<div id="plg_rdmedia_authorized_info_label">';		
							echo    '<input type="text" name="x_card_code" id="x_card_code" value="" size="5" />';
					echo '</div>';		
					
					echo '<div id="plg_rdmedia_authorized_info_label">';		
							echo    '<input type="submit" name="submit" class="button" value="Pay By Authorized.net" />';
					echo '</div>';															
						
				echo '</div>';								
	
				echo    '<input type="hidden" name="option" value="com_ticketmaster" />';
				echo    '<input type="hidden" name="view" value="transaction" />';
				echo    '<input type="hidden" name="payment_type" value="authorized" />';						
	
				echo '</div>';	
				echo '</form>';	
			
			}
		
		}
		
		return true;
	 }
	 
	 function message( $id, $msg, $reason, $transid, $amount, $ordercode, $cardtype ){
	 	
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		
		## Getting the failure template.
		$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$id."";
		$db->setQuery($sql);
		$config = $db->loadObject();		
		
		## Header for the page in H1 formatting.
		echo '<h1>'.$config->mailsubject.'</h1>';
		
		## replacing some usefull information.
		$message = str_replace('%%MSG%%', $msg, $config->mailbody);
		$message = str_replace('%%REASON%%', $reason, $message);
		$message = str_replace('%%NAME%%', $user->name, $message);
		
		$message = str_replace('%%TRANSID%%', $transid, $message);
		$message = str_replace('%%AMOUNT%%', $amount, $message);
		$message = str_replace('%%ORDERCODE%%', $ordercode, $message);
		$message = str_replace('%%CARDTYPE%%', $cardtype, $message);
		
		$message = str_replace('%%LINK%%', $this->error_url, $message);
		
		## Show message
		echo $message;
		
		## Some cleaning spacing at the bottom :)
		echo '<br/><br/><br/><br/><br/><br/>';
		
		if($id != $this->failure_tpl){
		
			## Removing the session, it's not needed anymore.
			$session =& JFactory::getSession();
			$session->clear('ordercode');
			$session->clear('coupon');	
		
		}
		
		## No U-Turn :)
		return false;			
	 
	 }
	 
	 function authorized() {

		
		## Include the authorized.net class to process payment. 
		$path_require = JPATH_SITE.DS.'plugins'.DS.'rdmedia'.DS.'authorized'.DS.'authorized-library'.DS.'authorizenet.class.php';
		require_once($path_require);
		
		$user = JFactory::getUser();	
		$db = JFactory::getDBO();	
		
		$a = new authorizenet_class;
		
		## Chosing payment type (Real or Sansbox)
		$a->gatewaytype($this->sandbox_on);		
		
		$a->add_field('x_login', $this->x_login);
		$a->add_field('x_tran_key', $this->x_tran_key);		
		
		$a->add_field('x_version', '3.1');
		$a->add_field('x_type', 'AUTH_CAPTURE');
		  
		$a->add_field('x_relay_response', 'FALSE');	
		$a->add_field('x_delim_data', 'TRUE');
		$a->add_field('x_delim_char', '|');     
		$a->add_field('x_encap_char', '');
		
		## Doing the query to get the count of transactions for this one.
		$sql = 'SELECT * FROM #__ticketmaster_clients WHERE userid = "'.(int)$user->id.'" ';
		$db->setQuery($sql);
		$userinfo = $db->loadObject();	
		
		$sql = 'SELECT * FROM #__ticketmaster_country WHERE country_id = "'.(int)$userinfo->country_id.'" ';
		$db->setQuery($sql);
		$country = $db->loadObject();			
		
		## Setup fields for customer information.  This would typically come from an
		## array of POST values froma secure HTTPS form.

		$x_first_name	= JRequest::getVar('x_first_name');
		$x_last_name	= JRequest::getVar('x_last_name');
		
		$a->add_field('x_first_name', $x_first_name);
		$a->add_field('x_last_name', $x_last_name);
		$a->add_field('x_address', $userinfo->address);
		$a->add_field('x_city', $userinfo->city);
		$a->add_field('x_zip', $userinfo->zipcode);
		$a->add_field('x_country', $country->country_2_code);
		$a->add_field('x_email', $user->email);
		$a->add_field('x_phone', $userinfo->phonenumber);
		
		##########################################################################################
		## Using credit card number '4007000000027' performs a successful test.  This			##
		## allows you to test the behavior of your script should the transaction be				##
		## successful.  If you want to test various failures, use '4222222222222' as			##
		## the credit card number and set the x_amount field to the value of the 				##
		## Response Reason Code you want to test.  												##							
		## 																						##
		## For example, if you are checking for an invalid expiration date on the				##
		## card, you would have a condition such as:											##
		## if ($a->response['Response Reason Code'] == 7) ... (do something)					##
		##																						##	
		## Now, in order to cause the gateway to induce that error, you would have to			##
		## set x_card_num = '4222222222222' and x_amount = '7.00'								##
		##########################################################################################
		
		$cardholder			= JRequest::getVar('cardholder', 0);
		$x_card_num			= JRequest::getVar('x_card_num', 0);
		$x_card_code		= JRequest::getInt('x_card_code', 0);
		$x_exp_date_year	= JRequest::getVar('x_exp_date_year', 0);
		$x_exp_date_month	= JRequest::getVar('x_exp_date_month', 0);
		
		if ($x_exp_date_year == 0 || $x_exp_date_month ==0){

			  ## Get the failed payment template
			  $id = $this->failure_tpl;
			  ## Get the error reason
			  if ($x_exp_date_month == 0){
			  	$message = JText::_( 'PLG_TICKETMSTER_MONTH_NOT_FILLED' );
			  }else{
			  	$message = JText::_( 'PLG_TICKETMSTER_YEAR_NOT_FILLED' );
			  }
			  
			  ## Process error, and show the screen.
			  self::message($id, $message, $reason, $transid, $amount, $ordercode, $cardtype);
			
		}
		
		if ($x_card_code == 0){

			  ## Get the failed payment template
			  $id = $this->failure_tpl;
			  ## Get the error reason
			  $message = JText::_( 'PLG_TICKETMSTER_CARDCODE_NOT_FILLED' );

			  ## Process error, and show the screen.
			  self::message($id, $message, $reason, $transid, $amount, $ordercode, $cardtype);
			
		}	
		
		if ($x_card_num == 0){

			  ## Get the failed payment template
			  $id = $this->failure_tpl;
			  ## Get the error reason
			  $message = JText::_( 'PLG_TICKETMSTER_CARDNUMBER_NOT_FILLED' );

			  ## Process error, and show the screen.
			  self::message($id, $message, $reason, $transid, $amount, $ordercode, $cardtype);
			
		}			
		
		##  Setup fields for payment information
		$a->add_field('x_method', 'CC');
		
		##$a->add_field('x_card_num', '4007000000027');   // test successful visa
		##$a->add_field('x_card_num', '370000000000002');   // test successful american express
		##$a->add_field('x_card_num', '6011000000000012');  // test successful discover
		##$a->add_field('x_card_num', '5424000000000015');  // test successful mastercard
		##$a->add_field('x_card_num', '4222222222222');    // test failure card number
		
		$a->add_field('x_card_num', $x_card_num);
		$a->add_field('x_amount', $this->amount); // the amount of money
		$a->add_field('x_exp_date', $x_exp_date_month.$x_exp_date_year);    // march of 2008
		$a->add_field('x_card_code', $x_card_code);    // Card CAVV Security code 903
		$a->add_field('x_description', 'Order: '.$this->ordercode);
		$a->add_field('x_invoice_num', $this->ordercode);
		$a->add_field('x_cust_id', $user->id);
		
		$ai = $a->process();
		
		## Process the payment and output the results
		switch ($ai) {
		
		   case 1:  ## Payment Successs
				
				## If there are more transactions.
				if ($a->response['Response Reason Code'] == '311') {
					$error = JText::_( 'PLG_TICKETMSTER_ALREADY_CAPTURED' );
					exit($error);	
				}	
				
				## Check the returned amount with our order amount
				if  ($a->response['Amount'] != 	$this->return_amount) {
					$error = JText::_( 'PLG_TICKETMSTER_AMOUNT_NOT_CORRECT' );
					exit($error);
				}	
						
			  	## include the table for saving transaction data:
				JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tables');
				$row =& JTable::getInstance('transaction', 'Table');	
				## get user information:
				$user = & JFactory::getUser();						
				
				## Getting transaction date
				$customerdata = $a->get_response();
				$customerdata = str_replace('|', ' & ', $customerdata);
				
				## Prepare the row for insert.
				$row->transid = $a->response['Transaction ID'];
				$row->userid = $user->id;
				$row->details = $customerdata;
				$row->amount = $a->response['Amount'];
				$row->email_paypal = $user->email;
				$row->type = 'Authorized.net';
				$row->orderid = (int)$this->ordercode;
				
				$transid = $a->response['Transaction ID'];
				$amount = $a->response['Amount'];
				$ordercode = $this->ordercode;
				$cardtype = $a->response['Reserved Field 11'].' - '.$a->response['Reserved Field 10'];
				
				## Store data
				$row->store();	
				
				$query = 'UPDATE #__ticketmaster_orders SET paid = 1, published = 1 WHERE ordercode = "'.(int)$this->ordercode.'" ';						
				
				## Doing the Query
				$db->setQuery( $query );
				
				## When query goes wrong.. Show message with error.
				if (!$db->query()) {
					$this->setError($db->getErrorMsg());
					return false;
				}	
				
				$query = 'SELECT * FROM #__ticketmaster_orders WHERE ordercode = '.(int)$this->ordercode.'';

				## Do the query now	
				$db->setQuery($query);
				$data = $db->loadObjectList();
	
				
				$k = 0;
				for ($i = 0, $n = count($data); $i < $n; $i++ ){
					
				$row  = &$data[$i];
					
				## Include the confirmation class to sent the tickets. 
				$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'createtickets.class.php';
				$override = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'override'.DS.'createtickets.class.php';
				
				## Check if the override is there.
				if (file_exists($override)) {
					## Yes, now we use it.
					require_once($override);
				} else {
					## No, use the standard
					require_once($path);
				}	
					
					if(isset($row->orderid)) {  
					
						$creator = new ticketcreator( (int)$row->orderid );  
						$creator->doPDF();
					
					}  				
					
					$k=1 - $k;
					
				}					
				
				## Include the confirmation class to sent the tickets. 
				$path_include = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'sendonpayment.class.php';
				include_once( $path_include );
				
				## Sending the ticket immediatly to the client.
				$sender = new sendonpayment( (int)$this->ordercode );  
				$sender->send();					
				
			    ## Get the failed payment template
			    $id = $this->success_tpl;
			    ## Get the error reason
			    $message = $a->get_response_reason_text();
			  
			    ## Process error, and show the screen.
			    self::message($id, $message, $reason, $transid, $amount, $ordercode, $cardtype);			  
				
			  break;
			  
		   case 2:  ## Payment Declined
			  
			  ## Get the failed payment template
			  $id = $this->failure_tpl;
			  ## Get the error reason
			  $message = $a->get_response_reason_text();
			  ## Show error in screen.
			  $reason = JText::_( 'PLG_TICKETMSTER_DECLINED_TRANSACTION' );
			  
			  ## Process error, and show the screen.
			  self::message($id, $message, $reason, $transid, $amount, $ordercode, $cardtype);
			  
			  break;
			  
		   case 3:  ## Payment Error
			  
			  ## Get the failed payment template
			  $id = $this->failure_tpl;
			  ## Get the error reason
			  $message = $a->get_response_reason_text();
			  ## Show error in screen.
			  $reason = JText::_( 'PLG_TICKETMSTER_ERROR_WITH_TRANSACTION' );
			  
			  ## Process error, and show the screen.
			  self::message($id, $message, $reason, $transid, $amount, $ordercode, $cardtype);
			  
			  break;
		}

		## The following two functions are for debugging and learning the behavior
		## of authorize.net's response codes.  They output nice tables containing
		## the data passed to and recieved from the gateway.
		
		if ($this->dump_sent_fields == 1){	
			## outputs all the fields that we set	
			$a->dump_fields(); 
		}
		if ($this->dump_response == 1){		
			## outputs the response from the payment gateway
			$a->dump_response();  
		}	
		
	}

}
?>
