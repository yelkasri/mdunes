<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_register_controller extends WController {
function register(){
$uid=WUser::get('uid');
$usersAddon=WAddon::get('users.'.WPref::load('PUSERS_NODE_FRAMEWORK_FE'));
if(empty($uid)){
$allowRegistration=WPref::load('PUSERS_NODE_REGISTRATIONALLOW');
if(empty($allowRegistration)){
$this->userE('1401855798FZFQ');
return false;
}
$usersAddon->goRegister();
if( WExtension::exist('contacts.node')){
if( WPref::load('PCONTACTS_NODE_USETYPE')){
$contactsTypeC=WClass::get('contacts.type');
$count=$contactsTypeC->countTypes();
if($count > 1){
$utypid=WGlobals::get('utypid');
if(empty($utypid)){
$this->setView('users_profile_choose');
return true;
}}}}
}else{
$usersAddon->goProfile($uid );
}
return true;
}
}