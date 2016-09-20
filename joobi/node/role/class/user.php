<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Role_User_class extends WClasses {
public function insertRole($uid,$rolidA,$checkAlreadyHas=false){
if(empty($uid) || empty($rolidA)) return false;
if(!is_array($rolidA))$rolidA=array($rolidA );
$roleAddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.role');
$column=$roleAddon->getColumnName();
$roleC=WRole::get();
$roleM=WModel::get('role');
$usersRoleM=WModel::get('users.role');
foreach($rolidA as $oneRole){
if(!is_numeric($oneRole)){
$oneRole=$roleC->getRole($oneRole, 'rolid');
}if($checkAlreadyHas){
$hasRole=$roleC->hasRole($oneRole, $uid );
if($hasRole ) continue;
}
$usersRoleM->setVal('uid',$uid );
$usersRoleM->setVal('rolid',$oneRole );
$usersRoleM->insertIgnore();
$roleM->whereE('rolid',$oneRole );
$equivalentRole=$roleM->load('lr',$column );
if(!empty($equivalentRole)){
$roleAddon->insertRole($uid, $equivalentRole );
}
}
$roles=WRole::get();
$roles->reloadSession($uid );
return true;
}
public function deleteRole($uid,$rolidA){
if(empty($uid) || empty($rolidA)) return false;
if(!is_array($rolidA))$rolidA=array($rolidA );
$goodRolidA=array();
$roleC=WRole::get();
foreach($rolidA as $oneRole){
if(!is_numeric($oneRole)){
$goodRolidA[]=$roleC->getRole($oneRole, 'rolid');
}else{
$goodRolidA[]=$oneRole;
}}
$usersRoleM=WModel::get('users.role');
if(is_array($uid)){
$usersRoleM->whereIn('uid',$uid );
}else{
$usersRoleM->whereE('uid',$uid );
}
$usersRoleM->whereIn('rolid',$goodRolidA );
$usersRoleM->delete();
$roleAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.role');
$column=$roleAddon->getColumnName();
$roleM=WModel::get('role');
foreach($goodRolidA as $oneRole){
$roleM->whereE('rolid',$oneRole );
$equivalentRole=$roleM->load('lr',$column );
if(!empty($equivalentRole)){
$roleAddon->deleteRole($uid, $equivalentRole );
}
}
$roles=WRole::get();
$roles->reloadSession($uid );
return true;
}
}