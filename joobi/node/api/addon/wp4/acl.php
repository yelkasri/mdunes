<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Api_Wp4_Acl_addon extends WClasses {
private $_allDefaultWPRoleA=array('subscriber','contributor','author','editor','administrator','super admin');
private $_equivalentRoleA=array(
 'super admin'=>'sadmin'
,'administrator'=>'sadmin'
,'editor'=>'editor'
,'author'=>'author'
,'contributor'=>'author'
,'subscriber'=>'registered'
);
public function getEquivalentRoles(){
$rolesA=array();
$rolesA['vendor']='admin';
$rolesA['storemanager']='sadmin';
$rolesA['moderator']='admin';
$rolesA['supportmanager']='sadmin';
$rolesA['sales_agent']='admin';
$rolesA['sales_manager']='admin';
$rolesA['mailpublisher']='admin';
$rolesA['mailmanager']='sadmin';
$rolesA['listmanager']='sadmin';
return $rolesA;
}
public function getJoobiRoleFromWPRole($wpRole){
if(isset($this->_equivalentRoleA[$wpRole] )){
return $this->_equivalentRoleA[$wpRole];
}else{
return false;
}
}
public function deleteExtra(&$joobiRoleM){
$cmsID=$this->getCMSRole($joobiRoleM->rolid );
if(empty($cmsID)) return true;
return true;
}
public function updateRoleFromFramework($JUser){
if(empty($JUser['roles'])){
if(!empty($JUser['id'] )){
$newUsertype=get_option('default_role');
if(!empty($newUsertype)){
$joobiRole=$this->_equivalentRoleA[$newUsertype];
$equivaluentRole=WRole::getRole($joobiRole );
if(!empty($equivaluentRole )) return $equivaluentRole;
}
return WRole::getRole('registered');
}else{
return WRole::getRole('allusers');
}
}
foreach($this->_allDefaultWPRoleA as $oneRole){
if( in_array($oneRole, $JUser['roles'] )){
$joobiRole=$this->_equivalentRoleA[$oneRole];
$rolid=WRole::getRole($joobiRole );
break;
}
}
if(empty($rolid)){
$rolid=WRole::getRole('allusers');
}
return $rolid;
}
public function getCMSRole($rolid,$recalcul=false){
if( is_numeric($rolid))$rolid=WRole::getRole($rolid, 'namekey');
$reversedA=array_flip($this->_equivalentRoleA );
if(isset($reversedA[$rolid])) return $reversedA[$rolid];
else return '';
}
public function updateExtraCMSRoles($user,$CMSrolid,$uid){
if(empty($uid)) return false;
if(empty($allRolesToHaveA)) return false;
$dataToAdd=array();
$dataToRemove=array();
foreach($user['roles'] as $key=> $newRole){
if(empty($this->_equivalentRoleA[$newRole])) continue;
$rolidString=$this->_equivalentRoleA[$newRole];
$rolid=WRole::getRole($rolidString );
if(!in_array($newRole, $this->_allDefaultWPRoleA )){
$dataToRemove[]=$rolid;
}else{
$dataToAdd[]=array($uid, $rolid );
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