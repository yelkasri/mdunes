<?php
## no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
## Import library dependencies
jimport('joomla.plugin.plugin');
 
class plgRDmediaRDMideal extends JPlugin
{
/**
 * Constructor
 *
 * For php4 compatability we must not use the __constructor as a constructor for
 * plugins because func_get_args ( void ) returns a copy of all passed arguments
 * NOT references.  This causes problems with cross-referencing necessary for the
 * observer design pattern.
 */
 function plgRDMediaRDMideal( $subject , $params ) {
 
    parent::__construct( $subject , $params );
	
	// Load user_profile plugin language
	$lang = JFactory::getLanguage();
	$lang->load('plg_rdmedia_rdmideal', JPATH_ADMINISTRATOR);
 
	## load plugin params info
 	$plugin =& JPluginHelper::getPlugin('rdmedia', 'rdmideal');
	$this->partner_id = $this->params->def( 'partner_id', 2112 );
	$this->sandbox_on = $this->params->def( 'sandbox_on', 1 );
	$this->success_tpl = $this->params->def( 'success_tpl', 1 );
	$this->failure_tpl = $this->params->def( 'failure_tpl', 1 );
	$this->infobox = $this->params->def( 'infobox', 'enter a message in the backend.' );
	$this->description = $this->params->def( 'description', 'Ordercode:' );	
	$this->developermode = $this->params->def( 'developermode', 0 );	
	$this->developer_email = $this->params->def( 'developer_email', 'noreply@yourdomain.com' );
	$this->confirmation = $this->params->def( 'confirmation', 0 );	
	$this->layout = $this->params->def( 'layout', 0 );	
	
	## Including required paths to calculator.
	$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
	include_once( $path_include );

	## Getting the global DB session
	$session =& JFactory::getSession();
	## Gettig the orderid if there is one.
	$this->ordercode = $session->get('ordercode');
	
	## Getting the amounts for this order.
	$amount      = _getAmount($this->ordercode, 1);
	$fees		 = _getFees($this->ordercode); 
	
	## Prepare for Mollie.
	$this->amount = $amount*100;
	$this->fees = $fees;

	## Return URL after payment has been done.
	$this->report_url  = JURI::root().'index.php?option=com_ticketmaster&view=transaction&payment_type=processMollie';
	$this->return_url  = JURI::root().'index.php?option=com_ticketmaster&view=transaction&payment_type=mollie'; 
	
	
	
 }
 
/**
 * Plugin method with the same name as the event will be called automatically.
*/


	 function display()
	 {
		$app = JFactory::getApplication();
		$db  = JFactory::getDBO();

		## Loading the CSS file for ideal plugin.
		$document = &JFactory::getDocument();
		$document->addStyleSheet( JURI::root(true).'/plugins/rdmedia/rdmideal/rdmedia_mollie/css/ideal.css' );

		## Loading the configuration from the config table to check if bootstrap is on.
		$sql = 'SELECT load_bootstrap FROM #__ticketmaster_config WHERE configid = 1';
				
		$db->setQuery($sql);
		$config = $db->loadObject();

		
		## Check if partner id is given.
		if($this->partner_id == '2112') {
			
			## if not, please stop the script.
			echo JText::_( 'PLG_TICKETMSTER_MOLLIE_NO_PID' );
		
		}else{
			
			## Check if the server accepts SSL.
			if (!in_array('ssl', stream_get_transports())) {
				
				## Show error when not accepting SSL on server.
				echo '<h1>'. JText::_( 'PLG_TICKETMSTER_MOLLIE_NO_SSL' ).'</h1>';	
				
				return false;	
			}
			
			## Include the Mollie Ideal class to process payment. 
			$path_require = JPATH_SITE.DS.'plugins'.DS.'rdmedia'.DS.'rdmideal'.DS.'rdmedia_mollie'.DS.'ideal.class.php';
			require_once($path_require);
			
			## Starting class
			$iDEAL = new iDEAL_Payment($this->partner_id);
			
			
			## Check if sandbox is on.
			if ($this->sandbox_on == 1) {
				$iDEAL->setTestMode();			
			}	
			
		   ## Requesting banknumber from sent info.	
		   $bank_id =  JRequest::getVar('bank_id', 0);
		 
			## Check form submission.
			if ($bank_id != 0)  {
				
				$description = $this->description.$this->ordercode;
				
				## Create a mollie payment instantly.
				if ($iDEAL->createPayment($bank_id, $this->amount, $description, $this->return_url, $this->report_url)) {
				
					/* Hier kunt u de aangemaakte betaling opslaan in uw database, bijv. met het unieke transactie_id
					   Het transactie_id kunt u aanvragen door $iDEAL->getTransactionId() te gebruiken. Hierna wordt 
					   de consument automatisch doorgestuurd naar de gekozen bank. */
					   					   
						$db 	 =& JFactory::getDBO();

						## Now let's check if there is temporary payment with this information:
						## Doing the query to get the count of transactions for this one.
						$sql = 'SELECT * FROM #__ticketmaster_transactions_temp WHERE transaction_number = "'.$transaction_number.'" ';
						$db->setQuery($sql);
						$temp_transaction = $db->loadObjectList();
						
						## Count the transactions.	
						$transactions = count($temp_transaction);
						
						## If there are no transactions, insert now.
						if($transactions == 0) {	
							
							$user = & JFactory::getUser();					
										   
							## insert the data to this table for checks later on.
							$query = "INSERT INTO #__ticketmaster_transactions_temp 
									  (userid, transaction_number, ordercode, processed) 
									   VALUES ('".$user->id."', '".$iDEAL->getTransactionId()."', '".$this->ordercode."', 0)";								   			
							$db->setQuery($query);
							
							if (!$db->query() ){
								echo "<script>alert('Error: Please report this error to the webmaster.');
								window.history.go(-1);</script>\n";		 
							}
						}
						
					header("Location: " . $iDEAL->getBankURL());
					exit;	
				
				}else{
				
					/* Er is iets mis gegaan bij het aanmaken bij de betaling. U kunt meer informatie 
					   vinden over waarom het mis is gegaan door $iDEAL->getErrorMessage() en/of 
					   $iDEAL->getErrorCode() te gebruiken. */
					
					echo '<p>'.JText::_( 'PLG_TICKETMSTER_MOLLIE_ERROR' ).'</p>';
					echo '<p>'.$iDEAL->getErrorMessage().'</p>';
			
				}
			}
			
			## Create a list of banks.
			$bank_array = $iDEAL->getBanks();
			
			if ($bank_array == false) {
			
				## Show error - banklist couldn't be loaded.
				echo JText::_( 'PLG_TICKETMASTER_MOLLIE_NO_BANKLIST' ).$iDEAL->getErrorMessage();
			
			}	
			
			if ($this->amount != 0) {
				
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
                    
                    echo '<img src="plugins/rdmedia/rdmideal/rdmedia_mollie/images/plg-mollie-ideal.png" />';
                    
                    echo '<form action = "index.php" method="POST" name="adminForm" id="adminForm">';
					echo '<fieldset style="margin-top:15px;">';
                    
					echo	'<select name="bank_id" class="input">';
					
					echo 		'<option value="">'.JText::_( 'Kies uw bank AUB..' ).'</option>';
								
								 ## List the banks now in option from
								 foreach ($bank_array as $bank_id => $bank_name) { 
									echo '<option value=" '.$bank_id.' ">'.$bank_name.'</option>';
								 } 
					
					echo	'</select>';
                    
					echo    '<input type="hidden" name="option" value="com_ticketmaster" />';
					echo    '<input type="hidden" name="view" value="payment" />';	                 
				    echo    '<button class="btn btn-block btn-success" type="submit">'.JText::_( 'Nu Betalen' ).'</button>'; 
                    
                    echo '</fieldset>';
					echo '</form>';
                    						
				}else{
				
					## Form to show to client -- Show only when amount is not 0.00
					echo '<form action = "index.php" method="POST" name="adminForm" id="adminForm">';	
					echo '<div id="plg_rdmedia_ideal">';
		
					echo '<div id="plg_rdmedia_mollie_cards">';
						echo $this->infobox;
						
						echo	'<select name="bank_id" class="inputbox">';
						
						echo 		'<option value="">'.JText::_( 'Kies uw bank AUB..' ).'</option>';
									
									 ## List the banks now in option from
									 foreach ($bank_array as $bank_id => $bank_name) { 
										echo '<option value=" '.$bank_id.' ">'.$bank_name.'</option>';
									 } 
						
						echo	'</select>';				
						
					echo '</div>';			
			
					echo    '<input type="hidden" name="option" value="com_ticketmaster" />';
					echo    '<input type="hidden" name="view" value="payment" />';				
					
					echo '<div id="plg_rdmedia_ideal_confirmbutton">';
					echo	'<input type="submit" name="submit" class="ideal_button" value="" />';
					echo '</div>';				
		
					echo '</div>';	
					echo '</form>';					
					
				}
								
			}
		
		}
		
		return true;
	 }

	function mollie() {
		
		$trans_id =  JRequest::getVar('transaction_id');
		
		if($trans_id == ''){
		
			$error =  JText::_( 'PLG_TICKETMSTER_MOLLIE_NO_TRANSID' );
			self::showmsg($this->failure_tpl, $error);				
		
		}else{
		
			$db = JFactory::getDBO();
			
			## Getting the desired info from the configuration table
			$sql = "SELECT * FROM #__ticketmaster_transactions WHERE transid = '".$trans_id."'";
			$db->setQuery($sql);
			$transaction = $db->loadObject();
			
			## Getting the desired info from the configuration table
			$sql = "SELECT * FROM #__ticketmaster_transactions_temp WHERE transaction_number = '".$trans_id."'";
			$db->setQuery($sql);
			$temp_transaction = $db->loadObject();			
			
			if($temp_transaction->errorcode != 1) {

					switch ($temp_transaction->errorcode){
						case 0:
							$msg = JText::_( 'Er is geen bericht van de bank terug gekomen.' );
							break;
						case 2:
							$msg = JText::_( 'Uw betaling werd gecancelled.' );
							break;
						case 3:
							$msg = JText::_( 'Er is een fout opgetreden bij de betaling.' );
							break;	
						case 4:
							$msg = JText::_( 'Uw betaling is verlopen. (Waarschijnlijk heeft u te lang gewacht)' );
							break;	
						case 5:
							$msg = JText::_( 'Deze betaling is reeds gecontroleerd door de bank.' );
							break;
						case 6:
							$msg = JText::_( 'Er is geen bericht van de bank terug gekomen.' );
							break;				
					}
				
				self::showmsg($this->failure_tpl, $msg);
			
			}else{
				
				## Indien errorcode == 1, dan is de betaling success
				if($temp_transaction->errorcode == 1){

					## Removing the session, it's not needed anymore.
					$session =& JFactory::getSession();
					$session->clear('ordercode');				
	
					## Getting the desired info from the configuration table
					$sql = "SELECT * FROM #__ticketmaster_emails 
							WHERE emailid = ".(int)$this->success_tpl."";
					
					$db->setQuery($sql);
					$config = $db->loadObject();
	
					## Getting the desired info from the configuration table
					$sql = "SELECT * FROM #__users WHERE id = ".(int)$temp_transaction->userid."";
					
					$db->setQuery($sql);
					$user = $db->loadObject();	
					
					$amount  = _getAmount($this->ordercode, 1);			

					echo '<h1">'.$config->mailsubject.'</h1>';
					
					$message = str_replace('%%TID%%', $trans_id, $config->mailbody);
					$message = str_replace('%%OID%%', $temp_transaction->ordercode, $message);
					$message = str_replace('%%AMOUNT%%', number_format($amount, 2, ',', ' '), $message);
					$message = str_replace('%%DATE%%', $transaction->date, $message);
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
							$obj->addRecipient($sender);
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
					$session->clear('coupon');													

				}else{
				
					switch ($temp_transaction->errorcode){
						case 0:
							$msg = JText::_( 'Er is geen bericht van de bank terug gekomen.' );
							break;
						case 2:
							$msg = JText::_( 'Uw betaling werd gecancelled.' );
							break;
						case 3:
							$msg = JText::_( 'Er is een fout opgetreden bij de betaling.' );
							break;	
						case 4:
							$msg = JText::_( 'Uw betaling is verlopen.(Waarschijnlijk heeft u te lang gewacht)' );
							break;	
						case 5:
							$msg = JText::_( 'Deze betaling is reeds gecontroleerd door de bank.' );
							break;
						case 6:
							$msg = JText::_( 'Er is geen bericht van de bank terug gekomen.' );
							break;				
					}
					
					self::showmsg($this->failure_tpl, $msg);						
				
				}						
				
			}
		
		}
	
	}	

	function showmsg($msgid, $msg){
		
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
		$session->clear('coupon');	
		
		return false;												
				
	}	
	
	function developermessage( $message ){
		
		$from 		 = 'report-'.$this->developer_email;
		$fromname 	 = 'report-'.$this->developer_email;
		$recipient[] = $this->developer_email;
		$subject 	 = 'Dev Report Mollie Plugin Ticketmaster';
		$body 	 	 = $message;
		$mode 		 = 1; 
		
		$replyto 	 = $this->developer_email;
		$replytoname = $this->developer_email;	
			
		JUtility::sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);													
				
	}		 
	 
	 function processMollie() {
		
		## Include the Mollie Ideal class to process payment. 
		$path_include = JPATH_SITE.DS.'plugins'.DS.'rdmedia'.DS.'rdmideal'.DS.'rdmedia_mollie'.DS.'ideal.class.php';	
		require_once($path_include);
		
		## Including required paths to calculator.
		$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
		include_once( $path_include );		
		
		## Starting class
		$iDEAL = new iDEAL_Payment($this->partner_id);
		
		## Check if sandbox is on.
		if ($this->sandbox_on == 1) {
			$iDEAL->setTestMode();			
		}	
				
		## Let's check if the system has a payment_type
		$trans_id =  JRequest::getVar('transaction_id');		
		
		## Check ordercode and transaction_id
		if ($trans_id == ''){
			
			if ($this->developermode == 1){
				$message = 'Mollie heeft <strong>GEEN</strong> transactie id terug gezonden. 
							<br/> Script afegbroken bij regel: 368';
				self::developermessage( $message );
			}			
			
			exit(JText::_( 'COM_TICKETMSTER_MOLLIE_NO_TRANSID' ));
					
		}	
				
		## Double check the transaction
		$iDEAL->checkPayment($trans_id);
		
		$bankstatus = $iDEAL->getBankStatus();
		
		switch ($bankstatus){
			case "Success":
				$status = '1';
				break;
			case "Cancelled":
				$status = '2';
				break;	
			case "Failure":
				$status = '3';
				break;	
			case "Expired":
				$status = '4';
				break;
			case "CheckedBefore":
				$status = '5';
				break;	
			default:
				$status = '6';
				$bankstatus = 'NoStatusReturned';
				break;				
		}		
		
		if ($status != 5){
		
			if ($this->developermode == 1){
				$message = 'Mollie heeft de volgende betaalstatus terug gegeven: <strong>'.$bankstatus.' || '.$status.'</strong>';
				self::developermessage( $message );
			}
			
			$db = JFactory::getDBO();		
		
			## Updating the temporary table for payments.
			$query = 'UPDATE #__ticketmaster_transactions_temp 
					  SET errorcode = "'.$status.'", errormessage = "'.$message.'"
					  WHERE transaction_number = "'.$trans_id.'" ';
			
			## Do the query now	
			$db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$db->query()) {
				$this->setError($db->getErrorMsg());
				return false;
			}				
	
			if ($this->developermode == 1){
				$message = 'De volgende query heeft gedraaid:<br/><strong>'.$query.'</strong>';
				self::developermessage( $message );
			}
		
		}
		## Including the table transaction to store it.
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tables');
		$row =& JTable::getInstance('transaction', 'Table');		
				
		if ($iDEAL->getPaidStatus() == true) 
		{
			## Doing the query to get the count of transactions for this one.
			$sql = 'SELECT * FROM #__ticketmaster_transactions_temp WHERE transaction_number = "'.$trans_id.'" ';
			$db->setQuery($sql);
			$transaction = $db->loadObject();
			
			## Id there are more transactions.
			if ($transaction->processed == 1) {
				exit(JText::_( 'COM_TICKETMSTER_MOLLIE_PROCESSED' ));	
			}
			
			## Getting the order amount.
			$orderamount = _getAmount($transaction->ordercode, 1);
			$return_amount = number_format($orderamount, 2, '.', '');
			$total = $return_amount*100;

			## Making a string instead of array.	
			$data = $iDEAL->getConsumerInfo();
			$customerdata = http_build_query($data);
			
			## As Mollie is using cents only!! 
			## We need to convert the amount to EUROS again.
			$amount = $iDEAL->getAmount();
			$paid_amount = $amount/100;
				
			if ( $total != $amount ) {
				
				exit(JText::_( 'COM_TICKETMSTER_MOLLIE_FAULT_AMOUNT' ));			
				
			}else{
				
				## Getting the desired info from the configuration table
				$sql = "SELECT * FROM #__users WHERE id = ".(int)$transaction->userid."";
				
				$db->setQuery($sql);
				$user = $db->loadObject();					
				
				## Prepare the row.
				$row->transid = $trans_id;
				$row->userid = $transaction->userid.'/'.$transaction->ordercode;
				$row->details = $customerdata;
				$row->amount = $paid_amount;
				$row->type = 'Mollie BV';
				$row->orderid = (int)$transaction->ordercode;
				
				## Store data
				$row->store();

				$query = 'UPDATE #__ticketmaster_orders SET paid = 1, published = 1
						  WHERE ordercode = "'.(int)$transaction->ordercode.'" ';						
				
				## Doing the Query
				$db->setQuery( $query );
				
				## When query goes wrong.. Show message with error.
				if (!$db->query()) {
					$this->setError($db->getErrorMsg());
					return false;
				}

				if ($this->developermode == 1){
					$message = 'De volgende query (477) heeft gedraaid:<br/><strong>'.$query.'</strong>';
					self::developermessage( $message );
				}

				$query = 'UPDATE #__ticketmaster_transactions_temp SET processed = 1
						  WHERE transaction_number = "'.$trans_id.'" ';
				
				## Do the query now	
				$db->setQuery( $query );
				
				## When query goes wrong.. Show message with error.
				if (!$db->query()) {
					$this->setError($db->getErrorMsg());
					return false;
				}	
				
				if ($this->developermode == 1){
					$message = 'De volgende query (494) heeft gedraaid:<br/><strong>'.$query.'</strong>';
					self::developermessage( $message );
				}							
				
				$query = 'SELECT * FROM #__ticketmaster_orders WHERE ordercode = '.(int)$transaction->ordercode.'';

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
				
				if ($this->confirmation == 1) {
				
					$path_include = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'confirmation.php';
					include_once( $path_include );
					
					$sendconfirmation = new confirmation( (int)$transaction->ordercode );  
					$sendconfirmation->doConfirm();
					$sendconfirmation->doSend();

				
				}

				## Include the confirmation class to sent the tickets. 
				$path_include = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'sendonpayment.class.php';
				include_once( $path_include );
				
				## Sending the ticket immediatly to the client.
				$creator = new sendonpayment( (int)$transaction->ordercode );  
				$creator->send();	

				## Removing the session, it's not needed anymore.
				$session =& JFactory::getSession();
				$session->clear('ordercode');
				$session->clear('coupon');									
									
			}
		
		}else{

			$error =  JText::_( 'COM_TICKETMSTER_MOLLIE_IMPOSSIBLE' );
			self::showmsg($this->failure_tpl, $error);
			
				## Removing the session, it's not needed anymore.
				$session =& JFactory::getSession();
				$session->clear('ordercode');	
				$session->clear('coupon');			
		
		}  
	}
}
?>
