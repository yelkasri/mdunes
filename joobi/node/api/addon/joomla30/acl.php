<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Api_Joomla30_Acl_addon extends WClasses {
protected $allDefaultJoomlaRoleA=array('allusers','visitor','registered','author','editor','publisher','manager','admin','sadmin');
public function getEquivalentRoles(){
$rolesA=array();
$rolesA['vendor']='manager';
$rolesA['storemanager']='admin';
$rolesA['moderator']='manager';
$rolesA['supportmanager']='admin';
$rolesA['sales_agent']='manager';
$rolesA['sales_manager']='admin';
$rolesA['mailpublisher']='manager';
$rolesA['mailmanager']='admin';
$rolesA['listmanager']='admin';
return $rolesA;
}
public function deleteExtra(&$joobiRoleM){
$cmsID=$this->getCMSRole($joobiRoleM->rolid );
if(empty($cmsID)) return true;
$cmsRole=WModel::get('joomla.aclgroups');
$cmsRole->whereE($cmsRole->getPK(), $cmsID );
$parentID=$cmsRole->load('lr',$cmsRole->getParam('parentmap','parent'));
if(!empty($parentID)){
$cmsRoleMaps=WModel::get('core.acl.groups.aro.map');
$cmsRoleMaps->setVal('group_id',$parentID);
$cmsRoleMaps->whereE('group_id',$cmsID);
$cmsRoleMaps->setIgnore();
$cmsRoleMaps->update();
$cmsUsers=WModel::get('users');
$cmsUsers->setVal('gid',$parentID);
$cmsUsers->whereE('gid',$cmsID);
$cmsUsers->setIgnore();
$cmsUsers->update();
}
if(!$cmsRole->delete($cmsID)) return false;
$cmsRole->redoTree();
return true;
}
public function updateRoleFromFramework($JUser){
if(empty($JUser['groups'])){
if(!empty($JUser['id'] ) || empty($JUser['guest'] )){
$usersConfig=JComponentHelper::getParams('com_users');
$newUsertype=$usersConfig->get('new_usertype');
if(!empty($newUsertype)){
$roleM=WModel::get('role');
$roleM->whereE('j16',$newUsertype );
$roleM->orderBy('lft','ASC');
$equivaluentRole=$roleM->load('lr','rolid');
if(!empty($equivaluentRole )) return $equivaluentRole;
}
return WRole::getRole('registered');
}else{
return WRole::getRole('allusers');
}
}
$rolemodel=WModel::get('role');
$rolemodel->whereIn('j16',$JUser['groups'] );
$rolemodel->whereIn('namekey',$this->allDefaultJoomlaRoleA );
$rolemodel->orderBy('core','DESC');
$rolemodel->orderBy('j16','DESC');
$rolemodel->select('rolid');
$rolid=$rolemodel->load('lr');
if(empty($rolid)){
$rolid=WRole::getRole('allusers');
}
return $rolid;
}
public function getCMSRoles($rolids,$objectList=false,$fromRolid=true){
if(!is_array($rolids))$rolids=array($rolids );
$rolemodel=WModel::get('role');
if($fromRolid){
$rolemodel->whereIn('rolid',$rolids );
}else{
$rolemodel->whereIn('namekey',$rolids );
}
$rolemodel->where('j16','>',0) ;
if($objectList){
$rolemodel->select( array('j16','namekey','rolid'));
return $rolemodel->load('ol');
}else{
$rolemodel->select('j16');
return $rolemodel->load('lra');
}
}
public function getCMSRole($rolid,$recalcul=false){
static $memoryEquivalent;
if(!$recalcul && isset($memoryEquivalent[$rolid])){
return $memoryEquivalent[$rolid];
}
$rolemodel=WModel::get('role');
if(is_numeric($rolid)){
$rolemodel->whereE('rolid',$rolid );
}elseif(is_string($rolid)){
$rolemodel->whereE('namekey',$rolid );
}
$rolemodel->where('j16','!=',0 );
$rolemodel->orderBy('rolid');
$rolemodel->setLimit(1);
$rolemodel->select('j16');
$cms=$rolemodel->load('lr');
$memoryEquivalent[$rolid]=(empty($cms)?0 : $cms );
return $memoryEquivalent[$rolid];
}
public function updateExtraCMSRoles($user,$CMSrolid,$uid){
if(empty($uid)) return false;
$roleM=WModel::get('joomla.aclgroups');
$roleM->makeLJ('role','id','j16', 0, 1 );
$roleM->makeLJ('joomla.aclgroupuser','id','group_id', 0, 2 );
$roleM->whereOn('user_id','=',$user['id'], 2 );
$roleM->whereIn('namekey',$this->allDefaultJoomlaRoleA, 1, true);
if(!empty($CMSrolid))$roleM->where('rolid','!=',$CMSrolid, 1 );
$roleM->select('rolid', 1 );
$roleM->select('id', 0 );
$roleM->select('user_id', 2 );
$allRolesToHaveA=$roleM->load('ol');
if(empty($allRolesToHaveA)) return false;
$dataToAdd=array();
$dataToRemove=array();
foreach($allRolesToHaveA as $newRole){
if(empty($newRole->user_id)){
$dataToRemove[]=$newRole->rolid;
}else{
$dataToAdd[]=array($uid, $newRole->rolid );
}
}
$usersRoleM=WModel::get('users.role');
$usersRoleM->setAudit();
if(!empty($dataToRemove)){
$usersRoleM->whereE('uid',$uid );
$usersRoleM->whereIn('rolid',$dataToRemove );
$usersRoleM->delete();
}
if(!empty($dataToAdd)){
$usersRoleM->setIgnore(true);
$usersRoleM->insertMany( array('uid','rolid') , $dataToAdd );
}
}
}