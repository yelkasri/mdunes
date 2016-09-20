<?php
## no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
## Import library dependencies
jimport('joomla.plugin.plugin');
 
class plgRDmediaRDMepay extends JPlugin
{
/**
 * Constructor
 *
 * For php4 compatability we must not use the __constructor as a constructor for
 * plugins because func_get_args ( void ) returns a copy of all passed arguments
 * NOT references.  This causes problems with cross-referencing necessary for the
 * observer design pattern.
 */
 function plgRDMediaRDMepay( &$subject, $params ) {
 
    parent::__construct( $subject, $params );
 

	## load plugin params info
 	$plugin =& JPluginHelper::getPlugin('rdmedia', 'rdmepay');

	$this->merchantnumber = $this->params->def( 'merchantnumber', '9009' );
	$this->sandbox = $this->params->def( 'sandbox', '0' ); 
	$this->notify_email = $this->params->def( 'notify_email', '0' );
	$this->md5key = $this->params->def( 'md5key', 'NO' );
	$this->currency = $this->params->def( 'currency', 'DKK' );
	$this->paymenttype = $this->params->def( 'paymenttype', '0' );
	$this->instantcallback = $this->params->def( 'instantcallback', '1' );
	$this->failure_tpl = $this->params->def( 'failure_tpl', '0' );
	$this->success_tpl = $this->params->def( 'success_tpl', '0' );
	$this->infobox = $this->params->def( 'infobox', '0' );
	$this->layout = $this->params->def( 'layout', 0 );
	
	## Including required paths to calculator.
	$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
	include_once( $path_include );

	## Getting the global DB session
	$session =& JFactory::getSession();
	## Gettig the orderid if there is one.
	$this->ordercode = $session->get('ordercode');
	
	## Getting the amounts for this order.
	$this->amount = _getAmount($this->ordercode, 1);
	$fees		  = _getFees($this->ordercode); 
	  
 	## Beschrijving die consument op zijn/haar afschrift ziet.
	$this->description = JText::_( 'PAYMENT FOR' ).' '.$this->ordercode.' - '.JText::_( 'COMPANYNAME' );

	## Return URL after payment has been done.
	$this->declineurl  = JURI::root().'index.php?option=com_ticketmaster&view=transaction&payment_type=failedepay'; 
	## Return URL to send status update for payment.
	$this->accepturl   = JURI::root().'index.php?option=com_ticketmaster&view=transaction&payment_type=epay'; 
	$this->callbackurl = JURI::root().'index.php?option=com_ticketmaster&view=transaction&payment_type=callbackepay';
	
 }
 
/**
 * Plugin method with the same name as the event will be called automatically.
*/

	 function display()
	 {
		$app = JFactory::getApplication();
		
		// Load user_profile plugin language
		$lang = JFactory::getLanguage();
		$lang->load('plg_rdmedia_rdmepay', JPATH_ADMINISTRATOR);		

		## Loading the CSS file for ideal plugin.
		$document = JFactory::getDocument();
		$document->addStyleSheet( JURI::root(true).'/plugins/rdmedia/rdmepay/rdmedia_epay/css/epay.css' );	
		$document->addScript( 'http://www.epay.dk/js/standardwindow.js' );
		
		$user   =  JFactory::getUser();
		$userid = $user->id;		
		
		## Creating a secret md5 hash for security reasons.
		## Prepare without commas and dots
		$orderprice = number_format($this->amount, 2, '', '');		
		$md5key = md5($this->currency.$orderprice.$this->ordercode.$this->md5key);
		
		$transaction_number = md5($orderprice.$this->ordercode.$this->md5key);
		
		## Now let's check if there is temporary payment with this information:
		$db = JFactory::getDBO();
		## Doing the query to get the count of transactions for this one.
		$sql = 'SELECT * FROM #__ticketmaster_transactions_temp WHERE transaction_number = "'.$transaction_number.'" ';
		$db->setQuery($sql);
		$temp_transaction = $db->loadObjectList();
		
		##############################################################
		##															##
		## Now we insert the dta in the table for later check on	##
		## return of the customer, we do the same there again.		##
		## With the same data as above we can check against DB and	##
		## double payments as it will be set to processed then.		##
		##															##
		##############################################################

		## Count the transactions.	
		$transactions = count($temp_transaction);
		
		if ($this->amount != 0) {
		
			## If there are no transactions, insert now.
			if($transactions == 0) {
				
				## insert the data to this table for checks later on.
				$query = "INSERT INTO #__ticketmaster_transactions_temp 
						  (userid, transaction_number, ordercode, processed) 
						   VALUES ('0', '".$transaction_number."', '".$this->ordercode."', 0)";
				
				## Do the query now	
				$db->setQuery( $query );
				
				## When query goes wrong.. Show message with error.
				if (!$db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}	
			}		
			
			## Check if this is Joomla 2.5 or 3.0.+
			$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');
			
			## This will only be used if you use Joomla 2.5 with bootstrap enabled.
			## Please do not change!
			
			if(!$isJ30){
				if($config->load_bootstrap == 1){
					$isJ30 = true;
				}
			}
			
			if($this->layout == 1 && $isJ30 == true ){			
			
				echo '<img src="plugins/rdmedia/rdmepay/rdmedia_epay/images/epay-logo-vertical.png" width="240px" />';

				## The link to epay to popup.
				$epaylink = 'https://ssl.ditonlinebetalingssystem.dk/popup/default.asp';
				## Let's build the form now, we need to have some information.
				echo '<form action="'.$epaylink.'" method="post" name="ePay" target="ePay_window" id="ePay">';
	
				## Low let's get some information about your payment.	
				echo    '<input type="hidden" name="paymenttype" value="'.$this->paymenttype.'" />';
				echo    '<input type="hidden" name="merchantnumber" value="'.$this->merchantnumber.'" />';
				echo    '<input type="hidden" name="orderid" value="'.$this->ordercode.'" />';	
				echo    '<input type="hidden" name="md5key" value="'.$md5key.'" />';
				echo    '<input type="hidden" name="currency" value="'.$this->currency.'" />';
				echo    '<input type="hidden" name="amount" value="'.$orderprice.'" />';						
				echo    '<input type="hidden" name="accepturl" value="'.$this->accepturl.'" />';
				echo    '<input type="hidden" name="declineurl" value="'.$this->declineurl.'" />';
				echo    '<input type="hidden" name="callbackurl" value="'.$this->callbackurl.'" />';
				echo    '<INPUT TYPE="hidden" name="instantcallback" value="1">';
				echo    '<input type="hidden" name="language" value="2">';
				
				//echo    '<input type="button" value="" class="epay_button" style="width: 116px;" onClick="open_ePay_window()">';
				echo    '<button class="btn btn-block btn-success"  onClick="open_ePay_window()">'.JText::_( 'PLG_EPAY_PAY_WITH_EPAY' ).'</button>'; 
				
				echo '</fieldset>';
				echo '</form>';
			
			}else{
				
				echo '<div id="plg_rdmedia_epay">';
				
				echo '<div id="plg_rdmedia_epay_cards">';
				echo $this->infobox;
				echo '</div>';
				
					## The link to epay to popup.
					$epaylink = 'https://ssl.ditonlinebetalingssystem.dk/popup/default.asp';
					## Let's build the form now, we need to have some information.
					echo '<form action="'.$epaylink.'" method="post" name="ePay" target="ePay_window" id="ePay">';
		
					## Low let's get some information about your payment.	
					echo    '<input type="hidden" name="paymenttype" value="'.$this->paymenttype.'" />';
					echo    '<input type="hidden" name="merchantnumber" value="'.$this->merchantnumber.'" />';
					echo    '<input type="hidden" name="orderid" value="'.$this->ordercode.'" />';	
					echo    '<input type="hidden" name="md5key" value="'.$md5key.'" />';
					echo    '<input type="hidden" name="currency" value="'.$this->currency.'" />';
					echo    '<input type="hidden" name="amount" value="'.$orderprice.'" />';						
					echo    '<input type="hidden" name="accepturl" value="'.$this->accepturl.'" />';
					echo    '<input type="hidden" name="declineurl" value="'.$this->declineurl.'" />';
					echo    '<input type="hidden" name="callbackurl" value="'.$this->callbackurl.'" />';
					echo    '<INPUT TYPE="hidden" name="instantcallback" value="1">';
					//echo    '<input type="button" value="" class="epay_button" style="width: 116px;" onClick="open_ePay_window()">';
					echo    '<input type="hidden" name="language" value="2">';
		
					echo '<div id="plg_rdmedia_epay_confirmbutton">';
					echo    '<input type="button" value="" class="epay_button" style="width: 116px;" onClick="open_ePay_window()">';
					echo '</div>';	
					
					echo '</form>';
				
				echo '</div>';			
			
			}
			
		}
		
	 }
	 
 
	 function epay() {
		
		## Requesting variables.
		$eKey	= JRequest::getVar('eKey');
		$tid = JRequest::getInt('tid');
		$orderid = JRequest::getInt('orderid');
		$amount = JRequest::getInt('amount');
		
		## Now making the MD5 eKey:
		$db = JFactory::getDBO();
		
		## Create a new MD5 code that will be returned from ePay
		$md5key = md5($amount.$orderid.$tid.$this->md5key);
		
		## Now check our own md5 code towork it out.
		$transaction_number = md5($amount.$orderid.$this->md5key);
		
		if ( $md5key == $eKey) {
			
			## Now let's check if there is temporary payment with this information:
			$db = JFactory::getDBO();
			## Doing the query to get the count of transactions for this one.
			$sql = 'SELECT * FROM #__ticketmaster_transactions_temp WHERE transaction_number = "'.$transaction_number.'" ';
			$db->setQuery($sql);
			$transaction = $db->loadObject();
			
			if (count($transaction) == 0) {
				
				$error =  'No transaction found in our payment tables.';
				$this->_showmsg($this->failure_tpl, $error);
			
			}else{
				
				if ($transaction->processed == 1) {
					
					## Including required paths to calculator.
					$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
					include_once( $path_include );		
					
					## To be paid in the database.
					$to_be_paid = _getAmount($orderid, 1);
					$to_be_paid = number_format($to_be_paid, 2, ',', '');			

					## Getting the desired info from the configuration table
					$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$this->success_tpl."";
					$db->setQuery($sql);
					$config = $db->loadObject();
					
					## Getting the desired info from the configuration table
					$sql = "SELECT * FROM #__users WHERE id = ".(int)$transaction->userid."";
					$db->setQuery($sql);
					$user = $db->loadObject();					
					
					echo '<div class="componentheading">'.$config->mailsubject.'</div>';
					$message = str_replace('%%TID%%', $tid, $config->mailbody);
					$message = str_replace('%%OID%%', $orderid, $message);
					$message = str_replace('%%AMOUNT%%', $to_be_paid, $message);
					$message = str_replace('%%DATE%%', $transaction->create_date, $message);
					$message = str_replace('%%NAME%%', $user->name, $message);
					$message = str_replace('%%EMAIL%%', $user->email, $message);

					## Imaport mail functions:
					jimport( 'joomla.mail.mail' );
										
					## Set the sender of the email:
					$sender[0] = $config->from_email;
					$sender[1] = $config->from_name;					
					## Compile mailer function:			
					$obj = JFactory::getMailer();
					$obj->setSender( $sender );
					$obj->isHTML( true );
					$obj->setBody ( $message );				
					$obj->addRecipient($user->email);
					## Send blind copy to site admin?
					if ($config->receive_bcc == 1){
						if ($config->reply_to_email != ''){
							$obj->addBCC($obj->reply_to_email);
						}	
					}					
					## Add reply to and subject:					
					$obj->addReplyTo($config->reply_to_email);
					$obj->setSubject($config->mailsubject);
					
					if ($mail->published == 1){						
						
						$sent = $obj->Send();						
					}		
					
					echo $message;

					## Removing the session, it's not needed anymore.
					$session =& JFactory::getSession();
					$session->clear('ordercode');													
					
					
				}else{
					
					$error =  'Payment has not been processed at our website.<br>
							   The amount paid conflicts with the ordered amount or ePay did not send a message to our website.';
					$this->_showmsg($this->failure_tpl, $error);
					
				}
			}	

		}else{
		
			$error =  'You are trying to submit an invalid payment to our website.<br>The URL is conflicting with the eKey!';
			$this->_showmsg($this->failure_tpl, $error);			
		
		}
	}

	function _showmsg($msgid, $msg){
		
		$db = JFactory::getDBO();
		
		## Getting the desired info from the configuration table
		$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$msgid."";
		$db->setQuery($sql);
		$config = $db->loadObject();
	
		echo '<div class="componentheading">'.$config->mailsubject.'</div>';
		$message = str_replace('%%ERROR%%', $msg, $config->mailbody);
		echo $message;
		echo '<br/><br/><br/><br/><br/><br/>';

		## Removing the session, it's not needed anymore.
		$session =& JFactory::getSession();
		$session->clear('ordercode');													
		
		
	}

	function failedepay(){
	
			$error =  'Your payment is declined by ePay, please contact us for further information or try again.';
			$this->_showmsg($this->failure_tpl, $error);	
	
	}
	
	function callbackepay() {
			
		## Requesting variables.
		$eKey	= JRequest::getVar('eKey');
		$tid = JRequest::getInt('tid');
		$orderid = JRequest::getInt('orderid');
		$amount = JRequest::getInt('amount');
		$currency = JRequest::getInt('cur');
		
		## Now making the MD5 eKey:
		$db = JFactory::getDBO();
		
		## Create a new MD5 code that will be returned from ePay
		$md5key = md5($amount.$orderid.$tid.$this->md5key);
		## Now check our own md5 code to work it out.
		$transaction_number = md5($amount.$orderid.$this->md5key);
		
		## Including required paths to calculator.
		$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
		include_once( $path_include );		
		
		## To be paid in the database.
		$to_be_paid = _getAmount($orderid, 1);
		$to_be_paid = number_format($to_be_paid, 2, '', '');
		
		if ($md5key == $eKey) {

			if ($to_be_paid	== $amount) {

				$query = 'UPDATE #__ticketmaster_transactions_temp SET processed = 1 
							WHERE transaction_number =  "'.$transaction_number.'" ';
				## Do the query now	
				$db->setQuery( $query );
				
				## When query goes wrong.. Show message with error.
				if (!$db->query()) {
					$this->setError($db->getErrorMsg());
					return false;
				}	
				
				## Including the table transaction to store it.
				JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tables');
				$row =& JTable::getInstance('transaction', 'Table');
				
				## Making the query to check the orderprice.
				$sql='SELECT a.userid, SUM(ticketprice) AS orderprice 
					  FROM #__ticketmaster_tickets AS t, #__ticketmaster_orders AS a
					  WHERE a.ordercode = '.(int)$orderid.'
					  AND a.ticketid = t.ticketid'; 	
				
				$db->setQuery($sql);
				$result = $db->loadObject();
				
				$paid_amount = $amount/100;
				
				## Prepare the row.
				$row->transid = $tid;
				$row->userid = $result->userid;
				$row->details = '** ePay does not send this data ** Currency: '.$currency.' **';
				$row->amount = $paid_amount;
				$row->type = 'ePay Payment';
				$row->orderid = (int)$orderid;
				
				## Store data
				$row->store();	

				$query = 'UPDATE #__ticketmaster_transactions_temp SET userid = '.(int)$result->userid.' 
							WHERE transaction_number =  "'.$transaction_number.'" ';
				## Do the query now	
				$db->setQuery( $query );
				
				## When query goes wrong.. Show message with error.
				if (!$db->query()) {
					$this->setError($db->getErrorMsg());
					return false;
				}	
				
				unset($row);
				$odercode = $orderid;

				$query = 'UPDATE #__ticketmaster_orders'
					. ' SET paid = 1, published = 1'
					. ' WHERE ordercode = '.(int)$odercode.'';
				
				## Do the query now	
				$db->setQuery( $query );
				
				## When query goes wrong.. Show message with error.
				if (!$db->query()) {
					$this->setError($db->getErrorMsg());
					return false;
				}

				$query = 'SELECT * FROM #__ticketmaster_orders WHERE ordercode = '.(int)$odercode.'';

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
				$creator = new sendonpayment( (int)$odercode );  
				$creator->send();
				
				## Removing the session, it's not needed anymore.
				$session =& JFactory::getSession();
				$session->clear('ordercode');													
			
			}	
		
		}

	}	
}
?>
