<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_roles_assign_controller extends WController {
function assign(){
$eid=WGlobals::getEID();
$rolid=WGlobals::get('rolid');
$action=WGlobals::get('act', 0 );
if(!empty($eid) && !empty($rolid)){
if($action==2){WUser::addRole($eid, $rolid );
}elseif($action==1){WUser::removeRole($eid, $rolid );
}
$userO=new stdClass;
$userO->uid=$eid;
$userO->newUser=false;
WController::trigger('users','onRoleUpdated',$userO );
}
$link=WPage::linkPopUp('controller=users-roles&eid='.$eid );
WPages::redirect($link, false , false);
}
}