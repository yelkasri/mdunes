<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Api_class extends WClasses {
public function blockUser($block=1,$uid=null,$recordIncident=true,$SOURCE=''){
$resetCurrent=false;
if(empty($uid)){
return false;
}
if(empty($uid)) return false;
if(!is_array($uid))$uid=array($uid );
$usersM=WModel::get('users');
$usersM->whereIn('uid',$uid );
$usersM->setVal('blocked',$block );
$usersM->update();
$usersAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.user');
$usersAddon->blockUser($block, $uid );
if( WExtension::exist('security.node')){
if($recordIncident && $block){
$securityReportC=WClass::get('security.report');
foreach($uid as $oneUID){
$USERNAME=WUsers::get('username',$oneUID );
if(empty($oneUID)){
$ADMIN='';
$CAUSE=str_replace(array('$USERNAME','$ADMIN'), array($USERNAME,$ADMIN),WText::t('1454276195MTCB'));
}else{
$ADMIN=WUsers::get('username');
$CAUSE=str_replace(array('$USERNAME','$SOURCE'), array($USERNAME,$SOURCE),WText::t('1456969005RJNC'));
}
$details='Blocked into the function blockUser() : ';
$details .=print_r( debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ), true);
$securityReportC->recordIncident('manually_blocked',$CAUSE, $details, true, $oneUID );
}
}elseif(!$block && WRoles::hasRole('admin')){
$incidentM=WModel::get('security.incident');
$incidentM->whereIn('uid',$uid );
$incidentM->whereIn('type',array( 11, 14 ));
$incidentM->setVal('publish', -1 );
$incidentM->update();
}
}
$sessionM=WModel::get('library.session');
$sessionM->whereIn('uid',$uid );
$sessionM->delete();
if($resetCurrent){
WUser::get( null, 'reset');
$usersSessionC=WUser::session();
$usersSessionC->resetUser();
}
}
}