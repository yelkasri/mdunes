<?php
## no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
## Import library dependencies
jimport('joomla.plugin.plugin');
 
class plgRDmediaRDMpayfast extends JPlugin
{
/**
 * Constructor
 *
 * For php4 compatability we must not use the __constructor as a constructor for
 * plugins because func_get_args ( void ) returns a copy of all passed arguments
 * NOT references.  This causes problems with cross-referencing necessary for the
 * observer design pattern.
 */
 function plgRDMediaRDMpayfast( &$subject, $params  ) {
 
    parent::__construct( $subject , $params  );
	
	## Loading language:	
	$lang = JFactory::getLanguage();
	$lang->load('plg_rdmedia_rdmpayfast', JPATH_ADMINISTRATOR);	

	## load plugin params info
 	$plugin =& JPluginHelper::getPlugin('rdmedia', 'rdmpayfast');

	$this->merchant_id = $this->params->def( 'merchant_id', 2112 );
	$this->merchant_key = $this->params->def( 'merchant_key', 'EUR' );
	$this->sandbox_on = $this->params->def( 'sandbox_on', 1 );
	$this->success_tpl = $this->params->def( 'success_tpl', 1 );
	$this->failure_tpl = $this->params->def( 'failure_tpl', 1 );
	$this->cancel_tpl = $this->params->def( 'cancel_tpl', 1 );
	$this->layout = $this->params->def( 'layout', 1 );
	$this->pdt_key = $this->params->def( 'pdt_key', 0 );
	$this->item_description = $this->params->def( 'item_description', '' );
	$this->remove_session = $this->params->def( 'remove_session', 1 );
	
	## Including required paths to calculator.
	$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
	include_once( $path_include );

	## Getting the global DB session
	$session =& JFactory::getSession();
	## Gettig the orderid if there is one.
	$this->ordercode = $session->get('ordercode');
	
	## Getting the amounts for this order.
	$this->amount = _getAmount($this->ordercode);
	$this->fees	  = _getFees($this->ordercode); 

	## Return URLS to your website after processing the order.
	$this->return_url = JURI::root().'index.php?option=com_ticketmaster&view=transaction&payment_type=payfast';
	$this->cancel_url = JURI::root().'index.php?option=com_ticketmaster&view=transaction&payment_type=payfast_cancelled';
	
	## Use the sandbox if you're testing. (Required: Sandbox Account with PayPal)
	if ($this->sandbox_on == 1){
		## We're in a testing environment.
		$this->url = 'https://sandbox.payfast.co.za/eng/process';
	}else{
		## Use the lines below for a live site.
		$this->url = 'https://www.payfast.co.za/eng/process';
	}
	
 }
 
/**
 * Plugin method with the same name as the event will be called automatically.
 * You have to get at least a function called display, and the name of the processor (in this case paypal)
 * Now you should be able to display and process transactions.
 * 
*/

	 function display()
	 {
		$app = &JFactory::getApplication();
		
		## Loading the CSS file for ideal plugin.
		$document = &JFactory::getDocument();
		$document->addStyleSheet( JURI::root(true).'/plugins/rdmedia/rdmpayfast/rdmedia_payfast/css/payfast.css' );	
		
		$user = JFactory::getUser();
		
		## Making sure PayPal getting the right amount (23.00)
		$ordertotal = number_format($this->amount, 2, '.', '');		
		
		## Check the amount, if higher then 0.00 then show the plugin data.	
		if ($ordertotal > '0.00') {
			
			## Check if this is Joomla 2.5 or 3.0.+
			$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');
			
			## This will only be used if you use Joomla 2.5 with bootstrap enabled.
			## Please do not change!
			
			if(!$isJ30){
				if($config->load_bootstrap == 1){
					$isJ30 = true;
				}
			}	
			
			if($this->layout == 1 ){

				echo '<img src="plugins/rdmedia/rdmpayfast/rdmedia_payfast/images/payfast_logo.png" />';
				
				echo '<form action="'.$this->url.'" method="post" name="paypalForm">';
				## Low let's get some information about your payment.	
				echo    '<input type="hidden" name="item_number" value="'.$this->ordercode.'" />';
				echo    '<input type="hidden" name="amount" value="'.$ordertotal.'" />';	
				echo 	'<input type="hidden" name="item_name" value="Order: '.$this->ordercode.'">';
				echo    '<input type="hidden" name="merchant_key" value="'.$this->merchant_key.'">';
				echo	'<input type="hidden" name="merchant_id" value="'.$this->merchant_id.'">';
				echo	'<input type="hidden" name="custom_str1" value="'.$this->ordercode.'">';
				echo	'<input type="hidden" name="return_url" value="'.$this->return_url.'">';
				echo	'<input type="hidden" name="cancel_url" value="'.$this->cancel_url.'">';
				echo	'<input type="hidden" name="item_description" value="'.$this->item_description.'">';

				
				echo    '<button class="btn btn-block btn-success" style="margin-top: 8px;" type="submit">'.JText::_( 'PLG_PAYFAST_PAYNOW_BUTTON' ).'</button>';			
				echo 	'</form>';
					
				
			}else{
			
				## Let's build the form now, we need to have some information.
				echo '<form action="'.$this->url.'" method="post" name="paypalForm">';
				
				echo '<div id="plg_rdmedia_payfast">';
				
				echo '<div id="plg_rdmedia_payfast_cards">';
				echo $this->infobox;
				echo '</div>';
					
					## Low let's get some information about your payment.	
					echo    '<input type="hidden" name="item_number" value="'.$this->ordercode.'" />';
					echo    '<input type="hidden" name="amount" value="'.$ordertotal.'" />';	
					echo 	'<input type="hidden" name="item_name" value="Order: '.$this->ordercode.'">';
					echo    '<input type="hidden" name="merchant_key" value="'.$this->merchant_key.'">';
					echo	'<input type="hidden" name="merchant_id" value="'.$this->merchant_id.'">';
					echo	'<input type="hidden" name="custom_str1" value="'.$this->ordercode.'">';
					echo	'<input type="hidden" name="return_url" value="'.$this->return_url.'">';
					echo	'<input type="hidden" name="cancel_url" value="'.$this->cancel_url.'">';
					echo	'<input type="hidden" name="item_description" value="'.$this->item_description.'">';
					
					echo '<div id="plg_rdmedia_payfast_confirmbutton">';
					echo    '<input type="submit" name="submit" value="" class="payfast_button" style="width: 116px;">';
					echo '</div>';	
				
				echo '</div>';
				
				echo '</form>';
			
			}
		
		}
		
		return true;
	 }

	function _showmsg($msgid, $msg){
		
		$db = JFactory::getDBO();
		
		## Getting the desired info from the configuration table
		$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$msgid."";
		$db->setQuery($sql);
		$config = $db->loadObject();
	
		echo '<h1>'.$config->mailsubject.'</h1>';
		$message = str_replace('%%MSG%%', $msg, $config->mailbody);
		echo $message;
		echo '<br/><br/><br/><br/><br/><br/>';

		## Removing the session, it's not needed anymore.
		$session =& JFactory::getSession();
		$session->clear('ordercode');													
				
	}
	
	function payfast_cancelled(){
		
		$db = JFactory::getDBO();
		
		## Getting the desired info from the configuration table
		$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$this->cancel_tpl."";
		$db->setQuery($sql);
		$config = $db->loadObject();
		
		echo '<h1>'.$config->mailsubject.'</h1>';
		
		echo $config->mailbody;
		echo '<br/><br/><br/><br/><br/><br/>';
		
		if($this->remove_session == 1){
		
			## Removing the session, it's not needed anymore.
			$session =& JFactory::getSession();
			$session->clear('ordercode');		
		
		}
		
	}


	function payfast() {

		// Load user_profile plugin language
		$lang = JFactory::getLanguage();
		$lang->load('plg_rdmedia_payfast', JPATH_ADMINISTRATOR);
	
		## Include the confirmation class to sent the tickets. 
		$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'createtickets.class.php';
		$override = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'override'.DS.'createtickets.class.php';	
		
		// Variable Initialization
		$pmtToken = isset( $_GET['pt'] ) ? $_GET['pt'] : null;

		if( !empty( $pmtToken ) )
		{
			// Variable Initialization
			$error = false;
			$authToken = $this->merchant_key;
			$authToken = $this->pdt_key;
			$req = 'pt='. $pmtToken .'&at='. $authToken;
			$data = array();
			
			if($this->sandbox_on == 1){
				$host = 'sandbox.payfast.co.za';
			}else{
				$host = 'www.payfast.co.za';
			}
			
		
			//// Connect to server
			if( !$error )
			{
				// Construct Header
				$header = "POST /eng/query/fetch HTTP/1.0\r\n";
				$header .= 'Host: '. $host ."\r\n";
				$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
				$header .= 'Content-Length: '. strlen( $req ) ."\r\n\r\n";
		
				// Connect to server
				$socket = fsockopen( 'ssl://'. $host, 443, $errno, $errstr, 10 );
		
				if( !$socket )
				{
					$error = true;
					print( 'errno = '. $errno .', errstr = '. $errstr );
				}
			}
			

			## Get data from server
			if( !$error )
			{
				## Send command to server
				fputs( $socket, $header . $req );
		
				## Read the response from the server
				$res = '';
				$headerDone = false;
		
				while( !feof( $socket ) )
				{
					$line = fgets( $socket, 1024 );
		
					## Check if we are finished reading the header yet
					if( strcmp( $line, "\r\n" ) == 0 )
					{
						## read the header
						$headerDone = true;
					}
					## If header has been processed
					else if( $headerDone )
					{
						## Read the main response
						$res .= $line;
					}
				}
		
				## Parse the returned data
				$lines = explode( "\n", $res );
			}

			## Interpret the response from server
			if( !$error )
			{
				$result = trim( $lines[0] );
		
				## If the transaction was successful
				if( strcmp( $result, 'SUCCESS' ) == 0 )
				{
					## Process the reponse into an associative array of data
					for( $i = 1; $i < count( $lines ); $i++ )
					{
						list( $key, $val ) = explode( "=", $lines[$i] );
						$data[urldecode( $key )] = stripslashes( urldecode( $val ) );
					}
				}
				
				## If the transaction was NOT successful
				else if( strcmp( $result, 'FAIL' ) == 0 )
				{
					## Log for investigation
					$error = true;
					exit('error');
					
				}
				}
		
				## Process the payment
				if( !$error )
				{
				   
					## Sorting data:
					ksort($data);
					##print_r($data);
					
					## Check if the status is complete:
					if ($data['payment_status'] != 'COMPLETE') {
						
						## Amounts are not the same. Show the message to the client.
						$msg = JText::_( 'PLG_PAYFAST_PAYMENT_NOT_COMPLETED' );
						$this->_showmsg($this->failure_tpl, $msg);	
						
					}else{
					
						## Connecting the database
						$db = JFactory::getDBO();
						## Current date for database.
						$trans_date = date("d-m-Y H:i");
						## Getting the transaction info from PP.
						$payment_id = $data['pf_payment_id'];
						
						## Check that txn_id has not been previously processed
						$sql = 'SELECT COUNT(pid) AS total
						FROM #__ticketmaster_transactions
						WHERE transid = "'.$payment_id.'" ';
						
						$db->setQuery($sql);
						$results = $db->loadObject();		

						if($result->total > 0){
							
							## Show error on failed - Transaction may not exsist in DB.
							$msg = JText::_( 'PLG_PAYFAST_TRANSACTION_PROCESSED' );
							$this->_showmsg($this->failure_tpl, $msg);	
							
						}else{	
					    
						    // Get the data from the new array as needed
							$nameFirst   = $data['name_first'];
							$nameLast    = $data['name_last'];
							$amountGross = $data['amount_gross'];
							
							## Paid amount to PayPal
							$payment_amount = $data['amount_gross'];
							## Get the email address from the buyer.
							$payer_email 	= $data['email_address'];
							## Get the order information sent by PP.
							$orderid 		= $data['custom_str1'];
							
							## Including required paths to calculator.
							$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
							include_once( $path_include );
								
							## Getting the amounts for this order.
							$amount = _getAmount($data['custom_str1'], 1);
							
							##Requested amount for this order.
							$amount_req = number_format($amount, 2, '', '');
							## Sent amount by PP (needs the same notation as ours)
							$amount_pp = number_format($data['amount_gross'], 2, '', '');
							
							## Check if the amount is the same as the paid amount.
							if ($amount_req != $amount_pp) {
								
								## Amounts are not the same. Show the message to the client.
								$msg = JText::_( 'PLG_PAYFAST_AMOUNT_IS_NOT_CORRECTLY' );
								$this->_showmsg($this->failure_tpl, $msg);						
							
							}else{
							
								## Getting the latest logged in user.
								$user = & JFactory::getUser();
					
								JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tables');
								$row =& JTable::getInstance('transaction', 'Table');	
							
								## Pickup All Details and create foo=bar&baz=boom&cow=milk&php=hypertext+processor
								$payment_details = http_build_query($data);
								$payment_type = 'PayFast';
								$orderid = $data['custom_str1'];
								
								## Now store all data in the transactions table
								$row->transid = $data['pf_payment_id'];
								$row->userid = $user->id;
								$row->details = $payment_details;
								$row->amount = $amount_req;
								$row->type = 'PayFast';
								$row->email_paypal = $user->email;
								$row->orderid = $data['custom_str1'];
								
								## Store data
								$row->store();		
								
								$query = 'UPDATE #__ticketmaster_orders'
									. ' SET paid = 1, published = 1'
									. ' WHERE ordercode = '.(int)$data['custom_str1'].'';
								
								## Do the query now	
								$db->setQuery( $query );
								
								## When query goes wrong.. Show message with error.
								if (!$db->query()) {
									$this->setError($db->getErrorMsg());
									return false;
								}
								
								## TRANSACTION HAS BEEN RECORED ##
								
								$query = 'SELECT * FROM #__ticketmaster_orders WHERE ordercode = '.$data['custom_str1'].'';

								## Do the query now
								$db->setQuery($query);
								$tickets = $db->loadObjectList();
								
								## Include the confirmation class to sent the tickets.
								$path_include = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'sendonpayment.class.php';
								include_once( $path_include );								
									
								
								$k = 0;
								for ($i = 0, $n = count($tickets); $i < $n; $i++ ){
									
								$row  = $tickets[$i];
								
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

								## Sending the ticket immediatly to the client.
								$creator = new sendonpayment( (int)$data['custom_str1'] );
								$creator->send();
								
								## Removing the session, it's not needed anymore.
								$session =& JFactory::getSession();
								$session->clear($data['custom_str1']);
								$session->clear('ordercode');
								$session->clear('coupon');
								
								## Getting the desired info from the configuration table
								$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$this->success_tpl."";
								$db->setQuery($sql);
								$config = $db->loadObject();
										
								## Getting the desired info from the configuration table
								$sql = "SELECT * FROM #__users WHERE id = ".(int)$user->id."";
								$db->setQuery($sql);
								$user = $db->loadObject();
			
								echo '<h1>'.$config->mailsubject.'</h1>';
			
								$message = str_replace('%%TID%%', $response['txn_id'], $config->mailbody);
								$message = str_replace('%%OID%%', $data['custom_str1'], $message);
								$message = str_replace('%%AMOUNT%%', $amount_req, $message);
								$message = str_replace('%%DATE%%', $trans_date, $message);
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
										$obj->addRecipient($obj->reply_to_email);
									}
								}
								
								## Add reply to and subject:
								$obj->addReplyTo($config->reply_to_email);
								$obj->setSubject($config->mailsubject);
			
								if ($mail->published == 1){
									
								$sent = $obj->Send();
								}
			
								echo $message;								
								
								
							}
							
						}
						
					}
					
				}
		
				// Close socket if successfully opened
				if( $socket )
				fclose( $socket );
		}		
		
		
	
	}	
}	 
?>