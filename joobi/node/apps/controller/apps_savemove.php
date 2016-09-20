<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_savemove_controller extends WController {
function savemove(){
$SID=WModel::get('apps','sid');
$trk=WGlobals::get( JOOBI_VAR_DATA );
$wid=$trk[$SID]['wid'];
$url=$trk[$SID]['x']['url'];
$appsUserInfo=WModel::get('apps.userinfos');
$appsUserInfo->whereE('wid',$wid );
$tokenOrigin=$appsUserInfo->load('lr','token');
$data=new stdClass;
$data->url=$url;
$data->token=$tokenOrigin;
$netcom=WNetcom::get();
$token=$netcom->send( WPref::load('PAPPS_NODE_REQUEST'), 'license','move',$data );
$message=WMessage::get();
if(!empty($token)){
$message->userS('1206961836KSBJ',array('$url'=>$url));
$message->userS('1206961836KSBK',array('$token'=>$token));
$appsInfoC=WClass::get('apps.info');
$appsInfoC->cancelSweet($wid, 0 );
return true;
}else{
$message->userE('1206961836KSBL');
return false;
}
}
}