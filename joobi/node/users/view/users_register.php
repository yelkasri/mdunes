<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Users_register_view extends Output_Forms_class {
function prepareView(){
if(!WPref::load('PUSERS_NODE_USECAPTCHA'))$this->removeElements('users_register_captcha');
$removeElementsA=array();
$showMobile=WPref::load('PUSERS_NODE_USEMOBILE');
if(!$showMobile)$removeElementsA[]='users_register_mobile';
$showLanguage=WPref::load('PUSERS_NODE_USELANGUAGE');
if(!$showLanguage)$removeElementsA[]='users_register_language';
$showTimezone=WPref::load('PUSERS_NODE_USETIMEZONE');
if(!$showTimezone)$removeElementsA[]='users_register_timezone';
$showCurrency=WPref::load('PUSERS_NODE_USECURRENCY');
if(!$showCurrency)$removeElementsA[]='users_register_curid';
$showAvatar=WPref::load('PUSERS_NODE_USEAVATAR');
if(!$showAvatar)$removeElementsA[]='users_register_filid';
$showHTML=WPref::load('PUSERS_NODE_USEHTMLEMAIL');
if(!$showHTML)$removeElementsA[]='users_register_html';
if( WExtension::exist('contacts.node')){
$splitname=WPref::load('PCONTACTS_NODE_SPLITNAME');
if($splitname){
$removeElementsA[]='users_register_name';
$middlename=WPref::load('PCONTACTS_NODE_MIDDLENAME');
if(!$middlename)$removeElementsA[]='users_register_middlename';
}else{
$removeElementsA[]='users_register_firstname';
$removeElementsA[]='users_register_middlename';
$removeElementsA[]='users_register_lastname';
}
}else{
$removeElementsA[]='users_register_firstname';
$removeElementsA[]='users_register_middlename';
$removeElementsA[]='users_register_lastname';
}
if(!empty($removeElementsA))$this->removeElements($removeElementsA );
if( WExtension::exist('contacts.node') && ! WPref::load('PCONTACTS_NODE_USETYPE')){
$contactsTypeC=WClass::get('contacts.type');
$count=$contactsTypeC->countTypes();
if($count <=1){
$this->removeElements('users_register_utypid');
}
}
return true;
}}