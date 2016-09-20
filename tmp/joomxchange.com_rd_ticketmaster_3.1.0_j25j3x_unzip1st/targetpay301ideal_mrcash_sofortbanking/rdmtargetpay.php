<?php
## no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
## Import library dependencies
jimport('joomla.plugin.plugin');
 
class plgRDmediaRDMTargetPay extends JPlugin
{
/**
 * Constructor
 *
 * For php4 compatability we must not use the __constructor as a constructor for
 * plugins because func_get_args ( void ) returns a copy of all passed arguments
 * NOT references.  This causes problems with cross-referencing necessary for the
 * observer design pattern.
 */
 function plgRDMediaRDMTargetPay( &$subject, $params  ) {
 
    parent::__construct( $subject , $params  );
	
	## Loading language:	
	$lang = JFactory::getLanguage();
	$lang->load('plg_rdmedia_rdmtargetpay', JPATH_ADMINISTRATOR);	

	## load plugin params info
 	$plugin =& JPluginHelper::getPlugin('rdmedia', 'rdmpaypal');

	$this->rtlo 				= $this->params->def( 'rtlo', 1234 );
	$this->currency 			= $this->params->def( 'currency', 'EUR' );
	$this->description  		= $this->params->def( 'description', 'Test Omschrijving' );
	$this->language				= $this->params->def( 'language', 'nl' );
	$this->sandbox				= $this->params->def( 'sandbox', 1 );
	$this->layout 				= $this->params->def( 'layout', 0 );	
	
	$this->show_ideal			= $this->params->def( 'show_ideal', 1 );
	$this->success_tpl_ideal  	= $this->params->def( 'success_tpl_ideal', 1 );
	$this->failure_tpl_ideal  	= $this->params->def( 'failure_tpl_ideal', 1 );	
	
	$this->show_sofort			= $this->params->def( 'show_sofort', 1234 );
	$this->failure_tpl_sofort  	= $this->params->def( 'failure_tpl_sofort', 1 );
	$this->success_tpl_sofort  	= $this->params->def( 'success_tpl_sofort', 1 );
	
	$this->show_mr_cash			= $this->params->def( 'show_mr_cash', 1234 );
	$this->failure_tpl_mr_cash 	= $this->params->def( 'failure_tpl_mr_cash', 1 );
	$this->success_tpl_mr_cash 	= $this->params->def( 'success_tpl_mr_cash', 1 );	
	
	## Getting the global DB session
	$session =& JFactory::getSession();
	## Gettig the orderid if there is one.
	$this->ordercode = $session->get('ordercode');
	
	## Including required paths to calculator.
	$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
	include_once( $path_include );
	
	## Getting the global DB session
	$session 	= JFactory::getSession();
	$ordercode 	= $session->get('ordercode');
	
	## Getting the amounts for this order.
	$amount      = _getAmount($this->ordercode, 1);
	$fees		 = _getFees($this->ordercode); 
	
	## Prepare for Mollie.
	$this->amount = $amount*100;
	$this->fees = $fees;	

	## Return URLS to your website after processing the order.
	$this->reporturl  = JURI::root().'index.php?option=com_ticketmaster&view=transaction&payment_type=targetpayprocess';
	$this->returnSofort = JURI::root().'preparse.php?token=option-com_ticketmaster,view-transaction,payment_type-targetpaySofortreturn';
	$this->returnMrCash = JURI::root().'preparse.php?token=option-com_ticketmaster,view-transaction,payment_type-targetpayMrCashreturn';
	$this->returnIDeal = JURI::root().'preparse.php?token=option-com_ticketmaster,view-transaction,payment_type-targetpayIDealreturn';

 }
 
/**
 * Plugin method with the same name as the event will be called automatically.
 * You have to get at least a function called display, and the name of the processor (in this case paypal)
 * Now you should be able to display and process transactions.
 * 
*/

	 function display()
	 {
		$app = JFactory::getApplication();
		
		$document = JFactory::getDocument();
		$document->addStyleSheet( JURI::root(true).'/plugins/rdmedia/rdmtargetpay/rdmedia_targetpay/css/targetpay.css' );		
		
		## Getting the Bank Number for redirect.
		$bank     =  JRequest::getVar('bank', 0);
		$language =  JRequest::getVar('language', 0);
		$type     =  JRequest::getVar('targetpay_type', 0);
		
		$fault = '<strong><br/>The file preparse.php file does not exist in your website root! (Needed for TargetPay Plugins!)<br/>';
		$link  = '<a href="http://www.rd-media.org/support/knowledgebase/view-article/53-configure-targetpay-sofort-ideal-plugin.html" target="_blank">
				  <strong>Payments will not be processed, read the knowlegdebase now :)</strong></a></strong>';
		
		if (!file_exists(JPATH_SITE.DS.'preparse.php')) {
			echo $fault.$link;
		}		
		
		if ($bank != 0) {
			## OK - A bank has been selected, lets call the fuction to rediect to the bank.
			self::targetpayideal($bank);
		}
		
		if ($language > 30) {
			## OK - A bank has been selected, lets call the fuction to rediect to the bank.
			self::targetpaysofort($language);
		}
		
		if ($type == 50) {
			## OK - A bank has been selected, lets call the fuction to rediect to the bank.
			self::targetpaymrcash('NL');
		}					
		
		?>
		
        <?php ## Check if the layout cde has been entered:
		if ($this->rtlo != 1234) { 
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
			
			?>
				
				<?php if ($this->show_mr_cash == 1) {
    
                    ## Form to show to client -- Show only when amount is not 0.00
					echo '<img src="plugins/rdmedia/rdmtargetpay/rdmedia_targetpay/images/mr-cash-vertical.png" />';
					
                    echo '<form action = "index.php" method="POST" name="adminForm" id="adminForm">';
					echo '<fieldset style="margin-top:15px;">';		
                    
					echo    '<input type="hidden" name="option" value="com_ticketmaster" />';
					echo    '<input type="hidden" name="view" value="payment" />';	
					echo    '<input type="hidden" name="targetpay_type" value="50" />';				
                            
					echo    '<button class="btn btn-block btn-success" type="submit">'.JText::_( 'PLG_TARGETPAY_PAY_BY_MRCASH' ).'</button>'; 
                    
                    echo '</fieldset>';	
                    echo '</form>';	 
    
               } ?>
            
               <?php if ($this->show_sofort == 1) {
    
                    ## Form to show to client -- Show only when amount is not 0.00
					echo '<img src="plugins/rdmedia/rdmtargetpay/rdmedia_targetpay/images/sofort-banking-vertical.png" />';
					
                    echo '<form action = "index.php" method="POST" name="adminForm" id="adminForm">';
					echo '<fieldset style="margin-top:15px;">';
                        
                        echo '<select name="language" id="language" class="input">';
                        echo '<option value="49">Germany (DE)</option>';
                        echo '<option value="43">Austria (AT)</option>';
                        echo '<option value="41">Zwitserland (CH)</option>';
                        echo '<option value="32">Belgium (BE)</option>';
                        echo '</select>  ';					
                        		
            
                    echo    '<input type="hidden" name="option" value="com_ticketmaster" />';
                    echo    '<input type="hidden" name="view" value="payment" />';
				    
					echo    '<button class="btn btn-block btn-success" type="submit">'.JText::_( 'PLG_TARGETPAY_PAY_BY_SOFORT' ).'</button>'; 
                    
                    echo '</fieldset>';	
                    echo '</form>';	                                
    
               } ?>
        
      
                  <?php if ($this->show_ideal == 1) {       
                	
                    echo '<img src="plugins/rdmedia/rdmtargetpay/rdmedia_targetpay/images/plg-targetpay-vertical-ideal.png" />';
                    
                    echo '<form action = "index.php" method="POST" name="adminForm" id="adminForm">';
					echo '<fieldset style="margin-top:15px;">';
                        
                    echo '<select name="bank" id="bank" class="input">
							<script src="http://www.targetpay.com/ideal/issuers-nl.js"></script>
						  </select>';						
            
                    echo  '<input type="hidden" name="option" value="com_ticketmaster" />';
                    echo  '<input type="hidden" name="view" value="payment" />';				
                    
				    echo    '<button class="btn btn-block btn-success" type="submit">'.JText::_( 'PLG_TARGETPAY_PAY_BY_IDEAL' ).'</button>'; 
                    
                    echo '</fieldset>';	
                    echo '</form>';	
    
                  } 
				  		
               }else{
					
				  
				  if ($this->show_mr_cash == 1) {
    
                    ## Form to show to client -- Show only when amount is not 0.00
                    echo '<form action = "index.php" method="POST" name="adminForm" id="adminForm">';	
                    
                        echo '<div id="plg_rdmedia_mr_cash">';
            
                            echo '<div id="plg_rdmedia_mollie_cards">&nbsp;';
                            echo '</div>';			
                    
                            echo    '<input type="hidden" name="option" value="com_ticketmaster" />';
                            echo    '<input type="hidden" name="view" value="payment" />';	
                            echo    '<input type="hidden" name="targetpay_type" value="50" />';				
                            
                            echo '<div id="plg_rdmedia_mr_cash_confirmbutton">';
                            echo	'<input type="submit" name="submit" class="ideal_button" value="" />';
                            echo '</div>';				
            
                        echo '</div>';
                        
                    echo '</form>';	
    
               } 
            
               if ($this->show_sofort == 1) {
    
                    ## Form to show to client -- Show only when amount is not 0.00
                    echo '<form action = "index.php" method="POST" name="adminForm" id="adminForm">';	
                    echo '<div id="plg_rdmedia_sofort">';
        
                    echo '<div id="plg_rdmedia_mollie_cards">';
                        echo $this->infobox;
                        
                        echo '<select name="language" id="language" class="input-medium">';
                        echo '<option value="49">Germany (DE)</option>';
                        echo '<option value="43">Austria (AT)</option>';
                        echo '<option value="41">Zwitserland (CH)</option>';
                        echo '<option value="32">Belgium (BE)</option>';
                        echo '</select>  ';					
                        
                    echo '</div>';			
            
                    echo    '<input type="hidden" name="option" value="com_ticketmaster" />';
                    echo    '<input type="hidden" name="view" value="payment" />';
                                
                    
                    echo '<div id="plg_rdmedia_ideal_confirmbutton">';
                    echo	'<input type="submit" name="submit" class="ideal_button" value="" />';
                    echo '</div>';				
        
                    echo '</div>';	
                    echo '</form>';	
    
                 } 
        
      
                 if ($this->show_ideal == 1) {       
                
                    ## Form to show to client -- Show only when amount is not 0.00
                    echo '<form action = "index.php" method="POST" name="adminForm" id="adminForm">';	
                    echo '<div id="plg_rdmedia_ideal">';
        
                    echo '<div id="plg_rdmedia_mollie_cards">';
                        echo $this->infobox;
                        
                        echo '<select name="bank" id="bank" class="input-medium"><script src="http://www.targetpay.com/ideal/issuers-nl.js"></script></select>';				
                        
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
		} 		
		
		return true;
	 }

	 function targetpaymrcash($language) {
		 
		$app = JFactory::getApplication();
		
		## Prepare the order total (only cents!!)
		$iAmount = $this->amount;
		
		## Include the classes for TargetPay		
		$path_include = JPATH_SITE.DS.'plugins'.DS.'rdmedia'.DS.'rdmtargetpay'.DS.'rdmedia_targetpay'.DS.'TargetPayMrCash.class.php';
		include_once( $path_include );			
		
		$db   = JFactory::getDBO();				
		
		## To initiate a payment, initiate the class
		$oIdeal = new TargetPayMrCash ( $this->rtlo );		
		## Set ideal amount in cents so 500 cent will be 5 euro
		$oIdeal->setMrCashAmount ( $iAmount );
		## Set the visitors IP Address:
		$oIdeal->setMrCashIp ($_SERVER['REMOTE_ADDR']);	
		## Set ideal description
		$oIdeal->setMrCashDescription ( $this->description.' '.$this->ordercode);				
		## Set return url, wich should return on succes
		$oIdeal->setMrCashReturnUrl ( $this->returnMrCash );
		## Set report url 
		$oIdeal->setMrCashReportUrl ( $this->reporturl );
		
		## Now we can initiate the payment
		$aReturn = $oIdeal->startPayment();		

		## This is the transaction id
		$intTrxId = $aReturn[0];
		## this will be the bank url that will rederect to the bank.
		$strBankURL = $aReturn[1];

		$db = JFactory::getDBO();

		## Now let's check if there is temporary payment with this information:
		$db = JFactory::getDBO();
		## Doing the query to get the count of transactions for this one.
		$sql = 'SELECT * FROM #__ticketmaster_transactions_temp WHERE transaction_number = "'.$intTrxId.'" ';
		$db->setQuery($sql);
		$temp_transaction = $db->loadObjectList();
		
		## Count the transactions.	
		$transactions = count($temp_transaction);
		
		## If there are no transactions, insert now.
		if($transactions == 0) {	
			
			$user =  JFactory::getUser();					
						   
			## insert the data to this table for checks later on.
			$query = "INSERT INTO #__ticketmaster_transactions_temp 
					  (userid, transaction_number, ordercode, processed) 
					   VALUES ('".$user->id."', '".$intTrxId."', '".$this->ordercode."', 0)";								   			
			$db->setQuery($query);
			
			if (!$db->query() ){
				echo "<script>alert('Error: Please report this error to the webmaster.');
				window.history.go(-1);</script>\n";		 
			}
		}	

		## This haader function will redirect the browser to the bank

		header( "Location: ". $strBankURL );		

		
		return true;
	}


	 function targetpaysofort($language) {
		 
		$app = JFactory::getApplication();
		
		## Prepare the order total (only cents!!)
		$iAmount = $this->amount;
		
		## Include the classes for TargetPay		
		$path_include = JPATH_SITE.DS.'plugins'.DS.'rdmedia'.DS.'rdmtargetpay'.DS.'rdmedia_targetpay'.DS.'TargetPayDIRECTebanking.class.php';
		include_once( $path_include );			
		
		$db   = JFactory::getDBO();				
		
		## To initiate a payment, initiate the class
		$oIdeal = new TargetPayDIRECTebanking ( $this->rtlo );		
		## Set ideal amount in cents so 500 cent will be 5 euro
		$oIdeal->setDIRECTebankingAmount ( $iAmount );
		## Set the visitors IP Address:
		$oIdeal->setDIRECTebankingIp ($_SERVER['REMOTE_ADDR']);	
		## Set ideal description
		$oIdeal->setDIRECTebankingDescription ( $this->description.' '.$this->ordercode);				
		## Set return url, wich should return on succes
		$oIdeal->setDIRECTebankingReturnUrl ( $this->returnSofort );
		## Set report url 
		$oIdeal->setDIRECTebankingReportUrl ( $this->reporturl );
		## Set the countrycode
		$oIdeal->setDIRECTebankingCountry ( $language );
		## Set the type of goods to sell:
		$oIdeal->setDIRECTebankingType ( 2 );
		
		## Now we can initiate the payment
		$aReturn = $oIdeal->startPayment();		

		## This is the transaction id
		$intTrxId = $aReturn[0];
		## this will be the bank url that will rederect to the bank.
		$strBankURL = $aReturn[1];

		$db = JFactory::getDBO();

		## Now let's check if there is temporary payment with this information:
		$db = JFactory::getDBO();
		## Doing the query to get the count of transactions for this one.
		$sql = 'SELECT * FROM #__ticketmaster_transactions_temp WHERE transaction_number = "'.$intTrxId.'" ';
		$db->setQuery($sql);
		$temp_transaction = $db->loadObjectList();
		
		## Count the transactions.	
		$transactions = count($temp_transaction);
		
		## If there are no transactions, insert now.
		if($transactions == 0) {	
			
			$user =  JFactory::getUser();					
						   
			## insert the data to this table for checks later on.
			$query = "INSERT INTO #__ticketmaster_transactions_temp 
					  (userid, transaction_number, ordercode, processed) 
					   VALUES ('".$user->id."', '".$intTrxId."', '".$this->ordercode."', 0)";								   			
			$db->setQuery($query);
			
			if (!$db->query() ){
				echo "<script>alert('Error: Please report this error to the webmaster.');
				window.history.go(-1);</script>\n";		 
			}
		}	

		## This haader function will redirect the browser to the bank

		header( "Location: ". $strBankURL );		

		
		return true;
	}
	 
	 function targetpayideal($iIssuer) {
		 
		$app = JFactory::getApplication();
		
		## Prepare the order total (only cents!!)
		$iAmount = $this->amount; 
		
		## Include the classes for TargetPay		
		$path_include = JPATH_SITE.DS.'plugins'.DS.'rdmedia'.DS.'rdmtargetpay'.DS.'rdmedia_targetpay'.DS.'TargetPayIdeal.class.php';
		include_once( $path_include );			
		
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();					
		
		## To initiate a payment, initiate the class
		$oIdeal = new TargetPayIdeal ( $this->rtlo );		
		## Set ideal amount in cents so 500 cent will be 5 euro
		$oIdeal->setIdealAmount ( $iAmount );	
		## Set ideal issuer
		$oIdeal->setIdealissuer ( $iIssuer );	
		## Set ideal description
		$oIdeal->setIdealDescription ( $this->description.' '.$this->ordercode);				
		## Set return url, wich should return on succes
		$oIdeal->setIdealReturnUrl ( $this->returnIDeal );
		## Set report url 
		$oIdeal->setIdealReportUrl ( $this->reporturl );
		
		## Now we can initiate the payment
		$aReturn = $oIdeal->startPayment();		

		## This is the transaction id
		$intTrxId = $aReturn[0];
		## this will be the bank url that will rederect to the bank.
		$strBankURL = $aReturn[1];
	   
		$db = JFactory::getDBO();

		## Now let's check if there is temporary payment with this information:
		$db = JFactory::getDBO();
		## Doing the query to get the count of transactions for this one.
		$sql = 'SELECT * FROM #__ticketmaster_transactions_temp WHERE transaction_number = "'.$intTrxId.'" ';
		$db->setQuery($sql);
		$temp_transaction = $db->loadObjectList();
		
		## Count the transactions.	
		$transactions = count($temp_transaction);
		
		## If there are no transactions, insert now.
		if($transactions == 0) {	
			
			$user =  JFactory::getUser();					
						   
			## insert the data to this table for checks later on.
			$query = "INSERT INTO #__ticketmaster_transactions_temp 
					  (userid, transaction_number, ordercode, processed) 
					   VALUES ('".$user->id."', '".$intTrxId."', '".$this->ordercode."', 0)";								   			
			$db->setQuery($query);
			
			if (!$db->query() ){
				echo "<script>alert('Error: Please report this error to the webmaster.');
				window.history.go(-1);</script>\n";		 
			}
		}	

		## This haader function will redirect the browser to the bank

		header( "Location: ". $strBankURL );		

		
		return true;
	}
	
	###################################################################
	##############										 ##############
	##############	IDEAL PROCESSOR - DONT TOUCH PLEASE  ##############
	##############										 ##############
	###################################################################
	
	
	function targetpayIDealreturn(){

		if ( isset ( $_GET['ec'] ) && isset ( $_GET['trxid'] ) ) {

			## Include the classes for TargetPay		
			$path_include = JPATH_SITE.DS.'plugins'.DS.'rdmedia'.DS.'rdmtargetpay'.DS.'rdmedia_targetpay'.DS.'TargetPayIdeal.class.php';
			include_once( $path_include );			

			# Initiate the class
			$oIdeal = new TargetPayIdeal ( $this->rtlo );
		
			if ( $oIdeal->validatePayment ( $_GET['trxid'], 0, $this->sandbox  ) == true ) {
				
				$db = JFactory::getDBO();
				
				## Getting the desired info from the configuration table
				$sql = "SELECT * FROM #__ticketmaster_transactions_temp WHERE transaction_number = '".$_GET['trxid']."'";
				$db->setQuery($sql);
				$temp_transaction = $db->loadObject();
				
				## Removing the session, it's not needed anymore.
				$session =& JFactory::getSession();
				$session->clear('ordercode');				

				## Getting the desired info from the configuration table
				$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$this->success_tpl_ideal."";
				
				$db->setQuery($sql);
				$config = $db->loadObject();

				## Getting the desired info from the configuration table
				$sql = "SELECT * FROM #__users WHERE id = ".(int)$temp_transaction->userid."";
				
				$db->setQuery($sql);
				$user = $db->loadObject();	
				
				## Including required paths to calculator.
				$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
				include_once( $path_include );					

				$amount  = _getAmount($temp_transaction->ordercode, 1);	
				
				####### LET'S O THE LOGICAL NOW ########
				
					## Including the table transaction to store it.
					JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tables');
					$row =& JTable::getInstance('transaction', 'Table');	
					
					## Prepare the row.
					$row->transid = $_GET['trxid'];
					$row->userid = (int)$temp_transaction->userid.'/'.$temp_transaction->ordercode;
					$row->details = 'No Customer Data Present';
					$row->amount = $amount;
					$row->type = 'TargetPay IDEAL';
					$row->orderid = (int)$temp_transaction->ordercode;
					
					## Store data
					$row->store();	
					
					## Updating the order status in the orders table.
					$query = 'UPDATE #__ticketmaster_orders SET paid = 1, published = 1
							  WHERE ordercode = "'.(int)$temp_transaction->ordercode.'" ';						
					
					## Doing the Query
					$db->setQuery( $query );
					
					## When query goes wrong.. Show message with error.
					if (!$db->query()) {
						$this->setError($db->getErrorMsg());
						return false;
					}	
					
					## Updating the temporary transaction status.
					$query = 'UPDATE #__ticketmaster_transactions_temp SET processed = 1
							  WHERE transaction_number = "'.$_GET['trxid'].'" ';
					
					## Do the query now	
					$db->setQuery( $query );
					
					## When query goes wrong.. Show message with error.
					if (!$db->query()) {
						$this->setError($db->getErrorMsg());
						return false;
					}	
					
					## Now get the orders to create the tickets.
					$query = 'SELECT * FROM #__ticketmaster_orders WHERE ordercode = '.(int)$temp_transaction->ordercode.'';
	
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
						
						$sendconfirmation = new confirmation( (int)$temp_transaction->ordercode );  
						$sendconfirmation->doConfirm();
						$sendconfirmation->doSend();
	
					
					}
	
					## Include the confirmation class to sent the tickets. 
					$path_include = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'sendonpayment.class.php';
					include_once( $path_include );
					
					## Sending the ticket immediatly to the client.
					$creator = new sendonpayment( (int)$temp_transaction->ordercode );  
					$creator->send();	
	
					## Removing the session, it's not needed anymore.
					$session =& JFactory::getSession();
					$session->clear('ordercode');
					$session->clear('coupon');																				
				
				########################################		

				echo '<h1">'.$config->mailsubject.'</h1>';
				
				$message = str_replace('%%TID%%', $_GET['trxid'], $config->mailbody);
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
				$session->clear('coupon');											

			}else{
				
				$db  = JFactory::getDBO();
				
				## Getting the desired info from the configuration table
				$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$this->failure_tpl_ideal."";
				
				$db->setQuery($sql);
				$config = $db->loadObject();
				
				echo '<h1>'.$config->mailsubject.'</h1>';	
				echo $config->mailbody;
				
				## Removing the session, it's not needed anymore.
				$session =& JFactory::getSession();
				$session->clear('ordercode');
				$session->clear('coupon');					
				
			}
		}			
	}

	####################################################################
	##############										  ##############
	##############	MRCASH PROCESSOR - DONT TOUCH PLEASE  ##############
	##############										  ##############
	####################################################################

	function targetpayMrCashreturn(){

		if ( isset ( $_GET['trxid'] ) ) {

			## Include the classes for TargetPay		
			$path_include = JPATH_SITE.DS.'plugins'.DS.'rdmedia'.DS.'rdmtargetpay'.DS.'rdmedia_targetpay'.DS.'TargetPayMrCash.class.php';
			include_once( $path_include );			

			# Initiate the class
			$oIdeal = new TargetPayMrCash ( $this->rtlo );
		
			if ( $oIdeal->validatePayment ( $_GET['trxid'], 0, $this->sandbox  ) == true ) {
				
				$db = JFactory::getDBO();
				
				## Getting the desired info from the configuration table
				$sql = "SELECT * FROM #__ticketmaster_transactions_temp WHERE transaction_number = '".$_GET['trxid']."'";
				$db->setQuery($sql);
				$temp_transaction = $db->loadObject();
				
				## Removing the session, it's not needed anymore.
				$session =& JFactory::getSession();
				$session->clear('ordercode');								

				## Getting the desired info from the configuration table
				$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$this->success_tpl_mr_cash."";
				
				$db->setQuery($sql);
				$config = $db->loadObject();

				## Getting the desired info from the configuration table
				$sql = "SELECT * FROM #__users WHERE id = ".(int)$temp_transaction->userid."";
				
				$db->setQuery($sql);
				$user = $db->loadObject();	
				
				## Including required paths to calculator.
				$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
				include_once( $path_include );					

				$amount  = _getAmount($temp_transaction->ordercode, 1);	
				
				####### LET'S O THE LOGICAL NOW ########
				
					## Including the table transaction to store it.
					JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tables');
					$row =& JTable::getInstance('transaction', 'Table');	
					
					## Prepare the row.
					$row->transid = $_GET['trxid'];
					$row->userid = (int)$temp_transaction->userid.'/'.$temp_transaction->ordercode;
					$row->details = 'No Customer Data Present';
					$row->amount = $amount;
					$row->type = 'TargetPay MrCash';
					$row->orderid = (int)$temp_transaction->ordercode;
					
					## Store data
					$row->store();	
					
					## Updating the order status in the orders table.
					$query = 'UPDATE #__ticketmaster_orders SET paid = 1, published = 1
							  WHERE ordercode = "'.(int)$temp_transaction->ordercode.'" ';						
					
					## Doing the Query
					$db->setQuery( $query );
					
					## When query goes wrong.. Show message with error.
					if (!$db->query()) {
						$this->setError($db->getErrorMsg());
						return false;
					}	
					
					## Updating the temporary transaction status.
					$query = 'UPDATE #__ticketmaster_transactions_temp SET processed = 1
							  WHERE transaction_number = "'.$_GET['trxid'].'" ';
					
					## Do the query now	
					$db->setQuery( $query );
					
					## When query goes wrong.. Show message with error.
					if (!$db->query()) {
						$this->setError($db->getErrorMsg());
						return false;
					}	
					
					## Now get the orders to create the tickets.
					$query = 'SELECT * FROM #__ticketmaster_orders WHERE ordercode = '.(int)$temp_transaction->ordercode.'';
	
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
						
						$sendconfirmation = new confirmation( (int)$temp_transaction->ordercode );  
						$sendconfirmation->doConfirm();
						$sendconfirmation->doSend();
	
					
					}
	
					## Include the confirmation class to sent the tickets. 
					$path_include = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'sendonpayment.class.php';
					include_once( $path_include );
					
					## Sending the ticket immediatly to the client.
					$creator = new sendonpayment( (int)$temp_transaction->ordercode );  
					$creator->send();	
	
					## Removing the session, it's not needed anymore.
					$session =& JFactory::getSession();
					$session->clear('ordercode');
					$session->clear('coupon');																				
				
				########################################		

				echo '<h1">'.$config->mailsubject.'</h1>';
				
				$message = str_replace('%%TID%%', $_GET['trxid'], $config->mailbody);
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
				$session->clear('coupon');											

			}else{
			
				$db  = JFactory::getDBO();
				
				## Getting the desired info from the configuration table
				$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$this->failure_tpl_mr_cash."";
				
				$db->setQuery($sql);
				$config = $db->loadObject();
				
				echo '<h1>'.$config->mailsubject.'</h1>';	
				echo $config->mailbody;
				
				## Removing the session, it's not needed anymore.
				$session =& JFactory::getSession();
				$session->clear('ordercode');
				$session->clear('coupon');					
				
			}
		}			
	}
		
	####################################################################
	##############										  ##############
	##############	SOFORT PROCESSOR - DONT TOUCH PLEASE  ##############
	##############										  ##############
	####################################################################

	function targetpaySofortreturn(){

		if ( isset ( $_GET['trxid'] ) ) {

			## Include the classes for TargetPay		
			$path_include = JPATH_SITE.DS.'plugins'.DS.'rdmedia'.DS.'rdmtargetpay'.DS.'rdmedia_targetpay'.DS.'TargetPayDIRECTebanking.class.php';
			include_once( $path_include );			

			# Initiate the class
			$oIdeal = new TargetPayDIRECTebanking ( $this->rtlo );
			
			if ($_GET['status'] != 'Success') {
				
				$db      = JFactory::getDBO();
				$publish = 1;	
						
				$query = 'UPDATE #__ticketmaster_transactions_temp'
					  . ' SET processed = '.(int) $publish
					  . ' WHERE transaction_number = '.$_GET['trxid'].'';
				
				## Do the query now	
				$db->setQuery( $query );			

				## When query goes wrong.. Show message with error.
				if (!$db->query()) {
					$this->setError($db->getErrorMsg());
					return false;
				}				
				
				$db = JFactory::getDBO();
				
				## Getting the desired info from the configuration table
				$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$this->failure_tpl_sofort."";
				$db->setQuery($sql);
				$config = $db->loadObject();
			
				echo '<h1>'.$config->mailsubject.'</h1>';
				$message = str_replace($_GET['status'], $msg, $config->mailbody);
				
				echo $message;
				echo '<br/><br/><br/><br/><br/><br/>';
		
				## Removing the session, it's not needed anymore.
				$session = JFactory::getSession();
				$session->clear('ordercode');	
				
				return false;	
			
			}else{
		
				if ( $oIdeal->validatePayment ( $_GET['trxid'], $this->sandbox  ) == true ) {
					
					$db = JFactory::getDBO();
					
					## Getting the desired info from the configuration table
					$sql = "SELECT * FROM #__ticketmaster_transactions_temp WHERE transaction_number = '".$_GET['trxid']."'";
					$db->setQuery($sql);
					$temp_transaction = $db->loadObject();	
						
					if(	$temp_transaction->processed == 1) {

						## Payment not succesful
						$msg = 'Deze transactie is reeds gecontroleerd.';
						self::showmsg($this->failure_tpl_sofort, $msg);
						
						return false;					
					
					}
					
					## Getting the desired info from the configuration table
					$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$this->success_tpl_sofort."";
					
					$db->setQuery($sql);
					$config = $db->loadObject();					
	
					## Getting the desired info from the configuration table
					$sql = "SELECT * FROM #__users WHERE id = ".(int)$temp_transaction->userid."";
					
					$db->setQuery($sql);
					$user = $db->loadObject();	
					
					## Including required paths to calculator.
					$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
					include_once( $path_include );					
	
					$amount  = _getAmount($temp_transaction->ordercode, 1);	
					
					####### LET'S DO THE LOGICAL NOW ########
					
						## Including the table transaction to store it.
						JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tables');
						$row =& JTable::getInstance('transaction', 'Table');	
						
						## Prepare the row.
						$row->transid = $_GET['trxid'];
						$row->userid = (int)$temp_transaction->userid.'/'.$temp_transaction->ordercode;
						$row->details = 'No Customer Data Present';
						$row->amount = $amount;
						$row->type = 'TargetPay Sofort';
						$row->orderid = (int)$temp_transaction->ordercode;
						
						## Store data
						$row->store();	
						
						## Updating the order status in the orders table.
						$query = 'UPDATE #__ticketmaster_orders SET paid = 1, published = 1
								  WHERE ordercode = "'.(int)$temp_transaction->ordercode.'" ';						
						
						## Doing the Query
						$db->setQuery( $query );
						
						## When query goes wrong.. Show message with error.
						if (!$db->query()) {
							$this->setError($db->getErrorMsg());
							return false;
						}	
						
						## Updating the temporary transaction status.
						$query = 'UPDATE #__ticketmaster_transactions_temp SET processed = 1
								  WHERE transaction_number = "'.$_GET['trxid'].'" ';
						
						## Do the query now	
						$db->setQuery( $query );
						
						## When query goes wrong.. Show message with error.
						if (!$db->query()) {
							$this->setError($db->getErrorMsg());
							return false;
						}	
						
						## Now get the orders to create the tickets.
						$query = 'SELECT * FROM #__ticketmaster_orders WHERE ordercode = '.(int)$temp_transaction->ordercode.'';
		
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
							
							$sendconfirmation = new confirmation( (int)$temp_transaction->ordercode );  
							$sendconfirmation->doConfirm();
							$sendconfirmation->doSend();
		
						
						}
		
						## Include the confirmation class to sent the tickets. 
						$path_include = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'sendonpayment.class.php';
						include_once( $path_include );
						
						## Sending the ticket immediatly to the client.
						$creator = new sendonpayment( (int)$temp_transaction->ordercode );  
						$creator->send();	
		
						## Removing the session, it's not needed anymore.
						$session =& JFactory::getSession();
						$session->clear('ordercode');
						$session->clear('coupon');																				
				
					########################################
				
					echo '<h1">'.$config->mailsubject.'</h1>';
					
					$message = str_replace('%%TID%%', $_GET['trxid'], $config->mailbody);
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
					$session->clear('coupon');								
						 				
					
				}else {
					
					## Payment not succesful
					$msg = 'Payment not finished or not completed.';
					self::showmsg($this->failure_tpl_sofort, $msg);  
	
					$session = JFactory::getSession();
					$session->clear('ordercode');				 
	
					
				} 
			}
		}			
	}

	
	function targetpayprocess() {
				
			
		if ( isset($_POST['rtlo'])&& isset($_POST['trxid'])&& isset($_POST['status'])) {
				
			if($status = "000000 OK"){
			
																	
				die( "OK" ); 
				
			}
		
		}
		
		die("IP address not correct... This call is not from Targetpay");
		
	}
	
	function showmsg($msgid, $msg){
		
		$db = JFactory::getDBO();
		
		## Getting the desired info from the configuration table
		$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$msgid."";
		$db->setQuery($sql);
		
		## Run the query.
		$config = $db->loadObject();
	
		echo '<h1>'.$config->mailsubject.'</h1>';
		$message = str_replace('%%MSG%%', $msg, $config->mailbody);
		echo $message;
		echo '<br/><br/><br/><br/><br/><br/>';

		## Removing the session, it's not needed anymore.
		$session = JFactory::getSession();
		$session->clear('ordercode');	
		
		return false;												
				
	}			
}	 
?>