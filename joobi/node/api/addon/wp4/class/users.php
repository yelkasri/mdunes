<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Api_Wp4_Users_class extends WClasses {
private $appName=null;
public function wpRun($appName){
}
public function addUser($user_id=null){
$checkUserSyncDone=WGlobals::get('userSyncOnSaveDone', false, 'global');
if($checkUserSyncDone ) return true;
$userA=$this->_loadUserInfo($user_id );
if(empty($userA)) return false;
$usersAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.user');
$usersAddon->syncUser($userA, true, true);
WGlobals::set('userSyncOnSaveDone', true, 'global');
return true;
}
public function editUser($user_id=null,$old_user_data=null){
$checkUserSyncDone=WGlobals::get('userSyncOnSaveDone', false, 'global');
if($checkUserSyncDone ) return true;
$userA=$this->_loadUserInfo($user_id );
if(empty($userA)) return false;
$usersAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.user');
$usersAddon->syncUser($userA, false, true);
WGlobals::set('userSyncOnSaveDone', true, 'global');
return true;
}
public function deleteUser($user_id=null){
if(empty($user_id)) return false;
$usersM=WModel::get('users');
$usersM->whereE('id',$user_id );
return $usersM->deleteAll();
}
public function addUserPlugin($namekey,$user_id=null){
$userA=$this->_loadUserInfo($user_id );
if(empty($userA)) return false;
$instance=WExtension::plugin($namekey );
if(!empty($instance))$instance->onUserAfterSave($userA, true, '','');
}
public function deleteUserPlugin($namekey,$user_id=null){
$userA=$this->_loadUserInfo($user_id );
if(empty($userA)) return false;
$instance=WExtension::plugin($namekey );
if(!empty($instance))$instance->onUserAfterDelete($userA, '','');
}
public function editUserPlugin($namekey,$user_id=null,$old_user_data=null){
$userA=$this->_loadUserInfo($user_id );
if(empty($userA)) return false;
$instance=WExtension::plugin($namekey );
if(!empty($instance))$instance->onUserAfterSave($userA, false, '','');
}
public function loginUserPlugin($namekey,$user_login=null,$user=null){
$userA=$this->_loadUserInfo($user->ID );
if(empty($userA)) return false;
$instance=WExtension::plugin($namekey );
if(!empty($instance))$instance->onUserLogin($userA, null );
}
public function logoutUserPlugin($namekey){
$instance=WExtension::plugin($namekey );
if(!empty($instance))$instance->onUserLogout( null, null );
}
public function loginUserFailedPlugin($namekey,$username=''){
$instance=WExtension::plugin($namekey );
$response=array('username'=> $username );
if(!empty($instance))$instance->onUserLoginFailure($response );
}
private function _loadUserInfo($user_id){
if(empty($user_id)){
$email=WGlobals::get('email');
$wpUserT=WTable::get('users','','ID');
$wpUserT->whereE('user_email',$email );
$user_id=$wpUserT->load('lr','ID');
}
if(empty($user_id)) return false;
$userCompleteO=get_userdata($user_id );
if(empty($user_id)) return false;
$userO=$userCompleteO->data;
$userA=array();
if(isset($userO->ID))$userA['id']=$userO->ID;
if(isset($userO->user_email))$userA['email']=$userO->user_email;
if(isset($userO->user_login))$userA['username']=$userO->user_login;
$userA['password_clear']=WGlobals::get('WP_userPass','','global');
if(isset($userO->display_name))$userA['name']=$userO->display_name;
$userA['roles']=$userCompleteO->roles;
return $userA;
}
}