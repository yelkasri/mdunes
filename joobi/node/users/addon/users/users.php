<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WLoadFile('users.class.parent', JOOBI_DS_NODE );
class Users_Users_addon extends Users_Parent_class {
public function getUser($userId,$loadFrom=''){
$usersAddon=WAddon::get('users.'.JOOBI_FRAMEWORK );
return $usersAddon->getUser($userId, $loadFrom );
}
public function goRegister($itemId=null){
$register=WPref::load('PUSERS_NODE_REGISTRATION_PAGE');
if(!empty($register)) WPages::redirect($register );
$controller=WGlobals::get('controller');
$task=WGlobals::get('task');
if('users'==$controller && 'register'==$task ) return true;
WPages::redirect('controller=users&task=register');
}
public function goLogin($itemId=null,$message=''){
$login=WPref::load('PUSERS_NODE_LOGIN_PAGE');
if(!empty($login)) WPages::redirect($login );
$controller=WGlobals::get('controller');
$task=WGlobals::get('task');
if('users'==$controller && 'login'==$task ) return true;
$style=WPref::load('PUSERS_NODE_LOGIN_STYLE');
if(empty($style)) WPages::redirect('controller=users&task=login');
else WPages::redirect('controller=users&task=loginregister');
}
public function goProfile($uid=null){
if(empty($uid))$uid=WUser::get('uid');
$this->showUserProfile($uid, false);
}
public function addUserRedirect(){
return true;
}
public function showUserProfile($eid,$onlyLink=false){
$controller=WGlobals::get('controller');
$task=WGlobals::get('task');
if('users'==$controller && 'dashboard'==$task)$link=false;
else $link='controller=users&task=dashboard&eid='.$eid;
if($onlyLink ) return $link;
if(!empty($link)) WPages::redirect($link );
}
public function editUserRedirect($eid,$onlyLink=false){
$controller=WGlobals::get('controller');
$task=WGlobals::get('task');
if('users'==$controller && 'edit'==$task)$link=false;
else $link='users&task=edit&eid='.$eid;
if($onlyLink ) return $link;
if(!empty($link)) WPages::redirect($link );
}
public function ghostAccount($email,$password,$name='',$username=null,$automaticLogin=false,$createJoobiUser=true,$sendPwd=false,$extraPrams=null){
$usersAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.user');
return $usersAddon->ghostAccount($email, $password, $name, $username, $automaticLogin, $createJoobiUser, $sendPwd, $extraPrams );
}
public function getPicklistElement(){
$usersAddon=WAddon::get('users.'.JOOBI_FRAMEWORK );
return $usersAddon->getPicklistElement();
}
public function automaticLogin($username,$password,$url='',$pageId=0){
$usersAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.user');
$usersAddon->automaticLogin($username, $password, $url, $pageId );
}
}