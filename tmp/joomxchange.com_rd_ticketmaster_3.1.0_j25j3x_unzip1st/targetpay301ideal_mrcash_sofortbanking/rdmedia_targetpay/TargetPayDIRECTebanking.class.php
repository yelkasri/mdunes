<?php

/**
* TargetPay MrCash example class
* 
*  SvdV
*  22-12-2010
*/
  
# requires the init class
require_once ( 'TargetPay.class.php' );
  
class TargetPayDIRECTebanking extends TargetPay {
  
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
          $aParameters['amount'] =  $this->DIRECTebankingAmount;
          $aParameters['country'] =  $this->intCountry;
          $aParameters['type'] =  $this->intType;
          $aParameters['userip'] = $this->strUserIp;
          $aParameters['returnurl'] = $this->strReturnUrl;
          $aParameters['reporturl'] = $this->strReportUrl;
          
          # do request
          $strResponse = $this->getResponse( $aParameters, 'https://www.targetpay.com/directebanking/start?');
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
  
   public function validatePayment ( $intTrxId, $iTest = 0 ) {
       
       try {
        
           # Build parameter string
           $aParameters = array();
           $aParameters['rtlo'] = $this->intRtlo;
           $aParameters['trxid'] = $intTrxId;
           $aParameters['test'] = $iTest; 
           
           # do request
           $strResponse = $this->getResponse ( $aParameters , 'https://www.targetpay.com/directebanking/check?');
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
  
  public function setDIRECTebankingReturnUrl ( $strReturnUrl ) {
    $this->strReturnUrl = $strReturnUrl;  
     return $this;   
  }
  
  /**
  * 
  * @Desc set report url
  * 
  */
  
  public function setDIRECTebankingReportUrl ( $strReportUrl ) {
    $this->strReportUrl = $strReportUrl;  
     return $this;   
  }
  
  /**
  * 
  * @Desc set description for transaction
  * 
  */
  
  public function setDIRECTebankingDescription ( $strDescription ) {
    $this->strDescription = $strDescription;  
     return $this;   
  }
  
  /**
  * 
  * @Desc set Ip Address for transaction
  * 
  */
  
  public function setDIRECTebankingIp ( $ip ) {
     $this->strUserIp = $ip;  
     return $this;   
  }
  
  /**
  * @Desc set amount
  * 
  */
  
  public function setDIRECTebankingAmount ( $intAmount ) {
    
      # Is this a valid DIRECTebanking amount?
      if ( is_numeric ( $intAmount ) && $intAmount > 0 ) {
        $this->DIRECTebankingAmount = $intAmount;    
      }
      else {
        throw new Exception( 'Invalid amount, please check.' );   
      }
       return $this;
  }
  
  /**
  * @Desc set country
  * 
  */
  
  public function setDIRECTebankingCountry ( $intCountry ) {
    
      # Is this a valid DEB amount?
      if ( is_numeric ( $intCountry ) && $intCountry > 0 ) {
        $this->intCountry = $intCountry;    
      }
      else {
        throw new Exception( 'Invalid country, please check.' );   
      }
       return $this;
  }
  
  
  /**
  * @Desc set type
  * 
  */
  
  public function setDIRECTebankingType( $intType ) {
    
      # Is this a valid type?
      if ( is_numeric ( $intType ) && $intType > 0 ) {
        $this->intType = $intType;    
      }
      else {
        throw new Exception( 'Invalid type, please check.' );   
      }
       return $this;
  }
}
  
  
?>
