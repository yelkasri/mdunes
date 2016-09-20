<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_goregister_controller extends WController {
function goregister(){
$allowRegistration=WPref::load('PUSERS_NODE_REGISTRATIONALLOW');
if(empty($allowRegistration)){
$this->userE('1401855798FZFQ');
return false;
}
$captchaToolsC=WClass::get('main.captcha');
if(!$captchaToolsC->checkProcedure()) return false;
$email=WController::getFormValue('email','users');
if(empty($email)){
return $this->historyE('1379525427BHFS');
}
$usersEmailC=WClass::get('users.email');
if(!$usersEmailC->checkEmailUnique($email )){
return $this->historyE('1439907527RWCD');
}
$username=WController::getFormValue('username','users');
if(!$usersEmailC->checkUsernameUnique($username )){
return $this->historyE('1439907527RWCE');
}
WGlobals::set('userOnRegister', true, 'global');
$status=parent::save();
if(!$status){
return $this->historyE('1401908086NTVF');
}
$activationmethod=WPref::load('PUSERS_NODE_ACTIVATIONMETHOD');
switch($activationmethod){
case 'admin':
$this->userS('1401908086NTVG');
break;
case 'self':
$this->userS('1401908086NTVH');
$this->userW('1401908086NTVI');
break;
default:
$username=WController::getFormValue('username','users');
$x=WController::getFormValue('x','users');
$password=$x['password'];
if(!empty($username ) && !empty($password )){
$usersCredentialC=WUser::credential();
$usersCredentialC->automaticLogin($username, $password );
$this->userS('1401908086NTVJ');
}else{
$this->userS('1401908086NTVK');
}break;
}
$landing=WPref::load('PUSERS_NODE_REGISTRATION_LANDING');
if(!empty($landing )){
WPages::redirect($landing );
}
return true;
}
}