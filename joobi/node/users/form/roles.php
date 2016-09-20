<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Roles_form extends WForms_default {
function show(){
$usersRoleM=WModel::get('users.role');
$usersRoleM->makeLJ('role','rolid');
$usersRoleM->makeLJ('roletrans','rolid','rolid', 1,2);
$usersRoleM->whereLanguage(2);
$usersRoleM->whereE('uid',$this->value );
$usersRoleM->select( array('color'), 1 );
$usersRoleM->select('name', 2 );
$usersRoleM->setLimit( 1000 );
$allRolesA=$usersRoleM->load('ol','rolid');
$rolid=WUser::get('rolid',$this->value);
$weHave=false;
$content='';
if(!empty($allRolesA)){
foreach($allRolesA as $oneRole){
if($oneRole->rolid==$rolid)$weHave=true;
if(!empty($oneRole->name))$content .='<span style="color:'.$oneRole->color.';">'.$oneRole->name.'</span><br>'; 
}
}
if(!$weHave){
$roleM=WModel::get('role');
$roleM->makeLJ('roletrans','rolid','rolid', 0,1);
$roleM->whereLanguage(1);
$roleM->whereE('rolid',$rolid );
$roleM->select('name', 1 );
$oneRole=$roleM->load('o',array('color','rolid'));
$content='<span style="color:'.$oneRole->color.';">'.$oneRole->name.'</span><br>'. $content; 
}
$this->content=$content;
return true;
}}