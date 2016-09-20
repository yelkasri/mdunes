<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_gologin_controller extends WController {
function gologin(){
$allowLogin=WPref::load('PUSERS_NODE_LOGINALLOW');
if(empty($allowLogin)) WPages::redirect('previous');
$username=WController::getFormValue('username','x');
$password=WController::getFormValue('password','x');
if(empty($username) || empty($password)){
$username=WController::getFormValue('loginusername','x');
$password=WController::getFormValue('loginpassword','x');
if(empty($username)){
$this->userE('1458245058QHDN');
WPages::redirect('previous');
}
if(empty($password)){
$this->userE('1406645169SRMK');
WPages::redirect('previous');
}
}
if( WPref::load('PUSERS_NODE_LOGINEMAIL') && strpos($username, '@') > 1){
$possibleUsername=Wuser::get('username',$username );
if(!empty($possibleUsername ))$username=$possibleUsername;
}
$url=WGlobals::getSession('login','previousURL');
$usersCredentialC=WUser::credential();
$usersCredentialC->automaticLogin($username, $password, true, $url );
if(!WUser::get('uid')){
$this->userE('1454869718PIJZ');
$ISBLOCKED=WGlobals::getSession('lastLogin','blocked'.$username );
if(!empty($ISBLOCKED))$this->userN($ISBLOCKED );
}else{
WGlobals::setSession('lastLogin','blocked'.$username, '');
}
if('mobile'==JOOBI_FRAMEWORK_TYPE ) WGlobals::set('logPwd',$password, 'global');
if(!empty($url)){
WPages::redirect($url );
}else{
$loginPage=WPref::load('PUSERS_NODE_LOGIN_LANDING');
if(!empty($loginPage)){
WPages::redirect($loginPage );
}else{
WPages::redirect('previous');
}
}
return true;
}
}