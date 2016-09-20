<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_save_controller extends WController {
function save(){
$token=WController::getFormValue('token');
$token=strtoupper( trim($token));
if(!empty($token)){
$sx1Token=array('ACA','MOB','MOV','TOK','MAN','DEM','API');
$tID=strtoupper( substr($token, 0, 3 ));
if( in_array($tID, $sx1Token )){
$ltypeTolicence=0;
$widTolicence=0;
$levelTolicence=0;
$appsInfoC=WClass::get('apps.info');
$status=$appsInfoC->requestCandy($ltypeTolicence, $widTolicence , $levelTolicence, $token, true);
}elseif($tID=='STE'){
$data=new stdClass;
$data->url='';
$data->token=$token;
$data->key='bgU5J*q7*5dbl4n43jlfh(jhu93)$%';
$netcom=WNetcom::get();
$returned=$netcom->send( WPref::load('PAPPS_NODE_REQUEST'), 'site','validateSite',$data, false); 
$message=WMessage::get();
if($returned){
$message->userS('1251770410PASS');
}else{
$message->userE('1251770410PAST');
}
}else{
$message=WMessage::get();
$message->userE('1401297229KKPC');
}
$extensionHelperC=WCache::get();
$extensionHelperC->resetCache();
}else{
$message=WMessage::get();
$message->userE('1206732397TAXO');
}
WPages::redirect('controller=apps');
return true;
}}