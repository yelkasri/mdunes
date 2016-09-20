<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Users_form_view extends Output_Forms_class {
function prepareView(){
$removeElementsA=array();
if( WRoles::isNotAdmin('admin')){
$showMobile=WPref::load('PUSERS_NODE_USEMOBILE');
if(!$showMobile)$removeElementsA[]='users_form_users_mobile';
$showLanguage=WPref::load('PUSERS_NODE_USELANGUAGE');
if(!$showLanguage)$removeElementsA[]='users_form_users_lgid';
$showTimezone=WPref::load('PUSERS_NODE_USETIMEZONE');
if(!$showTimezone)$removeElementsA[]='users_form_users_timezone';
$showCurrency=WPref::load('PUSERS_NODE_USECURRENCY');
if(!$showCurrency)$removeElementsA[]='users_form_users_curid';
$showAvatar=WPref::load('PUSERS_NODE_USEAVATAR');
if(!$showAvatar)$removeElementsA[]='users_form_users_filid';
if( WExtension::exist('contacts.node')){
$splitname=WPref::load('PCONTACTS_NODE_SPLITNAME');
if($splitname){
$removeElementsA[]='users_form_users_name';
$middlename=WPref::load('PCONTACTS_NODE_MIDDLENAME');
IF ( ! $middlename)$removeElementsA[]='users_form_users_middlename';
}else{
$removeElementsA[]='users_form_users_firstname';
$removeElementsA[]='users_form_users_middlename';
$removeElementsA[]='users_form_users_lastname';
}}else{
$removeElementsA[]='users_form_users_firstname';
$removeElementsA[]='users_form_users_middlename';
$removeElementsA[]='users_form_users_lastname';
}
}
$roleC=WRole::get();
if( WRole::hasRole('manager')){
}
$eid=$this->getValue('uid');
if(!empty($eid)){
$uid=WUser::get('uid');
if($eid==$uid)$removeElementsA[]='users_form_users_block';
}
if(!empty($removeElementsA))$this->removeElements($removeElementsA );
return true;
}}