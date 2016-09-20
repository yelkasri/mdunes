<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_approval_controller extends WController {
function approval(){
$eid=WGlobals::getEID();
if(empty($eid)) return false;
if( WRole::hasRole('sadmin')) return true;
if( WRole::hasRole('admin')){
if( WRole::hasRole('sadmin',$eid )) return false;
}
$lock=WGlobals::get('lock');
$usersRegisterC=WClass::get('users.register');
$status=$usersRegisterC->sendApprovalConfirmation($eid , $lock );
if($lock){
$this->userS('1401855798FZFR');
}else{
$this->userE('1401855798FZFS');
}
return true;
}
}