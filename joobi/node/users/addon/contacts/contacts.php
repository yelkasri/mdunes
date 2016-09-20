<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WLoadFile('users.addon.users.users');
class Users_Contacts_addon extends Users_Users_addon {
public function showUserProfile($eid,$onlyLink=false){
$controller=WGlobals::get('controller');
$task=WGlobals::get('task');
if('users'==$controller && 'dashboard'==$task)$link=false;
else $link='controller=users&task=dashboard&eid='.$eid;
if($onlyLink ) return $link;
if(!empty($link)) WPages::redirect($link );
}
function editUserRedirect($eid,$onlyLink=false){
if( WRoles::isAdmin()){
$link='controller=contacts&task=edit&eid='.$eid;
if($onlyLink ) return $link;
if(!empty($link)) WPages::redirect($link );
}else{
$controller=WGlobals::get('controller');
$task=WGlobals::get('task');
if('users'==$controller && 'edit'==$task)$link=false;
else $link='users&task=edit&eid='.$eid;
if($onlyLink ) return $link;
if(!empty($link)) WPages::redirect($link );
}
}
}