<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_resetpwd_controller extends WController {
function resetpwd(){
$oneEmail=WController::getFormValue('email','x');
if(empty($oneEmail )){
$this->userE('1410373233CVZL');
return $this->_finsihed();
}
$usersEmail=WClass::get('users.email');
$EMAIL=trim($oneEmail );
if(!$usersEmail->validateEmail($EMAIL)){
$message->userE('1410373233CVZM',array('$EMAIL'=>$EMAIL));
return $this->_finsihed();
}
$uid=WUser::get('uid',$EMAIL );
if(empty($uid )){
$this->userE('1410373233CVZN');
return $this->_finsihed();
}
$password=WTools::randomString( 10, true);
$usersM=WModel::get('users');
$usersM->whereE('uid',$uid );
$usersM->load('o');
if(!isset($usersM->x ))$usersM->x=array();
$usersM->x['password']=$password;
$usersM->x['password_confirmed']=$password;
$usersM->save();
$usersRegisterC=WClass::get('users.register');
$usersRegisterC->emailPassword($uid, $password, true);
$this->userN('1410384292GWRX');
$usersAddon=WAddon::get('users.'.PUSERS_NODE_FRAMEWORK_FE );
$usersAddon->goLogin();
return true;
}
private function _finsihed(){
$usersAddon=WAddon::get('users.'.PUSERS_NODE_FRAMEWORK_FE );
$usersAddon->goLogin();
return true;
}}