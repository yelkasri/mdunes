<?php
## no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
## Import library dependencies
jimport('joomla.plugin.plugin');
 
class plgRDmediaPaybycheck extends JPlugin
{
/**
 * Constructor
 *
 * For php4 compatability we must not use the __constructor as a constructor for
 * plugins because func_get_args ( void ) returns a copy of all passed arguments
 * NOT references.  This causes problems with cross-referencing necessary for the
 * observer design pattern.
 */
	function plgRDMediaPaybycheck( &$subject, $params ) 
	{
	
		parent::__construct( $subject , $params );
		
		## Loading language:	
		$lang = JFactory::getLanguage();
		$lang->load('plg_rdmedia_paybycheck', JPATH_ADMINISTRATOR);		
		
		## load plugin params info
		$plugin = JPluginHelper::getPlugin('rdmedia', 'paybycheck');

		$this->infobox = $this->params->def( 'infobox', '0' );
		$this->email_tpl = $this->params->def( 'email_tpl', '1' );
		$this->success_tpl = $this->params->def( 'success_tpl', '1' );	
		$this->success_msg = $this->params->def( 'success_msg', '100' );
		$this->send_confirmation = $this->params->def( 'send_confirmation', '0' );
		$this->layout = $this->params->def( 'layout', '0' );
		
		## Including required paths to calculator.
		$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
		include_once( $path_include );
		
		## Getting the global DB session
		$session = JFactory::getSession();
		## Gettig the orderid if there is one.
		$this->ordercode = $session->get('ordercode');
		
		## Getting the amounts for this order.
		$amount      = _getAmount($this->ordercode, 1);
		$fees		 = _getFees($this->ordercode); 
		
		## Prepare for Mollie.
		$this->amount = $amount*100;
		$this->fees = $fees;
		
		## Return URL after payment has been done.
		$this->report_url  = 'index.php?option=com_ticketmaster&view=transaction&payment_type=paybycheck';
	
	}

	 function display()
	 {
		$app = &JFactory::getApplication();

		## Loading the CSS file for ideal plugin.
		$document = &JFactory::getDocument();
		$document->addStyleSheet( JURI::root(true).'/plugins/rdmedia/paybycheck/rdmedia_paybycheck/css/paybycheck.css' );	
		
		## Making sure PayPal getting the right amount (23.00)
		$ordertotal = number_format($this->amount, 2, '.', '');	
		
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

			if($this->layout == 1 && $isJ30 == true ){	
			
				## Form to show to client.
				//\\ROBERT-SERVER\Shared\ticketmaster300\plugins\rdmedia\paybycheck\rdmedia_paybycheck\images
				echo '<img src="plugins/rdmedia/paybycheck/rdmedia_paybycheck/images/wired-transfer.png" />';
				echo '<form action = "'.$this->report_url.'" method="POST" name="adminForm" id="adminForm">';	
				echo '<button class="btn btn-block btn-success" style="margin-top:10px;" type="submit">'.JText::_( 'PLG_RDMEDIA_PBC_PAY_BY_CHECK' ).'</button>'; 	
				echo '</form>';				
			
			}else{
		
				## Form to show to client.
				echo '<form action = "'.$this->report_url.'" method="POST" name="adminForm" id="adminForm">';	
				
				echo 	'<div id="plg_rdmedia_paybycheck">';	
				
				echo '<div id="plg_rdmedia_paybycheck_cards">';
				echo 	$this->infobox;
				echo '</div>';						
				
				echo 	'<div id="plg_rdmedia_ideal_confirmbutton">';
				echo		'<input type="submit" name="submit" class="paybycheck_button" value="" />';
				echo 	'</div>';				
		
				echo 	'</div>';
					
				echo '</form>';	
				
			}
		
		}
		
		return true;
	}
	
	function paybycheck()
	{

		## Getting the global DB session
		$session =& JFactory::getSession();
		## Gettig the orderid if there is one.
		$ordercode = $session->get('ordercode');
		
		## Include the confirmation class to sent the tickets. 
		$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'createtickets.class.php';
		$override = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'override'.DS.'createtickets.class.php';	
		

		$db = JFactory::getDBO();
		$user =& JFactory::getUser();
		
		$sql = "SELECT * FROM #__users WHERE id = ".(int)$user->id."";
		$db->setQuery($sql);
		$user = $db->loadObject();		

		
		## Getting the desired info from the configuration table
		$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$this->success_msg."";
		$db->setQuery($sql);
		$config = $db->loadObject();
		
		## Making the query for showing all the cars in list function
		$sql = 'SELECT priceformat, valuta, dateformat FROM #__ticketmaster_config WHERE configid = 1';
	 
		$db->setQuery($sql);
		$configuration = $db->loadObject();		
		
		## Getting the amount of tickets.
		$query = 'SELECT COUNT(orderid) AS tickets FROM #__ticketmaster_orders WHERE ordercode = '.(int)$ordercode.'';
		$db->setQuery($query);
		$t = $db->loadObject();	
		
		##### SHOWING A MESSAGE IN SCREEN ######

		## Including required paths to calculator.
		$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'functions.php';
		include_once( $path_include );	
		$price = $this->amount/100;
		
		$amount =  showprice($configuration->priceformat, $price, $configuration->valuta);	
		
		## Getting the date	
		$today = date($configuration->dateformat);  
		
		echo '<h1>'.$config->mailsubject.'</h1>';
		
		$message = str_replace('%%ORDERCODE%%', $ordercode, $config->mailbody);
		$message = str_replace('%%PRICE%%', $amount, $message);
		$message = str_replace('%%ORDERDATE%%', $today, $message);
		$message = str_replace('%%TICKETS%%', $t->tickets, $message);
		$message = str_replace('%%EMAIL%%', $user->email, $message);
		$message = str_replace('%%NAME%%', $user->name, $message);
		
		## encode the link;
		$encoded = base64_encode('payfororder='.$ordercode);
		$paymentlink = JURI::root().'index.php?option=com_ticketmaster&controller=validate&task=pay&order='.$encoded;	
		$message     = str_replace('%%PAYMENTLINK%%', $paymentlink, $message);	
		
		echo $message;
		
		if($this->send_confirmation == 0){ 
		
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
		
		}else{
			
		
			$path_include = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'confirmation.php';
			include_once( $path_include );
			
			$sendconfirmation = new confirmation( (int)$ordercode );  
			$sendconfirmation->doConfirm();
			$sendconfirmation->doSend();
			
			
		}

		$query = 'SELECT * FROM #__ticketmaster_orders WHERE ordercode = '.(int)$ordercode.'';

		## Do the query now	
		$db->setQuery($query);
		$data = $db->loadObjectList();
		
		$k = 0;
		for ($i = 0, $n = count($data); $i < $n; $i++ ){
			
			$row  = &$data[$i];
		
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

		$query = 'UPDATE #__ticketmaster_orders'
			. ' SET paid = 3, published = 1'
			. ' WHERE ordercode = '.(int)$ordercode.'';
		
		## Do the query now	
		$db->setQuery( $query );
		
		## When query goes wrong.. Show message with error.
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		## Ticket have been created now -- let's send them to the customer.
		## Include the confirmation class to sent the tickets.
		//$path_include = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'sendonpayment.class.php';
		//include_once( $path_include );
			
		## Sending the ticket immediatly to the client.
		//$creator = new sendonpayment( (int)$ordercode );
		//$creator->send();		


		## Removing the session, it's not needed anymore.
		$session =& JFactory::getSession();
		$session->clear('ordercode');	
		$session->clear('coupon');	
	
	}

}	
?>
