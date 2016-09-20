<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_User_plugin extends WPlugin {
function onUserLogin($user,$options){
$this->onLoginUser($user, $options );
}
function onUserLogout($user,$remember){
$this->onLogoutUser($user, $remember );
}
function onUserAfterDelete($user,$success,$msg){
$this->onAfterDeleteUser($user, $success, $msg);
}
function onUserAfterDeleteGroup($group,$bool,$msg){
$joobiCoreRolesA=array('allusers','visitor','registered','author','editor','publisher','manager','admin','sadmin','supplier','vendor','customer');
return true;
}
function onUserAfterSaveGroup($group){return true;
}
function onUserAfterSave($user,$isnew,$success,$msg){
$checkUserSyncDone=WGlobals::get('userSyncOnSaveDone', false, 'global');
if($checkUserSyncDone ) return true;
$usersAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.user');
$usersAddon->syncUser($user, $isnew, $success, $msg );
WGlobals::set('userSyncOnSaveDone', true, 'global');
return true;
}
function onLoginUser($user,$options){
if(!isset($user['id'])){
$user['id']=WUser::cmsMyUser('id');
}
$userO=null;
if(empty($user['id'])){
$usersM=WModel::get('users','object');
$usersM->whereE('username',$user['username'] );
$userO=$usersM->load('o');
if(!empty($userO->id)){
$user['id']=$userO->id;
}
}
if(empty($user['id'])){
return false;
}
$cacheHandler=WCache::get();
$cacheHandler->resetCache();
$usersSessionC=WUser::session();
$userInfoO=$usersSessionC->setUserSession($user['id'] );
$ip=$usersSessionC->getIP();
if(!empty($ip)){
$uid=WUser::get('uid');
if(empty($uid) && !empty($userO->uid))$uid=$userO->uid;
$usersM=WModel::get('users');
$usersM->whereE('uid',$uid );
$usersM->setVal('login', time());
if(!empty($ip)){
$usersM->setVal('ip',$ip, 0, null, 'ip');
$ipTrackerC=WClass::get('security.lookup', null, 'class', false);
if(!empty($ipTrackerC)){
if(empty($userO->ctyid ))$usersM->setVal('ctyid',$ipTrackerC->ipInfo($ip, 'ctyid'));
if(isset($userO->timezone ) && $userO->timezone==999)$usersM->setVal('timezone',$ipTrackerC->ipInfo($ip, 'timezone'));
}}
$usersM->update();
}
if( WRoles::isNotAdmin()){
if( WPref::load('PCART_NODE_USENEWCART')){
$basketClearC=WClass::get('cart.clear', null, 'class', false);
if(!empty($basketClearC))$basketClearC->resetBasketAddress();
}else{
$basketClearC=WClass::get('basket.clear', null, 'class', false);  if(!empty($basketClearC))$basketClearC->resetBasketAddress();
}
$uid=WUser::get('uid');
if(!empty($uid)){
$itemViewedM=WModel::get('item.viewed');
$itemViewedM->whereE('cookieid', WGlobals::getCookieUser());
$itemViewedM->whereE('uid','0');
$itemViewedM->setVal('uid',$uid );
$itemViewedM->update();
}
}
return true;
}
function onLogoutUser($user,$remember){
$usersSessionC=WUser::session();
$usersSessionC->resetUser();
$usersSessionC->setGuest();
if( WPref::load('PCART_NODE_USENEWCART')){
$basketClearC=WClass::get('cart.clear', null, 'class', false);
if(!empty($basketClearC))$basketClearC->clearEntireBasket();
}else{
$basketClearC=WClass::get('basket.clear', null, 'class', false);if(!empty($basketClearC))$basketClearC->clearEntireBasket();
}
return true;
}
function onAfterDeleteUser($user,$success,$msg){
if(empty($user['id']) || !$success){
return false;
}
$usersM=WModel::get('users');
$usersM->whereE('id',$user['id'] );
return $usersM->deleteAll();
}
}