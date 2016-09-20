<?php

/**
* TargetPay MrCash example class
* 
*  MKh
*  26-11-2010
*/
  
# requires the init class
require_once ( 'TargetPay.class.php' );
  
class TargetPayMrCash extends TargetPay {
  
  # ofcourse construct
  public function __construct( $intRtlo ) {
    # call parent constructor 
    parent::__construct( $intRtlo );
  }
  
  /**
  * @Desc start payment
  * @Return array ( trxid, MrCashReturnUrl )
  */
  
    public function startPayment () {
      
      try {
          
          
          # Build parameter string
          //$aParameters = $this->getBaseRequest();
          $aParameters = array();
          $aParameters['rtlo'] = $this->intRtlo;
          $aParameters['description'] = $this->strDescription;
          $aParameters['amount'] =  $this->MrCashAmount;
          $aParameters['userip'] = $this->strUserIp;
          $aParameters['returnurl'] = $this->strReturnUrl;
          $aParameters['reporturl'] = $this->strReportUrl;
          
          # do request
          $strResponse = $this->getResponse( $aParameters, 'https://www.targetpay.com/mrcash/start?');
          $aResponse = explode('|', $strResponse );

          # Bad response
          if ( !isset ( $aResponse[1] ) ) {
            throw new Exception( 'Error' . $aResponse[0] );    
          }
          
          $iTrxID = explode ( ' ', $aResponse[0] );
          
          # We return TRXid and url to rederict
          return array ( $iTrxID[1], $aResponse[1] );
              
      } 
      catch( Exception $e ) {
      
        # error, could not proceed 
        echo $e->getMessage();
      
      }    
  }
 
  
  
  
  /**
   * Validate the payment now by trxId
   *
   * @return bool
   */
  
   public function validatePayment ( $intTrxId, $iOnce = 1, $iTest = 0 ) {
       
       try {
        
           # Build parameter string
           $aParameters = array();
           $aParameters['rtlo'] = $this->intRtlo;
           $aParameters['trxid'] = $intTrxId;
           $aParameters['once'] = $iOnce;
           $aParameters['test'] = $iTest; 
           
           # do request
           $strResponse = $this->getResponse ( $aParameters , 'https://www.targetpay.com/mrcash/check?');
           $aResponse = explode('|', $strResponse );

           # Bad response
           if (  $aResponse[0] != '000000 OK' ) {
                throw new Exception( $aResponse[0] );    
           }
           
           return true;
       
       }    
       catch( Exception $e ) {
      
        # error, could not proceed 
        echo $e->getMessage();
      
      } 
               
   }
   
  
  /**
  * 
  * @Desc set return url
  * 
  */
  
  public function setMrCashReturnUrl ( $strReturnUrl ) {
    $this->strReturnUrl = $strReturnUrl;  
     return $this;   
  }
  
  /**
  * 
  * @Desc set report url
  * 
  */
  
  public function setMrCashReportUrl ( $strReportUrl ) {
    $this->strReportUrl = $strReportUrl;  
     return $this;   
  }
  
  /**
  * 
  * @Desc set description for transaction
  * 
  */
  
  public function setMrCashDescription ( $strDescription ) {
    $this->strDescription = $strDescription;  
     return $this;   
  }
  
  /**
  * 
  * @Desc set Ip Address for transaction
  * 
  */
  
  public function setMrCashIp ( $ip ) {
     $this->strUserIp = $ip;  
     return $this;   
  }
  
  /**
  * @Desc set amount
  * 
  */
  
  public function setMrCashAmount ( $intAmount ) {
    
      # Is this a valid ideal amount?
      if ( is_numeric ( $intAmount ) && $intAmount > 0 ) {
        $this->MrCashAmount = $intAmount;    
      }
      else {
        throw new Exception( 'Invalid amount, please check.' );   
      }
       return $this;
  }
}
  
  
?>
