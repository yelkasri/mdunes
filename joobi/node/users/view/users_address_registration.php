<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Users_address_registration_view extends Output_Forms_class {
function prepareView(){
$uid=WUser::get('uid');
if(!empty($uid)){
$this->removeElements( array('users_address_registration_name','users_address_registration_username','users_address_registration_email','users_address_registration_password','users_address_registration_confirmpassword'));
}
$zoneusestates=( WPref::load('PCART_NODE_USENEWCART')?WPref::load('PCART_NODE_ZONEUSESTATES') : WPref::load('PBASKET_NODE_ZONEUSESTATES'));
if(empty($zoneusestates)){
$this->removeElements( array('users_address_registration_originstateid'));
}else{
$this->removeElements( array('users_address_registration_originstate'));
}
$removeElementsA=array();
if( WExtension::exist('contacts.node')){
$splitname=WPref::load('PCONTACTS_NODE_SPLITNAME');
if($splitname){
$removeElementsA[]='users_address_registration_name';
$middlename=WPref::load('PCONTACTS_NODE_MIDDLENAME');
if(!$middlename)$removeElementsA[]='users_address_registration_middlename';
}else{
$removeElementsA[]='users_address_registration_firstname';
$removeElementsA[]='users_address_registration_middlename';
$removeElementsA[]='users_address_registration_lastname';
}}else{
$removeElementsA[]='users_address_registration_firstname';
$removeElementsA[]='users_address_registration_middlename';
$removeElementsA[]='users_address_registration_lastname';
}if(!WPref::load('PUSERS_NODE_USEMOBILE')){
$removeElementsA[]='users_address_registration_phone';
}if(!empty($removeElementsA))$this->removeElements($removeElementsA );
$this->_setProcessTail();
return true;
}
private function _setProcessTail(){
$basketTailC=WClass::get('cart.tail' , null, 'class', false);
if($basketTailC)$processTail=$basketTailC->displayTail('Address');
else {
$message=WMessage::get();
$message->codeE('Class cart_tail does not exist, trace function _setProcessTail');
return false;
}
$this->setValue('processtail',$processTail );
return true;
}}